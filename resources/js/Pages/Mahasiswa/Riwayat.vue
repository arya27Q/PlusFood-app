<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MahasiswaLayout from '@/Layouts/MahasiswaLayout.vue';
import { sampleFoods } from '@/Utils/mockFoods';
import { ref } from 'vue';

const activeTab = ref('menunggu'); // 'semua', 'menunggu', 'selesai', 'dibatalkan'
const showQrModal = ref(false);
const activeTicket = ref(null);

const orders = ref([
    {
        id: 'PF-88219',
        food: sampleFoods[0],
        quantity: 1,
        total: 12000,
        pickupTime: '19.30 – 20.30 WIB',
        date: 'Hari ini, 12 Okt 2026',
        status: 'menunggu',
        statusLabel: 'Menunggu Pickup',
        qrCode: 'PF-UNESA-88219-BS'
    },
    {
        id: 'PF-87904',
        food: sampleFoods[2],
        quantity: 2,
        total: 22000,
        pickupTime: '19.00 – 20.30 WIB',
        date: 'Kemarin, 11 Okt 2026',
        status: 'selesai',
        statusLabel: 'Selesai Diambil',
        qrCode: 'PF-UNESA-87904-CH'
    }
]);

const openQr = (order) => {
    activeTicket.value = order;
    showQrModal.value = true;
};
</script>

<template>
    <MahasiswaLayout>
        <Head title="Riwayat Pesanan - PlusFood" />

        <div class="space-y-6 max-w-4xl mx-auto">
            
            <!-- Header Riwayat & Dampak -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-[#2D1B1E]">Riwayat Pesanan</h1>
                    <p class="text-xs text-[#8C7A72] mt-0.5">Kelola kupon reservasi digital dan ambil makanan tepat waktu.</p>
                </div>

                <!-- Tabs Filter Status -->
                <div class="flex items-center gap-1.5 rounded-xl bg-white p-1.5 border border-[#EAE0D5]">
                    <button
                        @click="activeTab = 'menunggu'"
                        class="rounded-lg px-3 py-1.5 text-xs font-bold transition"
                        :class="activeTab === 'menunggu' ? 'bg-brand-maroon text-white shadow-sm' : 'text-[#8C7A72] hover:text-[#2D1B1E]'"
                    >
                        Menunggu Pickup
                    </button>
                    <button
                        @click="activeTab = 'selesai'"
                        class="rounded-lg px-3 py-1.5 text-xs font-bold transition"
                        :class="activeTab === 'selesai' ? 'bg-brand-maroon text-white shadow-sm' : 'text-[#8C7A72] hover:text-[#2D1B1E]'"
                    >
                        Selesai
                    </button>
                    <button
                        @click="activeTab = 'semua'"
                        class="rounded-lg px-3 py-1.5 text-xs font-bold transition"
                        :class="activeTab === 'semua' ? 'bg-brand-maroon text-white shadow-sm' : 'text-[#8C7A72] hover:text-[#2D1B1E]'"
                    >
                        Semua
                    </button>
                </div>
            </div>

            <!-- List Kartu Pesanan -->
            <div class="space-y-4">
                <div
                    v-for="order in orders"
                    :key="order.id"
                    v-show="activeTab === 'semua' || activeTab === order.status"
                    class="rounded-[24px] bg-white p-6 border border-[#EAE0D5] shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6"
                >
                    <div class="flex items-center gap-4">
                        <img
                            :src="order.food.image"
                            :alt="order.food.name"
                            class="h-20 w-24 rounded-2xl object-cover bg-[#FAF7F2] shrink-0"
                        />
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-brand-maroon">#{{ order.id }}</span>
                                <span class="text-[11px] text-[#8C7A72]">{{ order.date }}</span>
                                <span
                                    class="rounded-md px-2 py-0.5 text-[10px] font-bold"
                                    :class="order.status === 'menunggu' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'"
                                >
                                    {{ order.statusLabel }}
                                </span>
                            </div>

                            <h3 class="text-base font-extrabold text-[#2D1B1E] mt-1">
                                {{ order.food.shortName }} (x{{ order.quantity }})
                            </h3>
                            <p class="text-xs text-[#8C7A72] mt-0.5 flex items-center gap-1.5">
                                <span class="flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5 text-brand-maroon shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    </svg>
                                    {{ order.food.merchant }}
                                </span>
                                <span>•</span>
                                <span class="flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5 text-[#8C7A72] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <circle cx="12" cy="12" r="10" stroke-width="2" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2" />
                                    </svg>
                                    Ambil: {{ order.pickupTime }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="flex sm:flex-col items-center sm:items-end justify-between gap-3 border-t sm:border-t-0 pt-3 sm:pt-0 border-[#F5EFE6]">
                        <p class="text-lg font-black text-brand-maroon">
                            Rp{{ order.total.toLocaleString('id-ID') }}
                        </p>

                        <button
                            v-if="order.status === 'menunggu'"
                            @click="openQr(order)"
                            class="inline-flex items-center gap-2 rounded-xl bg-brand-maroon px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-brand-maroon/20 hover:bg-brand-maroon-hover transition"
                        >
                            <span>Tampilkan QR Tiket</span>
                        </button>
                        <span v-else class="text-xs font-bold text-emerald-700 flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 text-emerald-600 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Berhasil Diselamatkan
                        </span>
                    </div>
                </div>
            </div>

            <!-- MODAL QR CODE TIKET DIGITAL -->
            <div
                v-if="showQrModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 animate-page-enter"
            >
                <div class="relative w-full max-w-sm rounded-[28px] bg-white p-7 text-center shadow-2xl space-y-5">
                    
                    <button
                        @click="showQrModal = false"
                        class="absolute top-5 right-5 flex h-7 w-7 items-center justify-center rounded-full bg-[#FAF7F2] text-gray-400 hover:text-gray-700 transition"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <div class="inline-flex items-center gap-2 rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700">
                        Tiket Penyelamatan Makanan
                    </div>

                    <div>
                        <h3 class="text-lg font-black text-[#2D1B1E]">{{ activeTicket?.food.shortName }}</h3>
                        <p class="text-xs text-[#8C7A72] mt-0.5">{{ activeTicket?.food.merchant }}</p>
                    </div>

                    <!-- QR Box Mockup -->
                    <div class="mx-auto flex h-48 w-48 items-center justify-center rounded-2xl bg-[#FAF7F2] p-4 border-2 border-dashed border-brand-maroon/30 shadow-inner">
                        <img
                            src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=PLUSFOOD-TICKET-88219"
                            alt="QR Code"
                            class="h-full w-full object-contain"
                        />
                    </div>

                    <div>
                        <p class="text-xs font-mono font-bold text-[#2D1B1E] tracking-wider">{{ activeTicket?.id }}</p>
                        <p class="text-[11px] text-rose-700 font-semibold mt-1">Batas Ambil: {{ activeTicket?.pickupTime }}</p>
                    </div>

                    <p class="text-[11px] text-[#8C7A72]">
                        Tunjukkan QR Code ini kepada kasir warung saat pengambilan makanan untuk verifikasi instan.
                    </p>

                    <button
                        @click="showQrModal = false"
                        class="w-full rounded-xl bg-brand-maroon py-3 text-xs font-bold text-white shadow-md hover:bg-brand-maroon-hover transition"
                    >
                        Tutup Tiket
                    </button>

                </div>
            </div>

        </div>
    </MahasiswaLayout>
</template>
