<script setup>
import { Head } from '@inertiajs/vue3';
import MitraLayout from '@/Layouts/MitraLayout.vue';
import ScanQrModal from '@/Components/Mitra/ScanQrModal.vue';
import IncomingOrderBanner from '@/Components/Mitra/IncomingOrderBanner.vue';
import { useMitraPesanan } from '@/Composables/useMitraPesanan';

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

const {
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
} = useMitraPesanan(props);
</script>

<template>
    <MitraLayout>
        <Head title="Pesanan Masuk & Rekap Kasir - PlusFood Mitra" />

        <div class="space-y-6">
            
            <!-- Header Halaman & Ekspor -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                        <span>Gerai: Warung Bu Sari</span>
                        <span>•</span>
                        <span class="text-brand-maroon font-bold">Kantin Vokasi UNESA Ketintang</span>
                    </div>
                    <h1 class="text-3xl font-black text-slate-900 mt-0.5">Pesanan Masuk & Rekap Kasir</h1>
                    <p class="text-xs sm:text-sm text-slate-600 mt-0.5">
                        Kelola verifikasi pengambilan makanan oleh mahasiswa serta cetak pembukuan kasir.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Toggle Suara Notifikasi (Fitur 3) -->
                    <button
                        @click="toggleSound"
                        class="inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-bold transition shadow-xs cursor-pointer border"
                        :class="isSoundEnabled ? 'bg-emerald-50 text-emerald-800 border-emerald-300 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border-slate-300 hover:bg-slate-200'"
                        :title="isSoundEnabled ? 'Klik untuk mematikan suara lonceng' : 'Klik untuk mengaktifkan suara lonceng'"
                    >
                        <span class="text-sm leading-none">{{ isSoundEnabled ? '🔊' : '🔇' }}</span>
                        <span>Suara Notifikasi: {{ isSoundEnabled ? 'Aktif' : 'Nonaktif' }}</span>
                    </button>

                    <!-- Tombol Simulasi Pesanan Masuk (Demo) -->
                    <button
                        @click="simulateIncomingOrder"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 text-amber-300 hover:bg-slate-800 px-3.5 py-2 text-xs font-black shadow-sm transition hover:scale-105 active:scale-95 cursor-pointer border border-amber-400/40"
                        title="Simulasi Pesanan Masuk untuk Demo"
                    >
                        <span>🔔 Tes Pesanan</span>
                    </button>

                    <button
                        @click="printRekap"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200/90 bg-white px-3.5 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 shadow-2xs transition cursor-pointer"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span class="hidden sm:inline">Cetak Rekap Kasir</span>
                        <span class="sm:hidden">Cetak</span>
                    </button>

                    <button
                        @click="exportCsv"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-3.5 py-2 text-xs font-black text-white shadow-xs transition cursor-pointer"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span class="hidden sm:inline">Ekspor CSV</span>
                        <span class="sm:hidden">CSV</span>
                    </button>

                    <button
                        @click="showScanModal = true"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-amber-400 px-4 py-2 text-xs font-black text-stone-900 shadow-sm hover:bg-amber-300 transition cursor-pointer"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        <span>Scan QR</span>
                    </button>
                </div>
            </div>

            <!-- Toast Notifikasi Sukses -->
            <div v-if="notification" class="rounded-xl bg-emerald-700 p-3.5 text-xs font-bold text-white shadow-md animate-in fade-in flex items-center justify-between">
                <span>{{ notification }}</span>
                <button @click="notification = ''" class="text-white/80 hover:text-white p-1 rounded-md hover:bg-white/10 transition">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mini KPI Metric Row Sesuai Data Mahasiswa & Kasir -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="rounded-2xl bg-white p-4 border border-slate-200/90 shadow-2xs space-y-1">
                    <span class="text-[10px] font-black uppercase text-slate-500">Total Pesanan</span>
                    <p class="text-2xl font-black text-slate-900">{{ totalOrdersCount }} Order</p>
                    <p class="text-[10px] text-brand-maroon font-semibold">Tercatat di sistem</p>
                </div>

                <div class="rounded-2xl bg-white p-4 border border-slate-200/90 shadow-2xs space-y-1">
                    <span class="text-[10px] font-black uppercase text-amber-700">Menunggu Pickup</span>
                    <p class="text-2xl font-black text-amber-600">{{ pendingCount }} Order</p>
                    <p class="text-[10px] text-slate-500">Sesi malam berlangsung</p>
                </div>

                <div class="rounded-2xl bg-white p-4 border border-slate-200/90 shadow-2xs space-y-1">
                    <span class="text-[10px] font-black uppercase text-emerald-700">Wadah Sendiri</span>
                    <p class="text-2xl font-black text-emerald-700">{{ ecoBawaSendiriCount }} Mahasiswa</p>
                    <p class="text-[10px] text-emerald-800 font-semibold flex items-center gap-1">
                        <svg class="h-3 w-3 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Hemat biaya mika</span>
                    </p>
                </div>

                <div class="rounded-2xl bg-white p-4 border border-slate-200/90 shadow-2xs space-y-1">
                    <span class="text-[10px] font-black uppercase text-brand-maroon">Kasir Terkumpul</span>
                    <p class="text-2xl font-black text-brand-maroon">Rp{{ totalCashierRevenue.toLocaleString('id-ID') }}</p>
                    <p class="text-[10px] text-emerald-700 font-semibold">100% modal pulih</p>
                </div>
            </div>

            <!-- Filter & Search Toolbar -->
            <div class="rounded-2xl bg-white p-4 border border-slate-200/90 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                
                <!-- Filter Tabs -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                    <button
                        @click="activeTab = 'semua'"
                        class="rounded-lg px-3 py-1.5 text-xs font-bold transition whitespace-nowrap"
                        :class="activeTab === 'semua' ? 'bg-brand-maroon text-white shadow-2xs font-extrabold' : 'text-slate-600 hover:bg-slate-50'"
                    >
                        Semua ({{ orders.length }})
                    </button>
                    <button
                        @click="activeTab = 'menunggu'"
                        class="rounded-lg px-3 py-1.5 text-xs font-bold transition whitespace-nowrap"
                        :class="activeTab === 'menunggu' ? 'bg-brand-maroon text-white shadow-2xs font-extrabold' : 'text-slate-600 hover:bg-slate-50'"
                    >
                        Menunggu Pickup ({{ pendingCount }})
                    </button>
                    <button
                        @click="activeTab = 'selesai'"
                        class="rounded-lg px-3 py-1.5 text-xs font-bold transition whitespace-nowrap"
                        :class="activeTab === 'selesai' ? 'bg-brand-maroon text-white shadow-2xs font-extrabold' : 'text-slate-600 hover:bg-slate-50'"
                    >
                        Selesai ({{ completedCount }})
                    </button>
                    <button
                        @click="activeTab = 'dibatalkan'"
                        class="rounded-lg px-3 py-1.5 text-xs font-bold transition whitespace-nowrap"
                        :class="activeTab === 'dibatalkan' ? 'bg-brand-maroon text-white shadow-2xs font-extrabold' : 'text-slate-600 hover:bg-slate-50'"
                    >
                        Dibatalkan
                    </button>
                </div>

                <!-- Search Input -->
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Cari nama mahasiswa atau ID..."
                        class="w-full rounded-xl border border-slate-200/90 bg-white py-1.5 pl-8 pr-3 text-xs font-medium text-slate-900 placeholder-slate-400 focus:border-brand-maroon focus:outline-none transition shadow-2xs"
                    />
                </div>

            </div>

            <!-- Tabel Data Pesanan Komprehensif -->
            <div class="rounded-[28px] bg-white border border-slate-200/90 shadow-[0_4px_24px_-4px_rgba(0,0,0,0.04)] overflow-hidden">
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
                            <tr v-if="filteredOrders.length === 0">
                                <td colspan="8" class="py-12 text-center text-xs text-slate-500 bg-white">
                                    <div class="max-w-xs mx-auto space-y-2">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-700 border border-amber-200">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                        <p class="font-extrabold text-slate-900 text-sm">Tidak Ada Pesanan Ditemukan</p>
                                        <p class="text-[11px] text-slate-500">Coba ganti filter tab atau kata kunci pencarian Anda.</p>
                                        <button
                                            @click="searchQuery = ''; activeTab = 'semua'"
                                            class="inline-block rounded-xl bg-brand-maroon px-4 py-2 text-xs font-bold text-white shadow-2xs hover:bg-brand-maroon-hover transition cursor-pointer"
                                        >
                                            Reset Filter
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row Item (Matches Reference Screenshot Structure) -->
                            <tr
                                v-for="(order, idx) in filteredOrders"
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
                                         class="inline-flex items-center gap-1.5 cursor-pointer font-bold text-slate-700 hover:text-brand-maroon transition text-xs sm:text-sm font-mono group/num whitespace-nowrap"
                                    >
                                        <span class="text-slate-400 font-sans text-xs">#</span>
                                        <span>{{ String(order.order_number || order.id || '').replace(/^PF-/, '') }}</span>
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
                                                :href="'https://wa.me/' + String(order.whatsapp || '').replace(/[^0-9]/g, '')"
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
                                            @click="validateManual(order.id)"
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
                                            @click="cancelManual(order.id)"
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
                        <span class="font-bold text-slate-900">Menampilkan {{ filteredOrders.length }} dari {{ orders.length }} pesanan</span>
                        <span>•</span>
                        <span>Database lokal sinkron</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-semibold">Pending: <strong class="text-brand-maroon">{{ pendingCount }} Tiket</strong></span>
                        <span>•</span>
                        <button @click="reloadOrders" class="hover:text-brand-maroon font-bold underline transition cursor-pointer">Segarkan</button>
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
                            <a :href="'https://wa.me/' + String(selectedOrderModal.whatsapp || '').replace(/[^0-9]/g, '')" target="_blank" class="font-bold text-emerald-600 hover:underline">
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
                        @click="validateManual(selectedOrderModal.id); selectedOrderModal = null"
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

        <!-- Floating Banner Pesanan Baru Masuk (Fitur 3) -->
        <IncomingOrderBanner
            :order="incomingOrder"
            @close="incomingOrder = null"
        />
    </MitraLayout>
</template>
