<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MitraLayout from '@/Layouts/MitraLayout.vue';
import ScanQrModal from '@/Components/Mitra/ScanQrModal.vue';
import { getSavedOrders, updateOrderStatus, sampleFoods } from '@/Utils/mockFoods';
import { ref, onMounted, computed } from 'vue';

const showScanModal = ref(false);
const orders = ref([]);
const recentFilter = ref('semua'); // 'semua', 'menunggu', 'selesai'

const reloadOrders = () => {
    orders.value = getSavedOrders();
};

onMounted(() => {
    reloadOrders();
});

// Metrik Finansial Sesuai Spesifikasi PRD & Desain Sistem
const completedOrders = computed(() => orders.value.filter(o => o.status === 'selesai'));
const pendingOrders = computed(() => orders.value.filter(o => o.status === 'menunggu'));

const todayRevenue = computed(() => {
    return completedOrders.value.reduce((acc, curr) => acc + (curr.total || 0), 216000);
});

const rescuedPortions = computed(() => {
    return completedOrders.value.reduce((acc, curr) => acc + (curr.quantity || 1), 18);
});

const totalListingPortions = ref(24);
const recoveryRate = computed(() => {
    return Math.round((rescuedPortions.value / totalListingPortions.value) * 100);
});

const validateOrderManual = (orderId) => {
    updateOrderStatus(orderId, 'selesai');
    reloadOrders();
};

const handleValidated = (order) => {
    reloadOrders();
};
</script>

