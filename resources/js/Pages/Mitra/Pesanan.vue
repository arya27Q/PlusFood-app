<script setup>
import { Head } from '@inertiajs/vue3';
import MitraLayout from '@/Layouts/MitraLayout.vue';
import ScanQrModal from '@/Components/Mitra/ScanQrModal.vue';
import { getSavedOrders, updateOrderStatus } from '@/Utils/mockFoods';
import { ref, onMounted, computed } from 'vue';

const showScanModal = ref(false);
const orders = ref([]);
const activeTab = ref('semua'); // 'semua', 'menunggu', 'selesai', 'dibatalkan'
const searchQuery = ref('');
const notification = ref('');

const reloadOrders = () => {
    orders.value = getSavedOrders();
};

onMounted(() => {
    reloadOrders();
});

const validateManual = (id) => {
    updateOrderStatus(id, 'selesai');
    reloadOrders();
    notification.value = `Pesanan #${id} berhasil divalidasi dan diselesaikan!`;
    setTimeout(() => notification.value = '', 3000);
};

const handleValidated = (order) => {
    reloadOrders();
};

const filteredOrders = computed(() => {
    return orders.value.filter(o => {
        if (activeTab.value !== 'semua' && o.status !== activeTab.value) return false;
        if (searchQuery.value) {
            const q = searchQuery.value.toLowerCase();
            const matchId = o.id.toLowerCase().includes(q);
            const matchName = o.customerName.toLowerCase().includes(q);
            const matchMenu = o.food?.name.toLowerCase().includes(q);
            if (!matchId && !matchName && !matchMenu) return false;
        }
        return true;
    });
});

const printRekap = () => {
    window.print();
};

