<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MahasiswaLayout from '@/Layouts/MahasiswaLayout.vue';
import PickupTicketDetail from '@/Components/Mahasiswa/PickupTicketDetail.vue';
import { useRiwayat } from '@/Composables/useRiwayat';

const props = defineProps({
    initialTicketId: {
        type: String,
        default: null,
    },
    dbOrders: {
        type: Array,
        default: () => [],
    },
});

const {
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
} = useRiwayat(props);
</script>

<template>
    <MahasiswaLayout>
        <Head :title="activeTicketOrder ? 'Tunjukkan QR ke Penjual - PlusFood' : 'Riwayat Pesanan & Tiket QR - PlusFood'" />

        <!-- ===== VIEW DETAIL TIKET PICKUP DIGITAL (Image 2 Replica) ===== -->
        <PickupTicketDetail
            v-if="activeTicketOrder"
            :order="activeTicketOrder"
            @back="handleBackFromTicket"
            @cancel="handleCancelOrder"
        />

        <!-- ===== VIEW RIWAYAT PESANAN UTAMA ===== -->
        <div v-else class="space-y-7 max-w-4xl mx-auto">
            
            <!-- ================= HEADER RIWAYAT & METRIK DAMPAK SEMESTER ================= -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-black text-[#2D1B1E]">Riwayat & Tiket QR</h1>
                    <p class="text-xs text-[#8C7A72] mt-0.5">
                        Tunjukkan QR Code digital kepada kasir mitra sebelum jam operasional berakhir.
                    </p>
                </div>

                <!-- Tabs Filter Status Sesuai PRD Spec -->
                <div class="flex items-center gap-1 rounded-2xl bg-white p-1.5 border border-[#EAE0D5] shadow-2xs overflow-x-auto">
                    <button
                        @click="activeTab = 'menunggu'"
                        class="rounded-xl px-3 py-1.5 text-xs font-bold transition whitespace-nowrap"
                        :class="activeTab === 'menunggu' ? 'bg-brand-maroon text-white shadow-xs' : 'text-[#8C7A72] hover:text-[#2D1B1E]'"
                    >
                        Menunggu Pickup ({{ orders.filter(o => o.status === 'menunggu').length }})
                    </button>
                    <button
                        @click="activeTab = 'selesai'"
                        class="rounded-xl px-3 py-1.5 text-xs font-bold transition whitespace-nowrap"
                        :class="activeTab === 'selesai' ? 'bg-brand-maroon text-white shadow-xs' : 'text-[#8C7A72] hover:text-[#2D1B1E]'"
                    >
                        Selesai
                    </button>
                    <button
                        @click="activeTab = 'dibatalkan'"
                        class="rounded-xl px-3 py-1.5 text-xs font-bold transition whitespace-nowrap"
                        :class="activeTab === 'dibatalkan' ? 'bg-brand-maroon text-white shadow-xs' : 'text-[#8C7A72] hover:text-[#2D1B1E]'"
                    >
                        Dibatalkan
                    </button>
                    <button
                        @click="activeTab = 'semua'"
                        class="rounded-xl px-3 py-1.5 text-xs font-bold transition whitespace-nowrap"
                        :class="activeTab === 'semua' ? 'bg-brand-maroon text-white shadow-xs' : 'text-[#8C7A72] hover:text-[#2D1B1E]'"
                    >
                        Semua
                    </button>
                    <button
                        type="button"
                        @click="clearAllOrders"
                        class="rounded-xl px-2.5 py-1.5 text-xs font-bold transition whitespace-nowrap text-rose-600 hover:bg-rose-50 border border-rose-200/80 cursor-pointer ml-1"
                        title="Kosongkan seluruh riwayat pesanan untuk simulasi testing baru"
                    >
                        Kosongkan Riwayat
                    </button>
                </div>
            </div>

            <!-- ================= BANNER DRAFT CHECKOUT TERTUNDA ================= -->
            <div
                v-if="draftCheckout"
                class="rounded-[22px] bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-300 p-4 shadow-sm animate-fade-in flex flex-col sm:flex-row sm:items-center justify-between gap-3"
            >
                <div class="flex items-center gap-3.5 min-w-0">
                    <img
                        :src="draftCheckout.foodImage || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80'"
                        :alt="draftCheckout.foodName"
                        class="h-12 w-12 rounded-xl object-cover bg-amber-100 border border-amber-200 shrink-0"
                    />
                    <div class="min-w-0">
                        <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase tracking-wider text-amber-900 bg-amber-200/80 px-2 py-0.5 rounded-md">
                            <svg class="w-3 h-3 text-amber-800" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/></svg>
                            Pesanan Belum Selesai Dibayar
                        </span>
                        <h4 class="text-sm font-black text-[#2D1B1E] truncate mt-0.5">
                            {{ draftCheckout.foodName }}
                        </h4>
                        <p class="text-xs text-[#6E5A54] truncate">
                            {{ draftCheckout.merchantName }} · {{ draftCheckout.quantity }} porsi · Total: <strong class="text-brand-maroon">Rp{{ Number(draftCheckout.totalPayment || 0).toLocaleString('id-ID') }}</strong>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                    <button
                        type="button"
                        @click="discardDraft"
                        class="rounded-xl px-3 py-1.5 text-xs font-bold text-stone-500 hover:text-rose-600 transition"
                    >
                        Batalkan
                    </button>
                    <Link
                        :href="route('checkout', { slug: draftCheckout.slug, qty: draftCheckout.quantity, packaging: draftCheckout.packaging })"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-brand-maroon px-4 py-2 text-xs font-black text-white hover:bg-brand-maroon-hover shadow-md shadow-brand-maroon/20 transition active:scale-95"
                    >
                        <span>Lanjutkan Pembayaran</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </Link>
                </div>
            </div>

            <!-- ================= BANNER RINGKASAN DAMPAK 1 SEMESTER (POV DOCS) ================= -->
            <div class="rounded-[24px] bg-gradient-to-r from-emerald-900 via-emerald-800 to-teal-900 p-6 text-white shadow-md">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="rounded bg-white/20 px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wider backdrop-blur-xs">
                                Green Impact Semester Ini
                            </span>
                            <span class="text-xs text-emerald-200 font-semibold">Level 3 Rescuer</span>
                        </div>
                        <h2 class="text-xl font-black mt-1">Dampak Nyata Penyelamatan Makanan Anda</h2>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-center">
                        <div class="bg-white/10 rounded-xl p-2.5 backdrop-blur-xs">
                            <span class="text-[10px] text-white/70 block uppercase font-bold">Porsi</span>
                            <span class="text-base sm:text-lg font-black text-amber-300">{{ totalRescuedPortions }} Porsi</span>
                        </div>
                        <div class="bg-white/10 rounded-xl p-2.5 backdrop-blur-xs">
                            <span class="text-[10px] text-white/70 block uppercase font-bold">Hemat Uang</span>
                            <span class="text-base sm:text-lg font-black text-emerald-300">Rp{{ Number(totalMoneySaved || 0).toLocaleString('id-ID') }}</span>
                        </div>
                        <div class="bg-white/10 rounded-xl p-2.5 backdrop-blur-xs">
                            <span class="text-[10px] text-white/70 block uppercase font-bold">Emisi Dicegah</span>
                            <span class="text-base sm:text-lg font-black text-white">{{ totalCo2Saved }} kg</span>
                        </div>
                        <div class="bg-white/10 rounded-xl p-2.5 backdrop-blur-xs">
                            <span class="text-[10px] text-white/70 block uppercase font-bold">Green Points</span>
                            <span class="inline-flex items-center gap-1.5 text-base sm:text-lg font-black text-amber-200">
                                <svg class="h-4 w-4 text-amber-300 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.5 2 2 6.5 2 12c0 3.5 1.8 6.6 4.5 8.4C6.2 17 8 13.5 12 11c4-2.5 7.5-2 9.5-.5C21.8 8.5 17.5 2 12 2z" />
                                </svg>
                                {{ totalGreenPoints }} Poin
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= LIST KARTU PESANAN ================= -->
            <div class="space-y-4">
                <!-- Empty State -->
                <div v-if="filteredOrders.length === 0" class="rounded-[24px] bg-white p-12 text-center border border-[#EAE0D5] space-y-3">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-[#2D1B1E]">Belum Ada Pesanan di Kategori Ini</h3>
                    <p class="text-xs text-[#8C7A72] max-w-sm mx-auto">
                        Jelajahi katalog surplus untuk menyelamatkan makanan enak ramah kantong malam ini.
                    </p>
                    <Link
                        :href="route('katalog')"
                        class="inline-block rounded-xl bg-brand-maroon px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-brand-maroon-hover transition"
                    >
                        Buka Katalog Surplus
                    </Link>
                </div>

                <!-- Order Cards -->
                <div
                    v-for="order in filteredOrders"
                    :key="order.id"
                    class="rounded-[24px] bg-white p-6 border border-[#EAE0D5] shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 hover:shadow-md transition"
                >
                    <div class="flex items-start gap-4">
                        <img
                            :src="order.food?.image"
                            :alt="order.food?.name"
                            class="h-20 w-24 rounded-2xl object-cover bg-[#FAF7F2] shrink-0 border border-[#EAE0D5]"
                        />
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-xs font-black text-brand-maroon">#{{ order.id }}</span>
                                <span class="text-[11px] text-[#8C7A72]">{{ order.date }}</span>
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
                            </div>

                            <h3 class="text-base font-extrabold text-[#2D1B1E] mt-1.5">
                                {{ order.food?.shortName || order.food?.name }} (x{{ order.quantity }})
                            </h3>
                            <p class="text-xs text-[#8C7A72] mt-0.5 flex flex-wrap items-center gap-1.5">
                                <span class="flex items-center gap-1 font-semibold text-[#6E5A54]">
                                    <svg class="h-3.5 w-3.5 text-brand-maroon shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    </svg>
                                    {{ getMerchantName(order.food) }}
                                </span>
                                <span>•</span>
                                <span class="flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5 text-[#8C7A72] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <circle cx="12" cy="12" r="10" stroke-width="2" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2" />
                                    </svg>
                                    Batas Ambil: {{ order.pickupTime }}
                                </span>
                            </p>
                            <p v-if="order.ecoPackagingLabel" class="inline-flex items-center gap-1.5 text-[11px] text-emerald-700 font-bold mt-1">
                                <svg class="h-3.5 w-3.5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                {{ order.ecoPackagingLabel }}
                            </p>
                        </div>
                    </div>

                    <div class="flex sm:flex-col items-center sm:items-end justify-between gap-3 border-t sm:border-t-0 pt-3 sm:pt-0 border-[#F5EFE6]">
                        <p class="text-lg font-black text-brand-maroon">
                            Rp{{ Number(order.total ?? order.total_price ?? 0).toLocaleString('id-ID') }}
                        </p>

                        <!-- Button Tampilkan QR jika menunggu pickup & Batalkan -->
                        <div v-if="order.status === 'menunggu'" class="flex items-center gap-2">
                            <button
                                v-if="isOrderWithin15Mins(order)"
                                type="button"
                                @click="openCancelFromList(order)"
                                class="rounded-xl px-3 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 border border-rose-200 transition cursor-pointer"
                                title="Batalkan pesanan (tersisa waktu pembatalan)"
                            >
                                Batalkan
                            </button>
                            <button
                                @click="openQr(order)"
                                class="inline-flex items-center gap-2 rounded-xl bg-brand-maroon px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-brand-maroon/20 hover:bg-brand-maroon-hover transition cursor-pointer"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                                <span>Tampilkan QR Tiket</span>
                            </button>
                        </div>

                        <!-- Selesai: Bisa beri ulasan berhadiah atau tampilkan badge diulas -->
                        <div v-else-if="order.status === 'selesai'" class="flex flex-col sm:items-end gap-1.5">
                            <span class="text-xs font-bold text-emerald-700 flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-emerald-600 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Selesai Diselamatkan
                            </span>
                            
                            <button
                                v-if="!order.isReviewed && !order.hasReview"
                                @click="openReviewModal(order)"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-amber-400 hover:bg-amber-300 px-3.5 py-1.5 text-xs font-black text-stone-900 shadow-xs transition"
                            >
                                <svg class="h-3.5 w-3.5 text-stone-900 fill-stone-900 shrink-0" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span>Beri Ulasan (+15 Poin)</span>
                            </button>
                            <span
                                v-else
                                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-800 border border-emerald-300"
                            >
                                <svg class="h-3.5 w-3.5 text-amber-500 fill-amber-500 shrink-0" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span>Diulas ({{ order.review?.rating || 5 }}/5) · +15 Poin</span>
                            </span>
                        </div>

                        <span v-else class="text-xs font-bold text-stone-500">
                            Pesanan Dibatalkan
                        </span>
                    </div>
                </div>
            </div>

            <!-- ================= MODAL ULASAN BERHADIAH (+15 GREEN POINTS - FULL SCREEN) ================= -->
            <Teleport to="body">
                <div
                    v-if="showReviewModal"
                    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/75 backdrop-blur-sm p-4 animate-fade-in"
                    style="top: 0; left: 0; width: 100vw; height: 100vh;"
                >
                    <div class="relative w-full max-w-md rounded-[28px] bg-white p-6 sm:p-7 shadow-2xl border border-[#EAE0D5] space-y-5">
                        <button
                            @click="showReviewModal = false"
                            class="absolute top-5 right-5 flex h-7 w-7 items-center justify-center rounded-full bg-[#FAF7F2] text-gray-400 hover:text-gray-700 transition"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <div>
                            <div class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-800 px-3 py-1 text-xs font-black border border-emerald-300 mb-2">
                                <svg class="h-3.5 w-3.5 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.5 2 2 6.5 2 12c0 3.5 1.8 6.6 4.5 8.4C6.2 17 8 13.5 12 11c4-2.5 7.5-2 9.5-.5C21.8 8.5 17.5 2 12 2z" />
                                </svg>
                                <span>+15 Green Points Reward</span>
                            </div>
                            <h3 class="text-xl font-black text-[#2D1B1E]">Ulas Makanan Diselamatkan</h3>
                            <p class="text-xs text-[#8C7A72]">
                                Bantu mitra <strong>{{ activeReviewOrder?.food?.merchant }}</strong> dan dapatkan poin gamifikasi!
                            </p>
                        </div>

                        <!-- Bintang Rating -->
                        <div class="space-y-1.5 text-center bg-[#FAF7F2] p-4 rounded-2xl border border-[#EAE0D5]">
                            <span class="text-xs font-bold text-[#6E5A54] block">Nilai Pengalaman Anda</span>
                            <div class="flex items-center justify-center gap-2 pt-1">
                                <button
                                    v-for="star in 5"
                                    :key="star"
                                    type="button"
                                    @click="reviewRating = star"
                                    class="p-1 transition hover:scale-125"
                                >
                                    <svg
                                        class="h-8 w-8"
                                        :class="star <= reviewRating ? 'text-amber-400 fill-amber-400' : 'text-gray-300 fill-none'"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                </button>
                            </div>
                            <span class="text-xs font-bold text-amber-700">{{ reviewRating }} dari 5 Bintang</span>
                        </div>

                        <!-- Quick-Tags Selector Sesuai System Design Specs -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-[#2D1B1E]">Pilih Quick-Tags Penilaian:</label>
                            <div class="flex flex-wrap gap-1.5">
                                <button
                                    v-for="tag in quickReviewTags"
                                    :key="tag"
                                    type="button"
                                    @click="toggleReviewTag(tag)"
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition border inline-flex items-center gap-1.5"
                                    :class="selectedReviewTags.includes(tag) ? 'bg-brand-maroon text-white border-brand-maroon shadow-2xs' : 'bg-[#FAF7F2] text-[#6E5A54] border-[#EAE0D5] hover:bg-white'"
                                >
                                    <svg v-if="selectedReviewTags.includes(tag)" class="w-3 h-3 text-white shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <svg v-else class="w-3 h-3 text-[#8C7A72] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span>{{ tag }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Catatan / Ulasan Mahasiswa -->
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-[#2D1B1E]">Ulasan Tambahan (Opsional)</label>
                            <textarea
                                v-model="reviewComment"
                                rows="2"
                                placeholder="Contoh: Makanannya enak dan masih hangat, penjualnya sangat ramah..."
                                class="w-full rounded-xl border border-[#EAE0D5] bg-[#FAF7F2] p-3 text-xs font-medium text-[#2D1B1E] focus:border-brand-maroon focus:bg-white focus:outline-none"
                            ></textarea>
                        </div>

                        <!-- Submit CTA -->
                        <div class="pt-1">
                            <button
                                type="button"
                                @click="submitReview"
                                class="w-full rounded-xl bg-brand-maroon py-3 text-xs font-bold text-white shadow-md hover:bg-brand-maroon-hover transition flex items-center justify-center gap-2"
                            >
                                <span>Kirim Ulasan & Klaim +15 Poin</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </Teleport>

            <!-- ================= MODAL PEMBATALAN PESANAN DARI DAFTAR RIWAYAT ================= -->
            <Teleport to="body">
                <!-- Modal Pilih Alasan Pembatalan (Toleransi <= 15 Menit) -->
                <div
                    v-if="showCancelModal"
                    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/75 backdrop-blur-sm p-4 animate-fade-in"
                    style="top: 0; left: 0; width: 100vw; height: 100vh;"
                >
                    <div class="w-full max-w-md rounded-[28px] bg-white p-6 shadow-2xl border border-[#EAE0D5] space-y-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-rose-50 text-rose-700 shrink-0">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-black text-[#2D1B1E]">Batalkan Pesanan Surplus?</h3>
                                    <p class="text-xs text-stone-500">
                                        Toleransi pembatalan tersisa <strong>{{ getCancelMinutesRemaining(cancelModalOrder) }} menit</strong>.
                                    </p>
                                </div>
                            </div>
                            <button
                                type="button"
                                @click="showCancelModal = false"
                                class="rounded-full p-1.5 text-stone-400 hover:bg-stone-100 hover:text-stone-700 transition"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Pilih Alasan Pembatalan (POV Mahasiswa) -->
                        <div class="space-y-2 pt-1">
                            <label class="block text-xs font-bold text-stone-700">
                                Pilih alasan pembatalan:
                            </label>
                            <div class="space-y-1.5">
                                <label
                                    v-for="(reason, idx) in cancelReasons"
                                    :key="idx"
                                    class="flex items-center gap-3 p-2.5 rounded-xl border text-xs font-semibold cursor-pointer transition"
                                    :class="selectedCancelReason === reason ? 'border-brand-maroon bg-brand-maroon/5 text-brand-maroon font-bold' : 'border-stone-200 text-stone-600 hover:bg-stone-50'"
                                >
                                    <input
                                        type="radio"
                                        name="list_cancel_reason"
                                        :value="reason"
                                        v-model="selectedCancelReason"
                                        class="text-brand-maroon focus:ring-brand-maroon h-4 w-4"
                                    />
                                    <span class="flex-1">{{ reason }}</span>
                                </label>
                            </div>

                            <!-- Input Alasan Lainnya -->
                            <div v-if="selectedCancelReason === 'Lainnya'" class="pt-1.5">
                                <textarea
                                    v-model="customCancelReason"
                                    rows="2"
                                    placeholder="Tuliskan alasan pembatalan Anda..."
                                    class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800 placeholder:text-stone-400 focus:border-brand-maroon focus:ring-brand-maroon"
                                ></textarea>
                            </div>
                        </div>

                        <div class="rounded-xl bg-amber-50/80 p-3 text-[11px] text-amber-900 border border-amber-200/80 leading-relaxed">
                            Porsi makanan surplus ini akan langsung otomatis dikembalikan ke etalase katalog agar dapat diselamatkan mahasiswa lain.
                        </div>

                        <div class="flex items-center gap-2.5 pt-1">
                            <button
                                type="button"
                                @click="showCancelModal = false"
                                class="flex-1 rounded-xl border border-stone-200 py-2.5 text-xs font-bold text-stone-600 hover:bg-stone-50 transition cursor-pointer"
                            >
                                Kembali
                            </button>
                            <button
                                type="button"
                                :disabled="isSubmittingCancel"
                                @click="handleConfirmCancelFromList"
                                class="flex-1 rounded-xl bg-rose-600 py-2.5 text-xs font-bold text-white hover:bg-rose-700 transition disabled:opacity-50 flex items-center justify-center gap-2 cursor-pointer"
                            >
                                <svg v-if="isSubmittingCancel" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>{{ isSubmittingCancel ? 'Membatalkan...' : 'Ya, Batalkan Pesanan' }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal Notice jika waktu pembatalan sudah lewat 15 menit -->
                <div
                    v-if="showExpiredNotice"
                    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/75 backdrop-blur-sm p-4 animate-fade-in"
                    style="top: 0; left: 0; width: 100vw; height: 100vh;"
                >
                    <div class="w-full max-w-sm rounded-[24px] bg-white p-6 shadow-2xl border border-[#E8DFD8] text-center space-y-4">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-amber-50 text-amber-600">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" stroke-width="2" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01" />
                            </svg>
                        </div>

                        <div>
                            <h3 class="text-base font-black text-[#2D1B1E]">Tidak Dapat Dibatalkan</h3>
                            <p class="text-xs text-stone-500 mt-1.5 leading-relaxed">
                                Pesanan ini telah melewati batas toleransi <strong>15 menit</strong> setelah pemesanan. Makanan surplus sedang disiapkan oleh mitra penjual.
                            </p>
                        </div>

                        <div class="pt-2">
                            <button
                                type="button"
                                @click="showExpiredNotice = false"
                                class="w-full rounded-xl bg-brand-maroon py-2.5 text-xs font-bold text-white hover:bg-brand-maroon-hover transition"
                            >
                                Saya Mengerti
                            </button>
                        </div>
                    </div>
                </div>
            </Teleport>

            <!-- ================= REWARD TOAST POPUP ================= -->
            <div
                v-if="reviewRewardToast"
                class="fixed bottom-24 sm:bottom-8 right-6 z-50 rounded-2xl bg-emerald-900 text-white px-5 py-3.5 shadow-2xl flex items-center gap-3 border border-emerald-500 animate-in slide-in-from-bottom duration-300"
            >
                <div class="h-9 w-9 rounded-xl bg-emerald-500/30 flex items-center justify-center text-emerald-300 shrink-0">
                    <svg class="h-5 w-5 text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.5 2 2 6.5 2 12c0 3.5 1.8 6.6 4.5 8.4C6.2 17 8 13.5 12 11c4-2.5 7.5-2 9.5-.5C21.8 8.5 17.5 2 12 2z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs font-black">Ulasan Berhasil Terkirim!</h4>
                    <p class="text-[11px] text-emerald-200">+15 Green Points telah ditambahkan ke profil Anda.</p>
                </div>
            </div>

        </div>
    </MahasiswaLayout>
</template>
