import { ref, onMounted, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { getSoundPreference, saveSoundPreference, playOrderChime } from '@/Utils/mockFoods';

export function useMitraPesanan(props) {
    const showScanModal = ref(false);
    const orders = ref([...props.orders]);
    const activeTab = ref('semua'); // 'semua', 'menunggu', 'selesai', 'dibatalkan'
    const searchQuery = ref('');
    const notification = ref('');
    const isSoundEnabled = ref(true);
    const incomingOrder = ref(null);
    const knownOrderIds = ref(new Set());
    let bannerTimeout = null;

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

    // Simulasi pesanan masuk untuk demo di halaman pesanan
    const simulateIncomingOrder = () => {
        const simId = 'ORD-' + Math.floor(100 + Math.random() * 900);
        const mockStudents = ['Kevin Pratama (Mhs. FT)', 'Dewi Anggraini (Mhs. FMIPA)', 'Bagas Wicaksono (Mhs. FISH)'];
        const mockFoodTitles = ['Nasi Ayam Bakar Bumbu Rujak Spesial', 'Nasi Campur Bali Ayam Suwir', 'Sop Ayam & Sayur Segar'];
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

    onMounted(() => {
        knownOrderIds.value = new Set(orders.value.map(o => String(o.id)));
        isSoundEnabled.value = getSoundPreference();
    });

    const sortDueAsc = ref(true);
    const selectedOrderModal = ref(null);

    const toggleSortDue = () => {
        sortDueAsc.value = !sortDueAsc.value;
    };

    const cancelManual = (id) => {
        if (confirm(`Batalkan pesanan #${id}? Status akan diubah menjadi dibatalkan.`)) {
            router.patch(route('mitra.pesanan.status', id), {
                status: 'batal'
            }, {
                preserveScroll: true,
                onSuccess: () => {
                    const found = orders.value.find(o => String(o.id) === String(id) || o.order_number === id);
                    if (found) found.status = 'batal';
                    notification.value = `Pesanan #${id} telah dibatalkan.`;
                    setTimeout(() => notification.value = '', 3500);
                }
            });
        }
    };

    const validateManual = (id) => {
        router.patch(route('mitra.pesanan.status', id), {
            status: 'selesai'
        }, {
            preserveScroll: true,
            onSuccess: () => {
                const found = orders.value.find(o => String(o.id) === String(id) || o.order_number === id);
                if (found) found.status = 'selesai';
                notification.value = `Pesanan #${id} berhasil divalidasi dan diserahkan ke mahasiswa!`;
                setTimeout(() => notification.value = '', 3500);
            }
        });
    };

    const handleValidated = (order) => {
        router.reload({ preserveScroll: true });
    };

    // Metrik Ringkasan Kasir
    const totalOrdersCount = computed(() => orders.value.length);
    const pendingCount = computed(() => orders.value.filter(o => o.status === 'menunggu' || o.status === 'siap_ambil').length);
    const completedCount = computed(() => orders.value.filter(o => o.status === 'selesai').length);
    const ecoBawaSendiriCount = computed(() => orders.value.filter(o => (o.ecoPackaging || o.eco_packaging) === 'bawa_sendiri').length);
    const totalCashierRevenue = computed(() => {
        const sum = orders.value
            .filter(o => o.status === 'selesai')
            .reduce((acc, curr) => acc + (curr.total || curr.total_price || 0), 0);
        return sum > 0 ? sum : (props.merchant?.balance_rp || 216000);
    });

    const filteredOrders = computed(() => {
        let result = orders.value.filter(o => {
            if (activeTab.value !== 'semua' && o.status !== activeTab.value) return false;
            if (searchQuery.value) {
                const q = searchQuery.value.toLowerCase();
                const matchId = String(o.id).toLowerCase().includes(q);
                const matchName = o.customerName?.toLowerCase().includes(q);
                const matchMenu = o.food?.name?.toLowerCase().includes(q) || o.food?.shortName?.toLowerCase().includes(q);
                if (!matchId && !matchName && !matchMenu) return false;
            }
            return true;
        });

        return [...result].sort((a, b) => {
            const valA = a.pickupDeadline || a.pickupTime || '';
            const valB = b.pickupDeadline || b.pickupTime || '';
            return sortDueAsc.value ? valA.localeCompare(valB) : valB.localeCompare(valA);
        });
    });

    const printRekap = () => {
        window.print();
    };

    const copiedOrderId = ref('');
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

    const exportCsv = () => {
        const headers = ['Order ID,Nama Mahasiswa,NIM,Menu,Porsi,Kemasan,Total,Status,Batas Ambil\n'];
        const rows = orders.value.map(o => 
            `"${o.id}","${o.customerName}","${o.nim}","${o.food?.shortName}",${o.quantity},"${o.ecoPackaging === 'bawa_sendiri' ? 'Wadah Sendiri' : 'Kotak Kertas'}",${o.total},"${o.statusLabel}","${o.pickupTime}"`
        );
        const blob = new Blob([headers.concat(rows.join('\n'))], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `rekap-pesanan-plusfood-${new Date().toISOString().slice(0,10)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    return {
        showScanModal,
        orders,
        activeTab,
        searchQuery,
        notification,
        isSoundEnabled,
        incomingOrder,
        knownOrderIds,
        reloadOrders,
        toggleSound,
        triggerIncomingNotification,
        simulateIncomingOrder,
        sortDueAsc,
        selectedOrderModal,
        toggleSortDue,
        cancelManual,
        validateManual,
        handleValidated,
        totalOrdersCount,
        pendingCount,
        completedCount,
        ecoBawaSendiriCount,
        totalCashierRevenue,
        filteredOrders,
        printRekap,
        copiedOrderId,
        copyOrderId,
        getAsignColor,
        getInitials,
        exportCsv,
    };
}