const exportCsv = () => {
    const headers = ['Order ID,Nama Mahasiswa,NIM,Menu,Porsi,Kemasan,Total,Status,Batas Ambil\n'];
    const rows = orders.value.map(o => 
        `"${o.id}","${o.customerName}","${o.nim}","${o.food?.shortName}",${o.quantity},"${o.ecoPackagingLabel}",${o.total},"${o.statusLabel}","${o.pickupTime}"`
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
</script>

<template>
    <MitraLayout>
        <Head title="Pesanan Masuk & Riwayat Kasir - PlusFood Mitra" />

        <div class="space-y-6">
            
            <!-- Header Halaman & Ekspor -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-black text-[#2D1B1E]">Pesanan Masuk & Rekap Kasir</h1>
                    <p class="text-xs sm:text-sm text-[#6E5A54] mt-0.5">
                        Kelola verifikasi pengambilan makanan oleh mahasiswa serta cetak pembukuan kasir.
                    </p>
                </div>

                <div class="flex items-center gap-2.5">
                    <button
                        @click="printRekap"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-[#EAE0D5] bg-white px-3.5 py-2 text-xs font-bold text-[#6E5A54] hover:bg-[#FAF7F2] shadow-2xs transition"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak Rekap Kasir</span>
                    </button>

                    <button
                        @click="exportCsv"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-300 bg-emerald-50 px-3.5 py-2 text-xs font-bold text-emerald-800 hover:bg-emerald-100 shadow-2xs transition"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Ekspor CSV</span>
                    </button>

                    <button
                        @click="showScanModal = true"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-amber-400 px-4 py-2 text-xs font-black text-stone-900 shadow-sm hover:bg-amber-300 transition"
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
                <button @click="notification = ''" class="text-white/80 hover:text-white">✕</button>
            </div>

            <!-- Filter & Search Toolbar -->
            <div class="rounded-2xl bg-white p-4 border border-[#EAE0D5] shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                
                <!-- Filter Tabs -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                    <button
                        @click="activeTab = 'semua'"
                        class="rounded-lg px-3 py-1.5 text-xs font-bold transition whitespace-nowrap"
                        :class="activeTab === 'semua' ? 'bg-brand-maroon text-white shadow-2xs' : 'text-[#8C7A72] hover:bg-[#FAF7F2]'"
                    >
                        Semua ({{ orders.length }})
                    </button>
                    <button
                        @click="activeTab = 'menunggu'"
                        class="rounded-lg px-3 py-1.5 text-xs font-bold transition whitespace-nowrap"
                        :class="activeTab === 'menunggu' ? 'bg-brand-maroon text-white shadow-2xs' : 'text-[#8C7A72] hover:bg-[#FAF7F2]'"
                    >
                        Menunggu Pickup ({{ orders.filter(o => o.status === 'menunggu').length }})
                    </button>
                    <button
                        @click="activeTab = 'selesai'"
                        class="rounded-lg px-3 py-1.5 text-xs font-bold transition whitespace-nowrap"
                        :class="activeTab === 'selesai' ? 'bg-brand-maroon text-white shadow-2xs' : 'text-[#8C7A72] hover:bg-[#FAF7F2]'"
                    >
                        Selesai Diambil
                    </button>
                    <button
                        @click="activeTab = 'dibatalkan'"
                        class="rounded-lg px-3 py-1.5 text-xs font-bold transition whitespace-nowrap"
                        :class="activeTab === 'dibatalkan' ? 'bg-brand-maroon text-white shadow-2xs' : 'text-[#8C7A72] hover:bg-[#FAF7F2]'"
                    >
                        Dibatalkan
                    </button>
                </div>

                <!-- Search Input -->
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-[#9E8B83]">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Cari nama atau #Order ID..."
                        class="w-full rounded-xl border border-[#EAE0D5] bg-[#FAF7F2] py-1.5 pl-8 pr-3 text-xs font-medium text-[#2D1B1E] placeholder-[#9E8B83] focus:border-brand-maroon focus:bg-white focus:outline-none"
                    />
                </div>

            </div>

            <!-- Tabel Data Pesanan Komprehensif -->
            <div class="rounded-[24px] bg-white border border-[#EAE0D5] shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#FAF7F2] text-[#8C7A72] uppercase text-[10px] font-black border-b border-[#EAE0D5]">
                            <tr>
                                <th class="py-3.5 px-6">ID Pesanan</th>
                                <th class="py-3.5 px-4">Nama Mahasiswa</th>
                                <th class="py-3.5 px-4">Menu Surplus</th>
                                <th class="py-3.5 px-4">Jumlah</th>
                                <th class="py-3.5 px-4">Wadah</th>
                                <th class="py-3.5 px-4">Batas Ambil</th>
                                <th class="py-3.5 px-4">Total</th>
                                <th class="py-3.5 px-4">Status</th>
                                <th class="py-3.5 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F5EFE6]">
                            <tr v-if="filteredOrders.length === 0">
                                <td colspan="9" class="py-8 text-center text-xs text-[#8C7A72]">
                                    Tidak ada data pesanan yang sesuai dengan filter.
                                </td>
                            </tr>
                            <tr
                                v-for="order in filteredOrders"
                                :key="order.id"
                                class="hover:bg-[#FAF7F2]/60 transition"
                            >
                                <td class="py-4 px-6 font-mono font-black text-brand-maroon">
                                    #{{ order.id }}
                                </td>
                                <td class="py-4 px-4 font-bold text-[#2D1B1E]">
                                    <div>{{ order.customerName }}</div>
                                    <div class="text-[10px] text-[#8C7A72] font-normal">NIM: {{ order.nim }}</div>
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
                                <td class="py-4 px-4 text-[#8C7A72]">
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
                                            'bg-stone-100 text-stone-600 border border-stone-200': order.status === 'dibatalkan'
                                        }"
                                    >
                                        {{ order.statusLabel }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <button
                                        v-if="order.status === 'menunggu'"
                                        @click="validateManual(order.id)"
                                        class="rounded-xl bg-emerald-700 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-800 shadow-xs transition"
                                    >
                                        ✓ Validasi
                                    </button>
                                    <span v-else-if="order.status === 'selesai'" class="text-xs text-emerald-700 font-bold">
                                        Selesai
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
