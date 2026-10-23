<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import MitraLayout from '@/Layouts/MitraLayout.vue';
import ScanQrModal from '@/Components/Mitra/ScanQrModal.vue';
import PayoutModal from '@/Components/Mitra/PayoutModal.vue';
import IncomingOrderBanner from '@/Components/Mitra/IncomingOrderBanner.vue';
import SalesAnalyticsChart from '@/Components/Mitra/SalesAnalyticsChart.vue';
import { getSoundPreference, saveSoundPreference, playOrderChime } from '@/Utils/mockFoods';
import { ref, onMounted, computed, watch } from 'vue';

const props = defineProps({
    orders: {
        type: Array,
        default: () => [],
    },
    metrics: {
        type: Object,
        default: () => ({}),
    },
    merchant: {
        type: Object,
        default: () => ({}),
    },
});

const showScanModal = ref(false);
const showPayoutModal = ref(false);
const isSoundEnabled = ref(true);
const incomingOrder = ref(null);
const knownOrderIds = ref(new Set());
const totalWithdrawnToday = ref(0);
let bannerTimeout = null;

const orders = ref([...props.orders]);
const recentFilter = ref('semua'); // 'semua', 'menunggu', 'selesai'

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

const availableBalance = computed(() => {
    return Math.max(0, todayRevenue.value - totalWithdrawnToday.value);
});

const toggleSound = () => {
    isSoundEnabled.value = !isSoundEnabled.value;
    saveSoundPreference(isSoundEnabled.value);
    if (isSoundEnabled.value) {
        playOrderChime();
    }
};

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

const handleWithdrawn = ({ amount }) => {
    totalWithdrawnToday.value += amount;
    if (typeof window !== 'undefined') {
        localStorage.setItem('plusfood_withdrawn_today', String(totalWithdrawnToday.value));
    }
};

onMounted(() => {
    knownOrderIds.value = new Set(orders.value.map(o => String(o.id)));
    isSoundEnabled.value = getSoundPreference();

    if (typeof window !== 'undefined') {
        const savedWithdrawn = localStorage.getItem('plusfood_withdrawn_today');
        if (savedWithdrawn) {
            totalWithdrawnToday.value = Number(savedWithdrawn) || 0;
        }
    }
});

// Metrik Finansial Sesuai Spesifikasi PRD & Desain Sistem
const completedOrders = computed(() => orders.value.filter(o => o.status === 'selesai'));
const pendingOrders = computed(() => orders.value.filter(o => o.status === 'menunggu' || o.status === 'siap_ambil'));

const todayRevenue = computed(() => {
    if (props.metrics?.todayRevenue && props.metrics.todayRevenue > 0) {
        return props.metrics.todayRevenue;
    }
    const realSum = completedOrders.value.reduce((acc, curr) => acc + (curr.total || curr.total_price || 0), 0);
    return realSum > 0 ? realSum : (props.merchant?.balance_rp || 216000);
});

const rescuedPortions = computed(() => {
    if (props.metrics?.rescuedPortions && props.metrics.rescuedPortions > 0) {
        return props.metrics.rescuedPortions;
    }
    const realQty = completedOrders.value.reduce((acc, curr) => acc + (curr.quantity || 1), 0);
    return realQty > 0 ? realQty : 18;
});

const totalListingPortions = computed(() => {
    return props.metrics?.totalListingPortions || 24;
});

