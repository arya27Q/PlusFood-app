import { ref, onMounted, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { getSoundPreference, saveSoundPreference, playOrderChime } from '@/Utils/mockFoods';

export function useMitraDashboard(props) {
    const showScanModal = ref(false);
    const showPayoutModal = ref(false);
    const isSoundEnabled = ref(true);
    const incomingOrder = ref(null);
    const knownOrderIds = ref(new Set());
    const totalWithdrawnToday = ref(0);
    let bannerTimeout = null;

    const orders = ref([...props.orders]);
    const recentFilter = ref('semua'); // 'semua', 'menunggu', 'selesai'

    const triggerIncomingNotification = (newOrder) => {
        if (isSoundEnabled.value) {
            playOrderChime();
        }
        incomingOrder.value = {
            id: newOrder.id,
            customerName: newOrder.customerName || newOrder.customer_name || 'Mahasiswa UNESA',
            foodName: newOrder.food?.shortName || newOrder.food?.name || 'Makanan Surplus',
            total: newOrder.total || newOrder.total_price || 0,
            ecoPackaging: (newOrder.ecoPackaging || newOrder.eco_packaging) === 'bawa_sendiri',
            date: 'Baru saja'
        };

        if (bannerTimeout) clearTimeout(bannerTimeout);
        bannerTimeout = setTimeout(() => {
            incomingOrder.value = null;
        }, 7000);
    };

    watch(() => props.orders, (newOrders) => {
        if (newOrders && newOrders.length > 0) {
            // Cek pesanan baru yang masuk dengan status 'menunggu' untuk memicu lonceng notifikasi
            const newPending = newOrders.find(o => o.status === 'menunggu' && !knownOrderIds.value.has(String(o.id)));
            if (newPending && knownOrderIds.value.size > 0) {
                triggerIncomingNotification(newPending);
            }
            orders.value = [...newOrders];
            knownOrderIds.value = new Set(newOrders.map(o => String(o.id)));
        } else if (newOrders) {
            orders.value = [];
        }
    }, { deep: true, immediate: true });

    const reloadOrders = () => {
        router.reload({ preserveScroll: true });
    };

    const toggleSound = () => {
        isSoundEnabled.value = !isSoundEnabled.value;
        saveSoundPreference(isSoundEnabled.value);
        if (isSoundEnabled.value) {
            playOrderChime();
        }
    };

    // Simulasi pesanan baru masuk untuk demo & presentasi
    const simulateIncomingOrder = () => {
        const simId = 'ORD-' + Math.floor(100 + Math.random() * 900);
        const mockStudents = ['Dimas Arya (Mhs. FBS)', 'Nabila Putri (Mhs. FEB)', 'Raka Pratama (Mhs. FT)', 'Alifia Zahra (Mhs. FIP)'];
        const mockFoodTitles = ['Nasi Ayam Bakar Bumbu Rujak Spesial', 'Nasi Campur Bali Ayam Suwir', 'Soto Ayam Lamongan Koya Gurih'];
        const randomStudent = mockStudents[Math.floor(Math.random() * mockStudents.length)];
        const randomFood = mockFoodTitles[Math.floor(Math.random() * mockFoodTitles.length)];

        const simOrder = {
            id: simId,
            order_number: simId,
            customerName: randomStudent,
            customer_name: randomStudent,
            nim: '230512' + Math.floor(10000 + Math.random() * 90000),
            whatsapp: '0812' + Math.floor(10000000 + Math.random() * 90000000),
            food: {
                name: randomFood,
                shortName: randomFood.split(' ')[0] + ' ' + randomFood.split(' ')[1],
                priceDiscount: 12000,
                priceOriginal: 25000,
                merchant: props.merchant?.name || 'Warung Bu Sari'
            },
            quantity: 1,
            ecoPackaging: 'bawa_sendiri',
            ecoPackagingLabel: 'Bawa Wadah Sendiri (Rp0)',
            total: 12000,
            total_price: 12000,
            pickupTime: '19.30 – 20.30 WIB',
            pickupDeadline: 'Hari ini tutup 21.00 WIB',
            date: 'Hari ini',
            paymentMethod: 'qris',
            paymentStatus: 'lunas',
            status: 'menunggu',
            statusLabel: 'Menunggu Pickup',
            qrCode: simId + '-BS',
            hasReview: false
        };

        orders.value = [simOrder, ...orders.value];
        knownOrderIds.value.add(String(simOrder.id));
        triggerIncomingNotification(simOrder);
    };

    const merchantId = computed(() => props.merchant?.id || 'default');
    const withdrawnStorageKey = computed(() => `plusfood_withdrawn_today_${merchantId.value}`);

    const handleWithdrawn = ({ amount }) => {
        totalWithdrawnToday.value += amount;
        if (typeof window !== 'undefined') {
            localStorage.setItem(withdrawnStorageKey.value, String(totalWithdrawnToday.value));
        }
    };

    // Metrik Finansial Sesuai Spesifikasi PRD & Desain Sistem
    const completedOrders = computed(() => orders.value.filter(o => o.status === 'selesai'));
    const pendingOrders = computed(() => orders.value.filter(o => o.status === 'menunggu' || o.status === 'siap_ambil'));

    const todayRevenue = computed(() => {
        if (props.metrics?.todayRevenue !== undefined && props.metrics?.todayRevenue !== null) {
            return Number(props.metrics.todayRevenue);
        }
        const realSum = completedOrders.value.reduce((acc, curr) => acc + (curr.total || curr.total_price || 0), 0);
        return realSum > 0 ? realSum : (Number(props.merchant?.balance_rp) || 0);
    });

    const availableBalance = computed(() => {
        return Math.max(0, todayRevenue.value - totalWithdrawnToday.value);
    });

    const rescuedPortions = computed(() => {
        if (props.metrics?.rescuedPortions !== undefined && props.metrics?.rescuedPortions !== null) {
            return Number(props.metrics.rescuedPortions);
        }
        const realQty = completedOrders.value.reduce((acc, curr) => acc + (curr.quantity || 1), 0);
        return realQty;
    });

    const totalListingPortions = computed(() => {
        if (props.metrics?.totalListingPortions !== undefined && props.metrics?.totalListingPortions !== null) {
            return Number(props.metrics.totalListingPortions);
        }
        return 0;
    });

    const recoveryRate = computed(() => {
        if (props.metrics?.recoveryRate) return props.metrics.recoveryRate;
        if (totalListingPortions.value <= 0) return 0;
        return Math.round((rescuedPortions.value / totalListingPortions.value) * 100);
    });

    onMounted(() => {
        knownOrderIds.value = new Set(orders.value.map(o => String(o.id)));
        isSoundEnabled.value = getSoundPreference();

        if (typeof window !== 'undefined') {
            localStorage.removeItem('plusfood_withdrawn_today');
            const savedWithdrawn = localStorage.getItem(withdrawnStorageKey.value);
            if (savedWithdrawn) {
                totalWithdrawnToday.value = Math.min(Number(savedWithdrawn) || 0, todayRevenue.value);
            } else {
                totalWithdrawnToday.value = 0;
            }
        }
    });

    const validateOrderManual = (orderId) => {
        router.patch(route('mitra.pesanan.status', orderId), {
            status: 'selesai'
        }, {
            preserveScroll: true,
            onSuccess: () => {
                const found = orders.value.find(o => String(o.id) === String(orderId) || o.order_number === orderId);
                if (found) found.status = 'selesai';
            }
        });
    };

    const handleValidated = (order) => {
        router.reload({ preserveScroll: true });
    };

    // State Pencarian & Filter Tabel Interaktif
    const searchQuery = ref('');
    const ecoOnlyFilter = ref(false);
    const copiedOrderId = ref('');
    const sortDueAsc = ref(true);
    const selectedOrderModal = ref(null);

    const toggleSortDue = () => {
        sortDueAsc.value = !sortDueAsc.value;
    };

    const cancelOrderManual = (orderId) => {
        if (confirm(`Batalkan pesanan #${orderId}? Status akan diubah menjadi dibatalkan.`)) {
            router.patch(route('mitra.pesanan.status', orderId), {
                status: 'batal'
            }, {
                preserveScroll: true,
                onSuccess: () => {
                    const found = orders.value.find(o => String(o.id) === String(orderId) || o.order_number === orderId);
                    if (found) found.status = 'batal';
                }
            });
        }
    };

    const copyOrderId = (id) => {
        if (typeof navigator !== 'undefined' && navigator.clipboard) {
            navigator.clipboard.writeText(id);
        }
        copiedOrderId.value = id;
        setTimeout(() => {
            if (copiedOrderId.value === id) copiedOrderId.value = '';
        }, 1800);
    };

    // Solid full vibrant colors - Konsisten warna biru untuk badge inisial mahasiswa (Yu biru)
    const getAsignColor = (name, id = '', idx = null) => {
        return 'bg-blue-600 text-white shadow-xs';
    };

    const getInitials = (name) => {
        if (!name) return 'M';
        const parts = name.trim().split(/\s+/);
        if (parts.length >= 2) {
            return (parts[0][0] + parts[1][0]).toUpperCase();
        }
        return parts[0].slice(0, 2).toUpperCase();
    };

    const filteredDashboardOrders = computed(() => {
        let result = orders.value;

        // Status filter
        if (recentFilter.value !== 'semua') {
            result = result.filter(o => o.status === recentFilter.value);
        }

        // Eco filter
        if (ecoOnlyFilter.value) {
            result = result.filter(o => o.ecoPackaging === 'bawa_sendiri');
        }

        // Search query
        const q = searchQuery.value.trim().toLowerCase();
        if (q) {
            result = result.filter(o => {
                const idMatch = String(o.order_number || o.id || '').toLowerCase().includes(q);
                const nameMatch = (o.customerName || '').toLowerCase().includes(q);
                const foodMatch = (o.food?.shortName || o.food?.name || '').toLowerCase().includes(q);
                const nimMatch = (o.nim || '').toLowerCase().includes(q);
                return idMatch || nameMatch || foodMatch || nimMatch;
            });
        }

        // Sort by Due Date / Pickup Time
        return [...result].sort((a, b) => {
            const valA = a.pickupDeadline || a.pickupTime || '';
            const valB = b.pickupDeadline || b.pickupTime || '';
            return sortDueAsc.value ? valA.localeCompare(valB) : valB.localeCompare(valA);
        });
    });

    return {
        showScanModal,
        showPayoutModal,
        isSoundEnabled,
        incomingOrder,
        knownOrderIds,
        totalWithdrawnToday,
        orders,
        recentFilter,
        reloadOrders,
        availableBalance,
        toggleSound,
        triggerIncomingNotification,
        simulateIncomingOrder,
        merchantId,
        withdrawnStorageKey,
        handleWithdrawn,
        completedOrders,
        pendingOrders,
        todayRevenue,
        rescuedPortions,
        totalListingPortions,
        recoveryRate,
        validateOrderManual,
        handleValidated,
        searchQuery,
        ecoOnlyFilter,
        copiedOrderId,
        sortDueAsc,
        selectedOrderModal,
        toggleSortDue,
        cancelOrderManual,
        copyOrderId,
        getAsignColor,
        getInitials,
        filteredDashboardOrders,
    };
}
