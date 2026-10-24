import { ref, onMounted, onUnmounted, computed, watch } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { useAuth } from '@/Composables/useAuth';
import { getSavedOrders, syncLocalOrdersWithDb, addOrderReview, updateOrderStatus, mapDbOrder } from '@/Utils/mockFoods';

export function useRiwayat(props) {
    const auth = useAuth();
    const page = usePage();

    const activeTab = ref('menunggu'); // 'menunggu', 'selesai', 'dibatalkan', 'semua'
    const showQrModal = ref(false);
    const showReviewModal = ref(false);
    const activeTicket = ref(null);
    const activeTicketOrder = ref(null);
    const activeReviewOrder = ref(null);
    const reviewRating = ref(5);
    const selectedReviewTags = ref(['Porsi Pas & Kenyang', 'Masih Hangat']);
    const reviewComment = ref('');
    const reviewRewardToast = ref(false);
    const orders = ref([]);

    const quickReviewTags = [
        'Porsi Pas & Kenyang',
        'Masih Hangat',
        'Rasa Mantap',
        'Kemasan Rapi',
        'Pelayanan Ramah',
        'Harga Bersahabat'
    ];

    const reloadOrders = () => {
        if (typeof window !== 'undefined' && localStorage.getItem('plusfood_orders_cleared') === 'true') {
            orders.value = [];
            activeTicketOrder.value = null;
            return;
        }
        const currentUserId = auth.user.value?.id;
        const currentUserEmail = auth.user.value?.email;
        const dbSource = (props.dbOrders && props.dbOrders.length > 0) ? props.dbOrders : (page.props?.allMitraOrders || []);
        const local = syncLocalOrdersWithDb(dbSource, currentUserId, currentUserEmail);
        const dbMapped = dbSource.map(mapDbOrder).filter(Boolean);

        const cleanCode = (val) => {
            if (!val) return '';
            const match = String(val).match(/\d{4,6}/);
            return match ? match[0] : String(val).toLowerCase().replace(/[^a-z0-9]/g, '').replace(/^pf/, '');
        };

        // Gabungkan list pesanan database MySQL dan LocalStorage tanpa duplikasi
        const combined = [...dbMapped];
        local.forEach(loc => {
            if (currentUserId) {
                const locUserId = loc.user_id || loc.userId;
                const locEmail = loc.customer_email || loc.email || loc.userEmail;
                if (locUserId && Number(locUserId) !== Number(currentUserId)) return;
                if (locEmail && currentUserEmail && String(locEmail).trim().toLowerCase() !== String(currentUserEmail).trim().toLowerCase()) return;
            }

            const lClean = cleanCode(loc.order_number || loc.id || loc.manualCode);
            const existingIdx = combined.findIndex(c => {
                const cClean = cleanCode(c.order_number || c.id || c.manualCode);
                return lClean && cClean && lClean === cClean;
            });

            if (existingIdx >= 0) {
                // Pastikan jika salah satu sudah 'selesai', keduanya sinkron selesai
                if (combined[existingIdx].status === 'selesai' || loc.status === 'selesai') {
                    combined[existingIdx].status = 'selesai';
                    combined[existingIdx].statusLabel = 'Selesai Diambil';
                }
            } else {
                combined.push(loc);
            }
        });

        orders.value = combined;

        // Jika tidak ada pesanan menunggu dan ada pesanan selesai, default aktifkan tab selesai
        const pendingCount = orders.value.filter(o => o.status === 'menunggu').length;
        if (pendingCount === 0 && orders.value.some(o => o.status === 'selesai') && activeTab.value === 'menunggu') {
            activeTab.value = 'selesai';
        }

        // Auto-buka tiket jika ada parameter URL atau prop initialTicketId
        const urlParams = typeof window !== 'undefined' ? new URLSearchParams(window.location.search) : null;
        const ticketId = props.initialTicketId || (urlParams ? urlParams.get('tiket') : null);
        if (ticketId) {
            const found = orders.value.find(o => o.id === ticketId || o.qrCode === ticketId || o.manualCode === ticketId);
            if (found) {
                activeTicketOrder.value = found;
            } else if (orders.value.length > 0) {
                activeTicketOrder.value = orders.value[0];
            }
        }
    };

    watch(() => page.props?.allMitraOrders, () => {
        reloadOrders();
    });

    const draftCheckout = ref(null);
    const loadDraftCheckout = () => {
        if (typeof window === 'undefined') return;
        try {
            const raw = localStorage.getItem('plusfood_draft_checkout');
            if (raw) {
                const data = JSON.parse(raw);
                const currentUserId = auth.user.value?.id;
                const currentUserEmail = auth.user.value?.email;
                if (currentUserId && data.userId && Number(data.userId) !== Number(currentUserId)) {
                    draftCheckout.value = null;
                    return;
                }
                if (currentUserEmail && data.userEmail && String(data.userEmail).trim().toLowerCase() !== String(currentUserEmail).trim().toLowerCase()) {
                    draftCheckout.value = null;
                    return;
                }
                if (Date.now() - (data.timestamp || 0) < 2 * 60 * 60 * 1000) {
                    draftCheckout.value = data;
                } else {
                    localStorage.removeItem('plusfood_draft_checkout');
                    draftCheckout.value = null;
                }
            } else {
                draftCheckout.value = null;
            }
        } catch (e) {
            draftCheckout.value = null;
        }
    };

    watch(() => auth.user.value?.id, () => {
        reloadOrders();
        loadDraftCheckout();
    });

    const discardDraft = () => {
        if (typeof window !== 'undefined') {
            localStorage.removeItem('plusfood_draft_checkout');
        }
        draftCheckout.value = null;
    };

    const getMerchantName = (food) => {
        if (!food) return 'Mitra PlusFood';
        if (typeof food.merchant === 'string') return food.merchant;
        if (typeof food.merchant === 'object' && food.merchant?.name) return food.merchant.name;
        if (food.merchantName && typeof food.merchantName === 'string') return food.merchantName;
        return 'Mitra PlusFood';
    };

    const clearAllOrders = () => {
        if (typeof window !== 'undefined') {
            localStorage.setItem('plusfood_orders_cleared', 'true');
            localStorage.removeItem('plusfood_orders');
            localStorage.removeItem('plusfood_draft_checkout');
            window.dispatchEvent(new CustomEvent('plusfood_order_status_updated'));
        }
        orders.value = [];
        activeTicketOrder.value = null;
        draftCheckout.value = null;

        router.post(route('riwayat.clear'), {}, {
            preserveScroll: true,
            onSuccess: () => {
                orders.value = [];
                activeTicketOrder.value = null;
            }
        });
    };

    let storageHandler = null;
    let statusHandler = null;

    onMounted(() => {
        reloadOrders();
        loadDraftCheckout();
        if (typeof window !== 'undefined') {
            storageHandler = (e) => {
                if (e.key === 'plusfood_orders') {
                    reloadOrders();
                }
                if (e.key === 'plusfood_draft_checkout') {
                    loadDraftCheckout();
                }
            };
            statusHandler = () => reloadOrders();
            window.addEventListener('storage', storageHandler);
            window.addEventListener('plusfood_order_status_updated', statusHandler);
        }
    });

    onUnmounted(() => {
        if (typeof window !== 'undefined') {
            if (storageHandler) window.removeEventListener('storage', storageHandler);
            if (statusHandler) window.removeEventListener('plusfood_order_status_updated', statusHandler);
        }
    });

    const openQr = (order) => {
        activeTicketOrder.value = order;
        if (typeof window !== 'undefined') {
            const url = new URL(window.location);
            url.searchParams.set('tiket', order.id);
            window.history.pushState({}, '', url);
        }
    };

    const handleBackFromTicket = () => {
        activeTicketOrder.value = null;
        if (typeof window !== 'undefined') {
            const url = new URL(window.location);
            url.searchParams.delete('tiket');
            window.history.pushState({}, '', url.pathname);
        }
    };

    const cancelModalOrder = ref(null);
    const showCancelModal = ref(false);
    const showExpiredNotice = ref(false);
    const cancelReasons = [
        'Salah pilih menu atau jumlah porsi',
        'Jadwal kuliah / ada agenda mendadak',
        'Waktu / jarak penjemputan tidak memungkinkan',
        'Sudah makan di tempat lain',
        'Lainnya',
    ];
    const selectedCancelReason = ref('Salah pilih menu atau jumlah porsi');
    const customCancelReason = ref('');
    const isSubmittingCancel = ref(false);

    const isOrderWithin15Mins = (order) => {
        if (!order) return false;
        const raw = order.created_at || order.createdAt;
        if (!raw) return true;
        let s = String(raw).trim();
        if (!s.endsWith('Z') && !s.includes('+') && s.includes(' ')) {
            s = s.replace(' ', 'T') + 'Z';
        }
        const t = new Date(s).getTime();
        if (isNaN(t)) return true;
        return (Date.now() - t) / (1000 * 60) <= 15;
    };

    const getCancelMinutesRemaining = (order) => {
        if (!order) return 0;
        const raw = order.created_at || order.createdAt;
        if (!raw) return 15;
        let s = String(raw).trim();
        if (!s.endsWith('Z') && !s.includes('+') && s.includes(' ')) {
            s = s.replace(' ', 'T') + 'Z';
        }
        const t = new Date(s).getTime();
        if (isNaN(t)) return 15;
        const diff = (Date.now() - t) / (1000 * 60);
        return Math.max(0, Math.ceil(15 - diff));
    };

    const openCancelFromList = (order) => {
        if (!isOrderWithin15Mins(order)) {
            showExpiredNotice.value = true;
            return;
        }
        cancelModalOrder.value = order;
        selectedCancelReason.value = 'Salah pilih menu atau jumlah porsi';
        customCancelReason.value = '';
        showCancelModal.value = true;
    };

    const handleConfirmCancelFromList = () => {
        if (!cancelModalOrder.value) return;
        const finalReason = selectedCancelReason.value === 'Lainnya' 
            ? (customCancelReason.value.trim() || 'Alasan lainnya') 
            : selectedCancelReason.value;
        
        isSubmittingCancel.value = true;
        handleCancelOrder({
            order: cancelModalOrder.value,
            reason: finalReason
        });
    };

    const handleCancelOrder = (payload) => {
        const order = payload?.order || payload;
        const reason = payload?.reason || 'Dibatalkan oleh mahasiswa';
        if (!order) return;

        const targetId = order.id || order.order_number || order.manualCode;

        // 1. Optimistic update di local storage
        updateOrderStatus(targetId, 'dibatalkan');
        handleBackFromTicket();
        activeTab.value = 'dibatalkan';

        // 2. Kirim POST request pembatalan ke backend Laravel
        router.post(route('riwayat.batal', targetId), {
            reason: reason
        }, {
            preserveScroll: true,
            onSuccess: () => {
                isSubmittingCancel.value = false;
                showCancelModal.value = false;
                reloadOrders();
                activeTab.value = 'dibatalkan';
            },
            onError: () => {
                isSubmittingCancel.value = false;
                showCancelModal.value = false;
                reloadOrders();
            }
        });
    };

    const openReviewModal = (order) => {
        activeReviewOrder.value = order;
        reviewRating.value = 5;
        selectedReviewTags.value = ['Porsi Pas & Kenyang', 'Masih Hangat'];
        reviewComment.value = '';
        showReviewModal.value = true;
    };

    const toggleReviewTag = (tag) => {
        const idx = selectedReviewTags.value.indexOf(tag);
        if (idx >= 0) {
            selectedReviewTags.value.splice(idx, 1);
        } else {
            selectedReviewTags.value.push(tag);
        }
    };

    const submitReview = () => {
        if (!activeReviewOrder.value) return;
        const targetOrder = activeReviewOrder.value;
        const reviewData = {
            rating: reviewRating.value,
            tags: [...selectedReviewTags.value],
            comment: reviewComment.value
        };

        // 1. Simpan ke LocalStorage agar langsung responsif
        addOrderReview(targetOrder.id, reviewData);
        showReviewModal.value = false;
        reloadOrders();
        reviewRewardToast.value = true;

        // 2. Kirim ke backend Laravel MySQL untuk update tabel reviews & merchant rating
        const orderIdentifier = targetOrder.id || targetOrder.order_number || targetOrder.manualCode;
        if (orderIdentifier) {
            router.post(route('riwayat.review', orderIdentifier), reviewData, {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    reloadOrders();
                },
                onError: () => {
                    // Tetap biarkan fallback local storage berjalan mulus
                }
            });
        }

        setTimeout(() => {
            reviewRewardToast.value = false;
        }, 4500);
    };

    // Kalkulasi ringkasan dampak 1 semester sesuai dokumen desain
    const totalRescuedPortions = computed(() => {
        return orders.value
            .filter(o => o.status === 'selesai')
            .reduce((acc, curr) => acc + (curr.quantity || 1), 14); // default 14 plus completed
    });

    const totalMoneySaved = computed(() => {
        return totalRescuedPortions.value * 12000;
    });

    const totalCo2Saved = computed(() => {
        return (totalRescuedPortions.value * 0.85).toFixed(1);
    });

    const totalGreenPoints = computed(() => {
        const reviewBonus = orders.value.filter(o => o.isReviewed).length * 15;
        return 140 + reviewBonus;
    });

    const filteredOrders = computed(() => {
        if (activeTab.value === 'semua') return orders.value;
        if (activeTab.value === 'dibatalkan') {
            return orders.value.filter(o => o.status === 'dibatalkan' || o.status === 'batal');
        }
        return orders.value.filter(o => o.status === activeTab.value);
    });

    return {
        activeTab,
        showQrModal,
        showReviewModal,
        activeTicket,
        activeTicketOrder,
        activeReviewOrder,
        reviewRating,
        selectedReviewTags,
        reviewComment,
        reviewRewardToast,
        orders,
        quickReviewTags,
        reloadOrders,
        draftCheckout,
        loadDraftCheckout,
        discardDraft,
        getMerchantName,
        clearAllOrders,
        openQr,
        handleBackFromTicket,
        cancelModalOrder,
        showCancelModal,
        showExpiredNotice,
        cancelReasons,
        selectedCancelReason,
        customCancelReason,
        isSubmittingCancel,
        isOrderWithin15Mins,
        getCancelMinutesRemaining,
        openCancelFromList,
        handleConfirmCancelFromList,
        handleCancelOrder,
        openReviewModal,
        toggleReviewTag,
        submitReview,
        totalRescuedPortions,
        totalMoneySaved,
        totalCo2Saved,
        totalGreenPoints,
        filteredOrders,
    };
}