const recoveryRate = computed(() => {
    if (props.metrics?.recoveryRate) return props.metrics.recoveryRate;
    return Math.round((rescuedPortions.value / totalListingPortions.value) * 100);
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

// Solid full vibrant colors - Distinct per profile, no green/red clash with action buttons
const getAsignColor = (name, id = '', idx = null) => {
    const list = [
        'bg-purple-600 text-white shadow-xs',
        'bg-blue-600 text-white shadow-xs',
        'bg-amber-500 text-white shadow-xs',
        'bg-teal-600 text-white shadow-xs',
        'bg-indigo-600 text-white shadow-xs',
        'bg-sky-600 text-white shadow-xs',
        'bg-orange-500 text-white shadow-xs',
        'bg-fuchsia-600 text-white shadow-xs',
        'bg-cyan-600 text-white shadow-xs',
        'bg-violet-600 text-white shadow-xs',
    ];
    if (idx !== undefined && idx !== null && typeof idx === 'number') {
        return list[Math.abs(idx) % list.length];
    }
    const key = (name || '') + '-' + (id || '');
    let hash = 7;
    for (let i = 0; i < key.length; i++) {
        hash = (hash * 31 + key.charCodeAt(i)) | 0;
    }
    return list[Math.abs(hash) % list.length];
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
            const idMatch = (o.id || '').toLowerCase().includes(q);
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
</script>

<template>
    <MitraLayout>
        <Head title="Dashboard Kasir Mitra - PlusFood" />

        <div class="space-y-7">
            
            <!-- ================= HEADER UTAMA DASHBOARD MITRA ================= -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                        <span>Rabu malam, 19.35 WIB</span>
                        <span>•</span>
                        <span class="text-brand-maroon font-bold">Sesi Flash Sale Malam Aktif</span>
                    </div>
                    <h1 class="text-3xl font-black text-slate-900 mt-1">Dashboard Kasir & Pemulihan Modal</h1>
                    <p class="text-xs sm:text-sm text-slate-600 mt-0.5">
                        Pantau kedatangan mahasiswa, terima pembayaran fisik di toko, dan kurangi kerugian makanan sisa.
                    </p>
                </div>

                <!-- Action CTA Header -->
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <!-- Toggle Suara Notifikasi (Fitur 3) -->
                    <button
                        @click="toggleSound"
                        class="inline-flex items-center gap-1.5 sm:gap-2 rounded-xl px-3.5 py-2.5 sm:py-3 text-xs font-bold transition shadow-xs cursor-pointer border"
                        :class="isSoundEnabled ? 'bg-emerald-50 text-emerald-800 border-emerald-300 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border-slate-300 hover:bg-slate-200'"
                        :title="isSoundEnabled ? 'Klik untuk matikan suara lonceng' : 'Klik untuk nyalakan suara lonceng'"
                    >
                        <span class="text-sm leading-none">{{ isSoundEnabled ? '🔊' : '🔇' }}</span>
                        <span>Suara Notifikasi: {{ isSoundEnabled ? 'Aktif' : 'Nonaktif' }}</span>
                    </button>

                    <!-- Tombol Simulasi Pesanan Masuk (Demo Presentasi) -->
                    <button
                        @click="simulateIncomingOrder"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 text-amber-300 hover:bg-slate-800 px-3.5 py-2.5 sm:py-3 text-xs font-black shadow-sm transition hover:scale-105 active:scale-95 cursor-pointer border border-amber-400/40"
                        title="Simulasi Pesanan Masuk untuk Demo Presentasi"
                    >
                        <span>🔔 Tes Pesanan Masuk</span>
                    </button>

                    <button
                        @click="showScanModal = true"
                        class="inline-flex items-center gap-2 rounded-xl bg-amber-400 px-4 sm:px-5 py-2.5 sm:py-3 text-xs font-black text-stone-900 shadow-md hover:bg-amber-300 transition hover:scale-105 cursor-pointer"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        <span class="hidden sm:inline">Buka Pemindai QR</span>
                        <span class="sm:hidden">Scan QR</span>
                    </button>

                    <Link
                        :href="route('mitra.kelola-menu')"
                        class="inline-flex items-center gap-2 rounded-xl bg-brand-maroon px-4 sm:px-5 py-2.5 sm:py-3 text-xs font-bold text-white shadow-md hover:bg-brand-maroon-hover transition"
                    >
                        <span>+ Tambah Surplus Kilat</span>
                    </Link>
                </div>
            </div>

            <!-- ================= KPI METRIC CARDS (GRID 3 KOLOM SESUAI SPEC) ================= -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                
                <!-- KPI 1: Pendapatan Hari Ini & Payout Saldo Dompet -->
                <div class="rounded-[26px] bg-white p-6 border border-slate-200/90 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.07)] transition-all duration-200 space-y-3.5 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Pendapatan Hari Ini
                            </span>
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-700 border border-amber-200 shadow-2xs">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-3xl font-black text-brand-maroon tracking-tight">
                                Rp{{ todayRevenue.toLocaleString('id-ID') }}
                            </p>
                            <div class="flex items-center justify-between text-xs mt-1">
                                <p class="text-emerald-700 font-bold flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Tersedia: Rp{{ availableBalance.toLocaleString('id-ID') }}</span>
                                </p>
                                <span v-if="totalWithdrawnToday > 0" class="text-[10px] text-slate-400 font-semibold">
                                    Ditarik: Rp{{ totalWithdrawnToday.toLocaleString('id-ID') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Tarik Saldo / Pencairan Dana (Fitur 2) -->
                    <div class="pt-2 border-t border-slate-100">
                        <button
                            @click="showPayoutModal = true"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-brand-maroon via-[#7B0D2C] to-[#8E1032] px-4 py-2.5 text-xs font-black text-white shadow-sm hover:opacity-95 hover:shadow-md transition active:scale-98 cursor-pointer group"
                        >
                            <svg class="h-4 w-4 text-amber-300 transition-transform group-hover:rotate-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Tarik Saldo / Pencairan Dana</span>
                        </button>
                    </div>
                </div>

                <!-- KPI 2: Porsi Terselamatkan -->
                <div class="rounded-[26px] bg-white p-6 border border-slate-200/90 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.07)] transition-all duration-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Porsi Terselamatkan
                        </span>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-baseline justify-between">
                            <p class="text-3xl font-black text-slate-900 tracking-tight">
                                {{ rescuedPortions }} <span class="text-base font-bold text-slate-400">/ {{ totalListingPortions }} Porsi</span>
                            </p>
                            <span class="text-xs font-black text-brand-maroon bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200/70">{{ recoveryRate }}%</span>
                        </div>
                        <div class="mt-2.5 h-2.5 w-full rounded-full bg-slate-100 border border-slate-200/80 overflow-hidden">
                            <div
                                class="h-full rounded-full bg-gradient-to-r from-amber-400 via-emerald-400 to-emerald-600 transition-all duration-700"
                                :style="{ width: `${recoveryRate}%` }"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- KPI 3: Break-Even Tracker / Pemulihan Modal -->
                <div class="rounded-[26px] bg-white p-6 border border-slate-200/90 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.07)] transition-all duration-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Break-Even Tracker
                        </span>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-sky-50 text-sky-700 border border-sky-200 shadow-2xs">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-3xl font-black text-emerald-700 tracking-tight">
                            {{ recoveryRate }}% Modal Pulih
                        </p>
                        <p class="text-xs text-slate-500 font-medium mt-1">
                            Mencegah potensi kerugian <strong class="text-slate-800 font-bold">Rp145.000</strong> masakan sisa.
                        </p>
                    </div>
                </div>

            </div>

            <!-- ================= GRAFIK ANALITIK & PEMULIHAN MODAL (FRONTEND INTERAKTIF) ================= -->
            <SalesAnalyticsChart />

            <!-- ================= STATUS SESI REAL-TIME BANNER ================= -->
            <div class="rounded-2xl bg-gradient-to-r from-amber-500/12 via-amber-50/40 to-white p-5 border border-amber-300 shadow-[0_4px_24px_-4px_rgba(245,158,11,0.10)] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-maroon via-rose-900 to-rose-950 text-white shadow-sm">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <circle cx="12" cy="12" r="9" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-black text-slate-900 tracking-tight">
                            Sesi Ambil Berjalan: 18.00 s/d 21.00 WIB
                        </p>
                        <p class="text-xs text-slate-600 mt-0.5">
                            Tersisa <strong class="text-rose-600 font-extrabold">42 menit</strong> sebelum batas penutupan warung otomatis.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <span class="rounded-xl bg-amber-100 text-amber-950 border border-amber-300 px-3.5 py-1.5 text-xs font-black shadow-2xs">
                        {{ pendingOrders.length }} Pesanan Belum Diambil
                    </span>
                    <button
                        @click="showScanModal = true"
                        class="rounded-xl bg-brand-maroon text-white px-4 py-2 text-xs font-bold hover:bg-brand-maroon-hover shadow-sm transition active:scale-95"
                    >
                        Scan Tiket Tiba
                    </button>
                </div>
            </div>

            <!-- ================= TABEL PESANAN MASUK (LIVE FEED) ================= -->
            <div class="rounded-[28px] bg-white border border-slate-200/90 shadow-[0_4px_24px_-4px_rgba(0,0,0,0.04)] overflow-hidden">
                
                <!-- Table Header & Toolbar -->
                <div class="p-6 border-b border-slate-200/80 bg-gradient-to-b from-white to-slate-50/50 space-y-4">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <!-- Live Pulsing Radar Beacon -->
                            <div class="relative flex h-3.5 w-3.5 shrink-0">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500 shadow-xs"></span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">Antrean Pesanan Masuk (Live Feed)</h2>
                                    <span class="rounded-full bg-emerald-50 text-emerald-800 text-[10px] font-extrabold px-2.5 py-0.5 border border-emerald-300">
                                        Real-time Sync
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Validasi tiket QR mahasiswa saat tiba di stand atau verifikasi langsung sebelum batas waktu.
                                </p>
                            </div>
                        </div>

                        <!-- Right Actions: Quick Scanner Trigger & Refresh -->
                        <div class="flex items-center gap-2 self-start lg:self-auto">
                            <button
                                @click="showScanModal = true"
                                class="inline-flex items-center gap-2 rounded-xl bg-amber-400 px-3.5 py-2 text-xs font-black text-stone-900 shadow-2xs hover:bg-amber-300 transition hover:scale-105 active:scale-95"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                                <span>Scan Kamera</span>
                            </button>
                            <button
                                @click="reloadOrders"
                                title="Segarkan data antrean"
                                class="p-2 rounded-xl border border-slate-200/90 bg-white text-slate-600 hover:text-brand-maroon hover:border-brand-maroon transition shadow-2xs"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Toolbar Filters & Search -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
                        <!-- Filter Tabs Pills -->
                        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 scrollbar-none">
                            <button
                                @click="recentFilter = 'semua'"
                                class="rounded-xl px-3 py-1.5 text-xs font-bold transition-all flex items-center gap-1.5 whitespace-nowrap"
                                :class="recentFilter === 'semua' ? 'bg-brand-maroon text-white shadow-xs font-black' : 'bg-white text-slate-600 border border-slate-200/90 hover:bg-slate-50'"
                            >
                                <span>Semua</span>
                                <span class="rounded-full px-1.5 py-0.2 text-[10px]" :class="recentFilter === 'semua' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600 font-semibold'">
                                    {{ orders.length }}
                                </span>
                            </button>
                            <button
                                @click="recentFilter = 'menunggu'"
                                class="rounded-xl px-3 py-1.5 text-xs font-bold transition-all flex items-center gap-1.5 whitespace-nowrap"
                                :class="recentFilter === 'menunggu' ? 'bg-brand-maroon text-white shadow-xs font-black' : 'bg-white text-slate-600 border border-slate-200/90 hover:bg-slate-50'"
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                                <span>Menunggu Pickup</span>
                                <span class="rounded-full px-1.5 py-0.2 text-[10px]" :class="recentFilter === 'menunggu' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-900 font-bold'">
                                    {{ pendingOrders.length }}
                                </span>
                            </button>
                            <button
                                @click="recentFilter = 'selesai'"
                                class="rounded-xl px-3 py-1.5 text-xs font-bold transition-all flex items-center gap-1.5 whitespace-nowrap"
                                :class="recentFilter === 'selesai' ? 'bg-brand-maroon text-white shadow-xs font-black' : 'bg-white text-slate-600 border border-slate-200/90 hover:bg-slate-50'"
                            >
                                <span>Selesai</span>
                                <span class="rounded-full px-1.5 py-0.2 text-[10px]" :class="recentFilter === 'selesai' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-900 font-bold'">
                                    {{ completedOrders.length }}
                                </span>
                            </button>
                            <button
                                @click="ecoOnlyFilter = !ecoOnlyFilter"
                                class="rounded-xl px-3 py-1.5 text-xs font-bold transition-all flex items-center gap-1.5 border whitespace-nowrap ml-1"
                                :class="ecoOnlyFilter ? 'bg-emerald-700 text-white border-emerald-700 shadow-2xs font-black' : 'bg-white text-emerald-800 border-emerald-300 hover:bg-emerald-50'"
                            >
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.5 2 2 6.5 2 12c0 3.5 1.8 6.6 4.5 8.4C6.2 17 8 13.5 12 11c4-2.5 7.5-2 9.5-.5C21.8 8.5 17.5 2 12 2z" />
                                </svg>
                                <span>Wadah Sendiri</span>
                            </button>
                        </div>

                        <!-- Search Input Bar -->
                        <div class="relative w-full sm:w-72">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input
                                type="text"
                                v-model="searchQuery"
                                placeholder="Cari ID tiket, mahasiswa, menu..."
                                class="w-full rounded-xl border border-slate-200/90 bg-white py-1.5 pl-9 pr-8 text-xs font-medium text-slate-900 placeholder-slate-400 focus:border-brand-maroon focus:outline-none transition shadow-2xs"
                            />
                            <button
                                v-if="searchQuery"
                                @click="searchQuery = ''"
                                class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400 hover:text-slate-700"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto scrollbar-thin">
                    <table class="min-w-[1100px] w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 text-xs font-semibold border-b border-slate-200/80">
                            <tr>
                                <th class="py-3 px-4 w-14 text-center">Asign</th>
                                <th class="py-3 px-4 w-24">Number</th>
                                <th class="py-3 px-4 min-w-[170px]">Name</th>
                                <th class="py-3 px-4 min-w-[170px] cursor-pointer select-none group" @click="toggleSortDue" title="Klik untuk mengurutkan jadwal">
                                    <span class="inline-flex items-center gap-1 group-hover:text-slate-800 transition whitespace-nowrap">
                                        Due Date
                                        <svg class="h-3.5 w-3.5 text-slate-400 group-hover:text-slate-700 transition" :class="{'rotate-180': !sortDueAsc}" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </th>
                                <th class="py-3 px-4 min-w-[180px]">Size</th>
                                <th class="py-3 px-4 min-w-[180px]">Comments</th>
                                <th class="py-3 px-4 min-w-[140px]">Client</th>
                                <th class="py-3 px-4 text-center w-28">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <!-- Empty Filter State -->
                            <tr v-if="filteredDashboardOrders.length === 0">
                                <td colspan="8" class="py-12 text-center text-xs text-slate-500 bg-white">
                                    <div class="max-w-xs mx-auto space-y-2">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-700 border border-amber-200">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                        <p class="font-extrabold text-slate-900 text-sm">Tidak Ada Pesanan Ditemukan</p>
                                        <p class="text-[11px] text-slate-500">Coba ganti filter tab atau reset kata kunci pencarian Anda.</p>
                                        <button
                                            @click="searchQuery = ''; recentFilter = 'semua'; ecoOnlyFilter = false"
                                            class="inline-block rounded-xl bg-brand-maroon px-4 py-2 text-xs font-bold text-white shadow-2xs hover:bg-brand-maroon-hover transition cursor-pointer"
                                        >
                                            Reset Filter
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row Item (Matches Reference Screenshot Structure) -->
                            <tr
                                v-for="(order, idx) in filteredDashboardOrders"
                                :key="order.id"
                                class="hover:bg-slate-50/80 transition duration-150 group"
                            >
                                <!-- Column 1: Asign (Full Vibrant Color Initial Circular Badge) -->
                                <td class="py-4 px-4 align-middle text-center">
                                    <div
                                        class="w-9 h-9 rounded-full flex items-center justify-center font-extrabold text-[11px] shrink-0 select-none transition group-hover:scale-105 shadow-2xs mx-auto"
                                        :class="getAsignColor(order.customerName, order.id, idx)"
                                    >
                                        {{ getInitials(order.customerName) }}
                                    </div>
                                </td>

                                <!-- Column 2: Number (Clean Ticket Number with Click-to-Copy) -->
                                <td class="py-4 px-4 align-middle whitespace-nowrap">
                                    <div
                                        @click="copyOrderId(order.id)"
                                        title="Klik untuk salin ID tiket"
                                        class="inline-flex items-center gap-1.5 cursor-pointer font-bold text-slate-700 hover:text-brand-maroon transition text-xs sm:text-sm font-mono group/num"
                                    >
                                        <span class="text-slate-400 font-sans text-xs">#</span>
                                        <span>{{ (order.id || '').replace(/^PF-/, '') }}</span>
                                        <svg class="h-3.5 w-3.5 text-slate-300 group-hover/num:text-brand-maroon transition shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        <span v-if="copiedOrderId === order.id" class="text-[9px] font-sans font-bold text-emerald-600 animate-fade-in">✓</span>
                                    </div>
                                </td>

                                <!-- Column 3: Name (Two-Tier: Bold Name + NIM / WhatsApp) -->
                                <td class="py-4 px-4 align-middle whitespace-nowrap">
                                    <div class="space-y-0.5">
                                        <p class="font-bold text-slate-900 text-xs sm:text-sm leading-snug whitespace-nowrap">{{ order.customerName }}</p>
                                        <div class="flex items-center gap-1.5 text-[11px] text-slate-400 font-normal whitespace-nowrap">
                                            <span>{{ order.nim || '23051204088' }}</span>
                                            <span>·</span>
                                            <a
                                                :href="'https://wa.me/' + (order.whatsapp || '').replace(/[^0-9]/g, '')"
                                                target="_blank"
                                                class="inline-flex items-center gap-1 hover:underline text-emerald-600 font-medium transition whitespace-nowrap"
                                                title="Hubungi via WhatsApp"
                                            >
                                                <svg class="w-3 h-3 text-[#25D366] shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.694.073-2.125-.521-1.832-.759-3.003-2.617-3.095-2.738-.088-.124-.741-.987-.741-1.884 0-.898.469-1.339.636-1.522.167-.184.364-.23.486-.23.12 0 .242.001.348.006.113.005.263-.043.411.314.153.37.522 1.272.568 1.365.045.093.076.201.015.324-.061.123-.092.2-.182.308-.091.108-.192.241-.274.324-.092.093-.187.195-.081.378.107.182.474.781 1.017 1.265.7.625 1.29.818 1.472.91.182.092.289.077.396-.046.107-.123.456-.531.577-.714.122-.184.243-.153.41-.092.167.062 1.062.5 1.244.591.182.093.303.138.349.215.046.077.046.446-.098.851z"/>
                                                </svg>
                                                <span>{{ order.whatsapp || '0812-3456-7890' }}</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>

                                <!-- Column 4: Due Date ▾ (Pickup Window + Status Badge - Single Line) -->
                                <td class="py-4 px-4 align-middle whitespace-nowrap">
                                    <div class="space-y-0.5">
                                        <div class="flex items-center gap-1.5 whitespace-nowrap">
                                            <svg class="h-3.5 w-3.5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                                <circle cx="12" cy="12" r="9"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l2.5 2.5"/>
                                            </svg>
                                            <span class="font-bold text-slate-800 text-xs sm:text-sm leading-snug whitespace-nowrap">{{ order.pickupTime }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 text-[11px] font-semibold whitespace-nowrap">
                                            <span v-if="order.status === 'selesai'" class="inline-flex items-center gap-1 text-emerald-700 whitespace-nowrap">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                ✓ Selesai Diambil
                                            </span>
                                            <span v-else-if="order.status === 'dibatalkan'" class="inline-flex items-center gap-1 text-rose-600 whitespace-nowrap">
                                                <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                                ✕ Dibatalkan
                                            </span>
                                            <span v-else class="inline-flex items-center gap-1 text-amber-700 whitespace-nowrap">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                ⏳ Sesi Berjalan
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Column 5: Size (Menu Name + Portion Tag + Eco packaging - Single Line) -->
                                <td class="py-4 px-4 align-middle whitespace-nowrap">
                                    <div class="space-y-0.5">
                                        <div class="flex items-center gap-1.5 whitespace-nowrap">
                                            <span class="font-bold text-slate-900 text-xs sm:text-sm leading-snug whitespace-nowrap">{{ order.food?.shortName || order.food?.name }}</span>
                                            <span class="rounded-md bg-amber-500 text-white text-[10px] font-black px-1.5 py-0.5 shadow-2xs shrink-0">
                                                {{ order.quantity }}x
                                            </span>
                                        </div>
                                        <p class="text-[11px] font-medium flex items-center gap-1 whitespace-nowrap">
                                            <span v-if="order.ecoPackaging === 'bawa_sendiri'" class="inline-flex items-center gap-1 text-emerald-700 font-semibold whitespace-nowrap">
                                                <svg class="h-3 w-3 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.5 2 2 6.5 2 12c0 3.5 1.8 6.6 4.5 8.4C6.2 17 8 13.5 12 11c4-2.5 7.5-2 9.5-.5C21.8 8.5 17.5 2 12 2z"/>
                                                </svg>
                                                Wadah Sendiri (+10 Poin)
                                            </span>
                                            <span v-else class="inline-flex items-center gap-1 text-slate-500 whitespace-nowrap">
                                                <svg class="h-3 w-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                                Kotak Kertas
                                            </span>
                                        </p>
                                    </div>
                                </td>

                                <!-- Column 6: Comments (Mini Avatar + Truncated Note with Tooltip) -->
                                <td class="py-4 px-4 align-middle whitespace-nowrap">
                                    <div
                                        class="flex items-center gap-2 max-w-[210px] cursor-help whitespace-nowrap"
                                        :title="order.customerNote || 'Tidak ada catatan tambahan mahasiswa.'"
                                    >
                                        <div
                                            class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold shrink-0 select-none shadow-2xs"
                                            :class="getAsignColor(order.customerName, order.id, idx)"
                                        >
                                            {{ getInitials(order.customerName) }}
                                        </div>
                                        <svg class="h-3.5 w-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        <span class="text-xs text-slate-600 truncate font-normal">
                                            {{ order.customerNote || 'Tidak ada catatan tambahan...' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Column 7: Client (Payment Emblem + Bold Price + Method) -->
                                <td class="py-4 px-4 align-middle whitespace-nowrap">
                                    <div class="flex items-center gap-2.5 whitespace-nowrap">
                                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <div class="space-y-0.5">
                                            <p class="font-bold text-slate-900 text-xs sm:text-sm leading-snug whitespace-nowrap">Rp{{ order.total?.toLocaleString('id-ID') }}</p>
                                            <p class="text-[11px] text-slate-400 font-normal whitespace-nowrap">Kasir / Tunai</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Column 8: Action (Group of 3 Action Buttons [✓ Full Green] [🗑 Full Red] [...]) -->
                                <td class="py-4 px-4 align-middle text-center whitespace-nowrap">
                                    <div class="inline-flex items-center justify-center gap-2 whitespace-nowrap">
                                        <!-- Checkmark Action [✓] - Full Green Box -->
                                        <button
                                            v-if="order.status === 'menunggu'"
                                            @click="validateOrderManual(order.id)"
                                            class="h-8 w-8 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-90 text-white flex items-center justify-center transition-all shadow-sm hover:shadow cursor-pointer"
                                            title="Verifikasi & Serahkan Makanan (Full Green)"
                                        >
                                            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <span
                                            v-else-if="order.status === 'selesai'"
                                            class="h-8 w-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-xs"
                                            title="Sudah Selesai Diserahkan"
                                        >
                                            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                        <span
                                            v-else
                                            class="h-8 w-8 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center text-xs"
                                            title="Pesanan Dibatalkan"
                                        >
                                            -
                                        </span>

                                        <!-- Cancel / Trash Action [🗑] - Full Red Box -->
                                        <button
                                            v-if="order.status === 'menunggu'"
                                            @click="cancelOrderManual(order.id)"
                                            class="h-8 w-8 rounded-xl bg-rose-600 hover:bg-rose-700 active:scale-90 text-white flex items-center justify-center transition-all shadow-sm hover:shadow cursor-pointer"
                                            title="Batalkan Pesanan (Full Red)"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        <span
                                            v-else
                                            class="h-8 w-8 rounded-xl bg-slate-100 text-slate-300 flex items-center justify-center pointer-events-none"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </span>

                                        <!-- More / Detail Modal Action [...] -->
                                        <button
                                            @click="selectedOrderModal = order"
                                            class="h-8 w-8 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-90 text-slate-700 flex items-center justify-center transition-all shadow-2xs cursor-pointer"
                                            title="Lihat Detail Tiket & Catatan"
                                        >
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <circle cx="5" cy="12" r="2" />
                                                <circle cx="12" cy="12" r="2" />
                                                <circle cx="19" cy="12" r="2" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Table Summary Footer -->
                <div class="px-6 py-3.5 bg-slate-50/90 border-t border-slate-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs text-slate-500">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-900">Menampilkan {{ filteredDashboardOrders.length }} dari {{ orders.length }} pesanan</span>
                        <span>•</span>
                        <span>Sistem kasir real-time aktif</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-semibold">Sisa Menunggu: <strong class="text-brand-maroon">{{ pendingOrders.length }} Tiket</strong></span>
                        <span>•</span>
                        <button @click="reloadOrders" class="hover:text-brand-maroon font-bold underline transition cursor-pointer">Segarkan Data</button>
                    </div>
                </div>

            </div>

        </div>

        <!-- Quick Order Detail Modal (FULL SCREEN) -->
        <Teleport to="body">
            <div
                v-if="selectedOrderModal"
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/75 backdrop-blur-sm p-4 animate-fade-in"
                style="top: 0; left: 0; width: 100vw; height: 100vh;"
                @click.self="selectedOrderModal = null"
            >
            <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold" :class="getAsignColor(selectedOrderModal.customerName, selectedOrderModal.id)">
                            {{ getInitials(selectedOrderModal.customerName) }}
                        </div>
                        <div>
                            <h3 class="font-black text-slate-900 text-base leading-tight">Detail Pesanan #{{ selectedOrderModal.id }}</h3>
                            <p class="text-xs text-slate-400">{{ selectedOrderModal.date || 'Hari ini' }}</p>
                        </div>
                    </div>
                    <button @click="selectedOrderModal = null" class="p-1 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">Mahasiswa:</span>
                            <span class="font-bold text-slate-900">{{ selectedOrderModal.customerName }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">NIM:</span>
                            <span class="font-mono font-bold text-slate-800">{{ selectedOrderModal.nim || '23051204088' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">WhatsApp:</span>
                            <a :href="'https://wa.me/' + (selectedOrderModal.whatsapp || '').replace(/[^0-9]/g, '')" target="_blank" class="font-bold text-emerald-600 hover:underline">
                                {{ selectedOrderModal.whatsapp || '0812-3456-7890' }}
                            </a>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">Menu Dipesan:</span>
                            <span class="font-bold text-slate-900">{{ selectedOrderModal.food?.name }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">Jumlah Porsi:</span>
                            <span class="font-bold text-amber-700">{{ selectedOrderModal.quantity }}x Porsi</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">Wadah Kemasan:</span>
                            <span class="font-bold" :class="selectedOrderModal.ecoPackaging === 'bawa_sendiri' ? 'text-emerald-700' : 'text-slate-700'">
                                {{ selectedOrderModal.ecoPackaging === 'bawa_sendiri' ? 'Wadah Sendiri 🍃 (+10 Poin)' : 'Kotak Kertas 📦' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">Jadwal Ambil:</span>
                            <span class="font-bold text-slate-900">{{ selectedOrderModal.pickupTime }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-slate-200">
                            <span class="text-slate-700 font-bold">Total Pembayaran:</span>
                            <span class="font-black text-brand-maroon text-sm">Rp{{ selectedOrderModal.total?.toLocaleString('id-ID') }}</span>
                        </div>
                    </div>

                    <div v-if="selectedOrderModal.customerNote" class="bg-amber-50/70 p-3 rounded-2xl border border-amber-200/80">
                        <p class="font-bold text-amber-900 text-[11px] mb-1">Catatan / Alergi Mahasiswa:</p>
                        <p class="text-amber-800 italic leading-relaxed text-[11px]">"{{ selectedOrderModal.customerNote }}"</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button
                        v-if="selectedOrderModal.status === 'menunggu'"
                        @click="validateOrderManual(selectedOrderModal.id); selectedOrderModal = null"
                        class="flex-1 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 text-xs font-bold transition shadow-sm cursor-pointer"
                    >
                        ✓ Verifikasi & Serahkan
                    </button>
                    <button
                        @click="selectedOrderModal = null"
                        class="flex-1 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 py-2.5 text-xs font-bold text-slate-700 transition cursor-pointer"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
        </Teleport>

        <ScanQrModal
            v-if="showScanModal"
            :orders="orders"
            @close="showScanModal = false"
            @validated="handleValidated"
        />

        <!-- Modal Dialog Pencairan Dana Mitra (Fitur 2) -->
        <PayoutModal
            :show="showPayoutModal"
            :available-balance="availableBalance"
            :today-revenue="todayRevenue"
            @close="showPayoutModal = false"
            @withdrawn="handleWithdrawn"
        />

        <!-- Floating Banner Pesanan Baru Masuk (Fitur 3) -->
        <IncomingOrderBanner
            :order="incomingOrder"
            @close="incomingOrder = null"
        />
    </MitraLayout>
</template>
