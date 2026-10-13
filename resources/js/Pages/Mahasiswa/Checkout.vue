<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MahasiswaLayout from '@/Layouts/MahasiswaLayout.vue';
import { sampleFoods } from '@/Utils/mockFoods';
import { ref } from 'vue';
import { useAuth } from '@/Composables/useAuth';

const auth = useAuth();
const food = ref(sampleFoods[0]);
const quantity = ref(1);
const ecoPackaging = ref('bawa_sendiri'); // 'bawa_sendiri' atau 'kotak_kertas'
const isSuccess = ref(false);

const userName = ref(auth.user.value?.name || 'Raka Pratama');
const userNim = ref(auth.user.value?.nim || '23051204088');
const userWhatsapp = ref(auth.user.value?.whatsapp || '081234567890');
</script>

<template>
    <MahasiswaLayout>
        <Head title="Konfirmasi Reservasi - PlusFood" />

        <div class="max-w-2xl mx-auto space-y-6">
            
            <div class="flex items-center gap-3">
                <Link
                    :href="route('makanan.detail', food.slug)"
                    class="rounded-xl p-2 bg-white border border-[#EAE0D5] text-[#2D1B1E] hover:bg-[#F5EFE6]"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <h1 class="text-2xl font-black text-[#2D1B1E]">Konfirmasi Reservasi</h1>
            </div>

            <!-- Card Ringkasan Produk Single-Item -->
            <div class="rounded-[24px] bg-white p-6 border border-[#EAE0D5] shadow-sm flex items-center gap-4">
                <img
                    :src="food.image"
                    :alt="food.name"
                    class="h-20 w-24 rounded-2xl object-cover bg-[#FAF7F2]"
                />
                <div class="flex-1">
                    <span class="text-[11px] font-bold text-rose-700 bg-rose-50 px-2.5 py-0.5 rounded-md">
                        Diskon {{ food.discountPercent }}%
                    </span>
                    <h3 class="text-base font-extrabold text-[#2D1B1E] mt-1">{{ food.shortName }}</h3>
                    <p class="text-xs text-[#8C7A72]">{{ food.merchant }} · {{ food.pickupTime }}</p>
                    <p class="text-sm font-black text-brand-maroon mt-1">
                        Rp{{ (food.priceDiscount * quantity).toLocaleString('id-ID') }}
                    </p>
                </div>
            </div>

            <!-- Card Opsi Wadah Ramah Lingkungan -->
            <div class="rounded-[24px] bg-white p-6 border border-[#EAE0D5] shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-[#2D1B1E]">Opsi Wadah Makanan</h3>

                <div class="space-y-3">
                    <label
                        class="flex items-center justify-between p-3.5 rounded-xl border cursor-pointer transition"
                        :class="ecoPackaging === 'bawa_sendiri' ? 'border-brand-maroon bg-emerald-50/40 ring-1 ring-brand-maroon' : 'border-[#EAE0D5] bg-[#FAF7F2]'"
                    >
                        <div class="flex items-center gap-3">
                            <input type="radio" value="bawa_sendiri" v-model="ecoPackaging" class="text-brand-maroon focus:ring-brand-maroon" />
                            <div>
                                <p class="text-xs font-bold text-[#2D1B1E]">Bawa Wadah Sendiri (Zero Waste)</p>
                                <p class="text-[11px] text-emerald-700 font-semibold">+10 Green Points · Bantu kurangi sampah plastik</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-emerald-700">GRATIS</span>
                    </label>

                    <label
                        class="flex items-center justify-between p-3.5 rounded-xl border cursor-pointer transition"
                        :class="ecoPackaging === 'kotak_kertas' ? 'border-brand-maroon bg-rose-50/40 ring-1 ring-brand-maroon' : 'border-[#EAE0D5] bg-[#FAF7F2]'"
                    >
                        <div class="flex items-center gap-3">
                            <input type="radio" value="kotak_kertas" v-model="ecoPackaging" class="text-brand-maroon focus:ring-brand-maroon" />
                            <div>
                                <p class="text-xs font-bold text-[#2D1B1E]">Kotak Kertas Makanan Ramah Lingkungan</p>
                                <p class="text-[11px] text-[#8C7A72]">Disediakan oleh pihak warung mitra</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-[#2D1B1E]">+Rp2.000</span>
                    </label>
                </div>
            </div>

            <!-- Card Data Mahasiswa Pemesan -->
            <div class="rounded-[24px] bg-white p-6 border border-[#EAE0D5] shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-[#2D1B1E]">Data Mahasiswa Pemesan</h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block text-[#8C7A72] font-semibold mb-1">Nama Lengkap (Sesuai KTM)</label>
                        <input type="text" v-model="userName" class="w-full rounded-xl border border-[#EAE0D5] bg-[#FAF7F2] p-2.5 font-bold text-[#2D1B1E]" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[#8C7A72] font-semibold mb-1">NIM</label>
                            <input type="text" v-model="userNim" class="w-full rounded-xl border border-[#EAE0D5] bg-[#FAF7F2] p-2.5 font-bold text-[#2D1B1E]" />
                        </div>
                        <div>
                            <label class="block text-[#8C7A72] font-semibold mb-1">WhatsApp</label>
                            <input type="text" v-model="userWhatsapp" class="w-full rounded-xl border border-[#EAE0D5] bg-[#FAF7F2] p-2.5 font-bold text-[#2D1B1E]" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA Kunci Reservasi -->
            <div>
                <button
                    @click="isSuccess = true"
                    class="w-full rounded-xl bg-brand-maroon py-4 text-sm font-bold text-white shadow-lg shadow-brand-maroon/25 hover:bg-brand-maroon-hover transition active:scale-95"
                >
                    Konfirmasi & Kunci Reservasi (Rp{{ ((food.priceDiscount * quantity) + (ecoPackaging === 'kotak_kertas' ? 2000 : 0)).toLocaleString('id-ID') }})
                </button>
            </div>

            <!-- POPUP SUKSES RESERVASI -->
            <div
                v-if="isSuccess"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 animate-page-enter"
            >
                <div class="w-full max-w-sm rounded-[28px] bg-white p-7 text-center shadow-2xl space-y-4">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700 border-2 border-emerald-300 shadow-md">
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-[#2D1B1E]">Reservasi Berhasil!</h3>
                    <p class="text-xs text-[#8C7A72]">
                        Stok porsi telah dikunci untuk Anda. Kupon digital bertanda QR Code telah diterbitkan di menu Riwayat.
                    </p>
                    <div class="pt-2">
                        <Link
                            :href="route('riwayat')"
                            class="block w-full rounded-xl bg-brand-maroon py-3 text-xs font-bold text-white shadow-md hover:bg-brand-maroon-hover transition"
                        >
                            Lihat Tiket QR Sekarang
                        </Link>
                    </div>
                </div>
            </div>

        </div>
    </MahasiswaLayout>
</template>