<template>
    <MitraLayout>
        <Head title="Dashboard Kasir Mitra - PlusFood" />

        <div class="space-y-7">
            
            <!-- ================= HEADER UTAMA DASHBOARD MITRA ================= -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-[#8C7A72]">
                        <span>Rabu malam, 19.35 WIB</span>
                        <span>•</span>
                        <span class="text-brand-maroon font-bold">Sesi Flash Sale Malam Aktif</span>
                    </div>
                    <h1 class="text-3xl font-black text-[#2D1B1E] mt-1">Dashboard Kasir & Pemulihan Modal</h1>
                    <p class="text-xs sm:text-sm text-[#6E5A54] mt-0.5">
                        Pantau kedatangan mahasiswa, terima pembayaran fisik di toko, dan kurangi kerugian makanan sisa.
                    </p>
                </div>

                <!-- Action CTA Header -->
                <div class="flex items-center gap-3">
                    <button
                        @click="showScanModal = true"
                        class="inline-flex items-center gap-2 rounded-xl bg-amber-400 px-5 py-3 text-xs font-black text-stone-900 shadow-md hover:bg-amber-300 transition hover:scale-105"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        <span>Buka Pemindai QR</span>
                    </button>
                    <Link
                        :href="route('mitra.kelola-menu')"
                        class="inline-flex items-center gap-2 rounded-xl bg-brand-maroon px-5 py-3 text-xs font-bold text-white shadow-md hover:bg-brand-maroon-hover transition"
                    >
                        <span>+ Tambah Surplus Kilat</span>
                    </Link>
                </div>
            </div>

            <!-- ================= KPI METRIC CARDS (GRID 3 KOLOM SESUAI SPEC) ================= -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                
                <!-- KPI 1: Pendapatan Hari Ini -->
                <div class="rounded-[24px] bg-white p-6 border border-[#EAE0D5] shadow-xs space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-[#8C7A72] uppercase tracking-wide">
                            Pendapatan Hari Ini
                        </span>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-700 border border-amber-200">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-3xl font-black text-brand-maroon">
                            Rp{{ todayRevenue.toLocaleString('id-ID') }}
                        </p>
                        <p class="text-xs text-emerald-700 font-semibold mt-1">
                            +100% masuk langsung ke kantong kasir
                        </p>
                    </div>
                </div>

                <!-- KPI 2: Porsi Terselamatkan -->
                <div class="rounded-[24px] bg-white p-6 border border-[#EAE0D5] shadow-xs space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-[#8C7A72] uppercase tracking-wide">
                            Porsi Terselamatkan
                        </span>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-baseline justify-between">
                            <p class="text-3xl font-black text-[#2D1B1E]">
                                {{ rescuedPortions }} <span class="text-base font-bold text-[#8C7A72]">/ {{ totalListingPortions }} Porsi</span>
                            </p>
                            <span class="text-xs font-extrabold text-brand-maroon">{{ recoveryRate }}%</span>
                        </div>
                        <div class="mt-2 h-2.5 w-full rounded-full bg-[#FAF7F2] border border-[#EAE0D5] overflow-hidden">
                            <div
                                class="h-full rounded-full bg-gradient-to-r from-amber-400 to-emerald-500 transition-all duration-700"
                                :style="{ width: `${recoveryRate}%` }"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- KPI 3: Break-Even Tracker / Pemulihan Modal -->
                <div class="rounded-[24px] bg-white p-6 border border-[#EAE0D5] shadow-xs space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-[#8C7A72] uppercase tracking-wide">
                            Break-Even Tracker
                        </span>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-sky-50 text-sky-700 border border-sky-200">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-3xl font-black text-emerald-700">
                            {{ recoveryRate }}% Modal Pulih
                        </p>
                        <p class="text-xs text-[#8C7A72] font-medium mt-1">
                            Mencegah kerugian Rp145.000 dari masakan sisa
                        </p>
                    </div>
                </div>

            </div>

            <!-- ================= STATUS SESI REAL-TIME BANNER ================= -->
            <div class="rounded-2xl bg-[#FAF7F2] p-4 border border-[#EAE0D5] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-maroon text-white font-bold">
                        ⏱
                    </div>
                    <div>
                        <p class="text-xs font-bold text-[#2D1B1E]">
                            Sesi Ambil Berjalan: 18.00 s/d 21.00 WIB
                        </p>
                        <p class="text-[11px] text-[#8C7A72]">
                            Tersisa <strong class="text-rose-700 font-bold">42 menit</strong> sebelum batas penutupan warung otomatis.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="rounded-lg bg-amber-100 text-amber-900 border border-amber-300 px-3 py-1 text-xs font-bold">
                        {{ pendingOrders.length }} Pesanan Belum Diambil
                    </span>
                    <button
                        @click="showScanModal = true"
                        class="rounded-lg bg-brand-maroon text-white px-3 py-1 text-xs font-bold hover:bg-brand-maroon-hover transition"
                    >
                        Scan Tiket Tiba
                    </button>
                </div>
            </div>

            <!-- ================= TABEL PESANAN MASUK (LIVE FEED) ================= -->
            <div class="rounded-[24px] bg-white border border-[#EAE0D5] shadow-xs overflow-hidden">
                <div class="p-6 border-b border-[#F5EFE6] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-base font-black text-[#2D1B1E]">Tabel Pesanan Masuk (Live Feed)</h2>
                        <p class="text-xs text-[#8C7A72]">Validasi saat mahasiswa menunjukkan tiket QR di gerai Anda.</p>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <button
                            @click="recentFilter = 'semua'"
                            class="rounded-lg px-3 py-1 text-xs font-bold transition"
                            :class="recentFilter === 'semua' ? 'bg-brand-maroon text-white shadow-2xs' : 'text-[#8C7A72] hover:bg-[#FAF7F2]'"
                        >
                            Semua ({{ orders.length }})
                        </button>
                        <button
                            @click="recentFilter = 'menunggu'"
                            class="rounded-lg px-3 py-1 text-xs font-bold transition"
                            :class="recentFilter === 'menunggu' ? 'bg-brand-maroon text-white shadow-2xs' : 'text-[#8C7A72] hover:bg-[#FAF7F2]'"
                        >
                            Menunggu Pickup ({{ pendingOrders.length }})
                        </button>
                        <button
                            @click="recentFilter = 'selesai'"
                            class="rounded-lg px-3 py-1 text-xs font-bold transition"
                            :class="recentFilter === 'selesai' ? 'bg-brand-maroon text-white shadow-2xs' : 'text-[#8C7A72] hover:bg-[#FAF7F2]'"
                        >
                            Selesai
                        </button>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#FAF7F2] text-[#8C7A72] uppercase text-[10px] font-black border-b border-[#EAE0D5]">
                            <tr>
                                <th class="py-3.5 px-6">Order ID</th>
                                <th class="py-3.5 px-4">Nama Mahasiswa</th>
                                <th class="py-3.5 px-4">Menu Dipesan</th>
                                <th class="py-3.5 px-4">Porsi</th>
                                <th class="py-3.5 px-4">Kemasan</th>
                                <th class="py-3.5 px-4">Batas Ambil</th>
                                <th class="py-3.5 px-4">Total Bayar</th>
                                <th class="py-3.5 px-4">Status</th>
                                <th class="py-3.5 px-6 text-right">Aksi Kasir</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F5EFE6]">
                            <tr
                                v-for="order in orders"
                                :key="order.id"
                                v-show="recentFilter === 'semua' || order.status === recentFilter"
                                class="hover:bg-[#FAF7F2]/60 transition"
                            >
                                <td class="py-4 px-6 font-mono font-black text-brand-maroon">
                                    #{{ order.id }}
                                </td>
                                <td class="py-4 px-4 font-bold text-[#2D1B1E]">
                                    <div>{{ order.customerName }}</div>
                                    <div class="text-[10px] text-[#8C7A72] font-normal">{{ order.nim }} · {{ order.whatsapp }}</div>
                                </td>
                                <td class="py-4 px-4 font-medium text-[#2D1B1E]">
                                    {{ order.food?.shortName }}
                                </td>
                                <td class="py-4 px-4 font-bold text-[#2D1B1E]">
                                    {{ order.quantity }} Porsi
                                </td>
                                <td class="py-4 px-4 text-[#6E5A54]">
                                    <span class="rounded bg-emerald-50 text-emerald-800 text-[10px] font-bold px-2 py-0.5 border border-emerald-200">
                                        {{ order.ecoPackaging === 'bawa_sendiri' ? 'Wadah Sendiri' : 'Kotak Kertas' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-[#8C7A72] font-medium">
                                    {{ order.pickupTime }}
                                </td>
                                <td class="py-4 px-4 font-black text-[#2D1B1E]">
                                    Rp{{ order.total?.toLocaleString('id-ID') }}
                                </td>
                                <td class="py-4 px-4">
                                    <span
                                        class="rounded-md px-2 py-0.5 text-[10px] font-extrabold"
                                        :class="{
                                            'bg-amber-100 text-amber-800 border border-amber-300': order.status === 'menunggu',
                                            'bg-emerald-100 text-emerald-800 border border-emerald-300': order.status === 'selesai',
                                            'bg-stone-100 text-stone-600': order.status === 'dibatalkan'
                                        }"
                                    >
                                        {{ order.statusLabel }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <button
                                        v-if="order.status === 'menunggu'"
                                        @click="validateOrderManual(order.id)"
                                        class="rounded-xl bg-emerald-700 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-800 shadow-xs transition"
                                    >
                                        ✓ Validasi & Ambil
                                    </button>
                                    <span v-else-if="order.status === 'selesai'" class="text-xs text-emerald-700 font-bold">
                                        Terserah Terambil
                                    </span>
                                    <span v-else class="text-xs text-stone-400">
                                        Dibatalkan
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <ScanQrModal
            v-if="showScanModal"
            @close="showScanModal = false"
            @validated="handleValidated"
        />
    </MitraLayout>
</template>
