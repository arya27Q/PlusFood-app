<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MahasiswaLayout from '@/Layouts/MahasiswaLayout.vue';
import { sampleFoods } from '@/Utils/mockFoods';
import { ref, computed } from 'vue';
import FoodPackageIcon from '@/Components/Mahasiswa/FoodPackageIcon.vue';

const getCardTheme = (pkg) => {
    const raw = (pkg.icon || '').toLowerCase();
    const t = (pkg.title || '').toLowerCase();

    if (raw === 'rice' || raw === 'bowl' || t.includes('nasi')) {
        return {
            card: 'border-2 border-amber-300 bg-amber-50/70 hover:bg-amber-50 hover:border-amber-400',
            badge: 'bg-amber-100/90 text-amber-800 border border-amber-300/80',
        };
    }
    if (raw === 'chicken' || raw === 'meat' || t.includes('ayam') || t.includes('paha') || t.includes('dada') || t.includes('bakar') || t.includes('daging')) {
        return {
            card: 'border-2 border-rose-300 bg-rose-50/70 hover:bg-rose-50 hover:border-rose-400',
            badge: 'bg-rose-100/90 text-rose-700 border border-rose-300/80',
        };
    }
    if (raw === 'sambal' || raw === 'chili' || t.includes('sambal') || t.includes('cabe') || t.includes('rawit') || t.includes('pedas')) {
        return {
            card: 'border-2 border-red-300 bg-red-50/70 hover:bg-red-50 hover:border-red-400',
            badge: 'bg-red-100/90 text-red-700 border border-red-300/80',
        };
    }
    if (raw === 'salad' || raw === 'leaf' || t.includes('lalap') || t.includes('timun') || t.includes('kemangi') || t.includes('urab') || t.includes('sayur') || t.includes('mentimun') || t.includes('acar')) {
        return {
            card: 'border-2 border-emerald-300 bg-emerald-50/70 hover:bg-emerald-50 hover:border-emerald-400',
            badge: 'bg-emerald-100/90 text-emerald-800 border border-emerald-300/80',
        };
    }
    if (raw === 'tempeh' || t.includes('tempe') || t.includes('tahu') || t.includes('crispy')) {
        return {
            card: 'border-2 border-orange-300 bg-orange-50/70 hover:bg-orange-50 hover:border-orange-400',
            badge: 'bg-orange-100/90 text-orange-800 border border-orange-300/80',
        };
    }
    if (raw === 'soup' || t.includes('soto') || t.includes('kuah') || t.includes('sop')) {
        return {
            card: 'border-2 border-yellow-300 bg-yellow-50/70 hover:bg-yellow-50 hover:border-yellow-400',
            badge: 'bg-yellow-100/90 text-yellow-800 border border-yellow-300/80',
        };
    }
    if (raw === 'noodles' || t.includes('mie') || t.includes('bakmi') || t.includes('bihun')) {
        return {
            card: 'border-2 border-orange-300 bg-orange-50/70 hover:bg-orange-50 hover:border-orange-400',
            badge: 'bg-orange-100/90 text-orange-800 border border-orange-300/80',
        };
    }
    if (raw === 'bread' || t.includes('roti') || t.includes('sobek') || t.includes('butter')) {
        return {
            card: 'border-2 border-amber-300 bg-amber-50/70 hover:bg-amber-50 hover:border-amber-400',
            badge: 'bg-amber-100/90 text-amber-900 border border-amber-300/80',
        };
    }
    if (raw === 'egg' || t.includes('telur')) {
        return {
            card: 'border-2 border-amber-300 bg-amber-50/70 hover:bg-amber-50 hover:border-amber-400',
            badge: 'bg-yellow-100/90 text-amber-800 border border-yellow-300/80',
        };
    }
    if (raw === 'cracker' || t.includes('koya') || t.includes('kerupuk') || t.includes('rempeyek')) {
        return {
            card: 'border-2 border-orange-300 bg-orange-50/70 hover:bg-orange-50 hover:border-orange-400',
            badge: 'bg-orange-100/90 text-orange-800 border border-orange-300/80',
        };
    }
    if (raw === 'cheese' || t.includes('keju') || t.includes('cokelat')) {
        return {
            card: 'border-2 border-amber-300 bg-amber-50/70 hover:bg-amber-50 hover:border-amber-400',
            badge: 'bg-amber-100/90 text-amber-900 border border-amber-300/80',
        };
    }
    if (raw === 'snack' || t.includes('gorengan') || t.includes('mendoan') || t.includes('bakwan')) {
        return {
            card: 'border-2 border-orange-300 bg-orange-50/70 hover:bg-orange-50 hover:border-orange-400',
            badge: 'bg-orange-100/90 text-orange-800 border border-orange-300/80',
        };
    }
    if (raw === 'sauce' || t.includes('petis') || t.includes('saus') || t.includes('bumbu')) {
        return {
            card: 'border-2 border-stone-300 bg-stone-50/70 hover:bg-stone-50 hover:border-stone-400',
            badge: 'bg-stone-100 text-stone-800 border border-stone-300/80',
        };
    }
    return {
        card: 'border-2 border-[#EAE0D5] bg-[#FAF7F2]/80 hover:bg-white hover:border-brand-maroon/40',
        badge: 'bg-[#F5EFE6] text-brand-maroon border border-[#EAE0D5]',
    };
};

const props = defineProps({
    slug: {
        type: String,
        default: 'nasi-ayam-bakar',
    },
    food: {
        type: Object,
        default: null,
    },
});

// Ambil makanan dari database via props.food, atau fallback ke mockFoods
const food = computed(() => {
    if (props.food) return props.food;
    return sampleFoods.find(f => f.slug === props.slug) || sampleFoods[0];
});

// State pemesanan single-product quantity
const quantity = ref(1);
const ecoContainer = ref('bawa_sendiri'); // 'bawa_sendiri' atau 'kotak_kertas'
const isBookmarked = ref(false);

const increment = () => {
    if (quantity.value < food.value.stock) {
        quantity.value++;
    }
};

const decrement = () => {
    if (quantity.value > 1) {
        quantity.value--;
    }
};

const containerFee = computed(() => {
    return ecoContainer.value === 'kotak_kertas' ? 2000 : 0;
});

const subtotal = computed(() => {
    return (food.value.priceDiscount * quantity.value) + containerFee.value;
});

const originalTotal = computed(() => {
    return food.value.priceOriginal * quantity.value;
});

const subsidySaved = computed(() => {
    return originalTotal.value - (food.value.priceDiscount * quantity.value);
});
</script>

<template>
    <MahasiswaLayout>
        <Head :title="`${food.name} - PlusFood`" />

        <div class="space-y-6">
            
            <!-- HERO IMAGE JUMBO DENGAN OVERLAY BUTTONS & BADGES -->
            <div class="relative aspect-[21/9] sm:aspect-[24/9] w-full overflow-hidden rounded-[28px] bg-slate-900 shadow-md">
                <img
                    :src="food.image"
                    :alt="food.name"
                    class="h-full w-full object-cover"
                />

                <!-- Top Floating Action Bar -->
                <div class="absolute inset-x-0 top-0 flex items-center justify-between p-4 sm:p-6 z-10">
                    <div class="flex items-center gap-3">
                        <Link
                            :href="route('katalog')"
                            class="inline-flex items-center gap-2 rounded-xl bg-white/95 px-4 py-2 text-xs font-bold text-[#2D1B1E] shadow-md backdrop-blur-sm transition hover:bg-white active:scale-95"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            <span>Kembali</span>
                        </Link>

                        <span class="inline-flex items-center gap-1.5 rounded-xl bg-rose-600 px-3 py-2 text-xs font-bold text-white shadow-md">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Diskon {{ food.discountPercent }}%
                        </span>

                        <span class="hidden sm:inline-flex items-center gap-1.5 rounded-xl bg-white/95 px-3.5 py-2 text-xs font-bold text-emerald-800 shadow-md backdrop-blur-sm">
                            <svg class="h-3.5 w-3.5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Verified Kitchen Mitra Kampus Surabaya
                        </span>
                    </div>

                    <!-- Heart Favorite Button -->
                    <button
                        @click="isBookmarked = !isBookmarked"
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/95 text-gray-600 shadow-md backdrop-blur-sm transition hover:scale-110"
                        :class="{ 'text-rose-600': isBookmarked }"
                    >
                        <svg class="h-5 w-5" :fill="isBookmarked ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </button>
                </div>

                <!-- Bottom Floating Badges -->
                <div class="absolute inset-x-0 bottom-0 flex items-center justify-between p-4 sm:p-6 z-10 text-[11px] font-bold text-white">
                    <div class="flex items-center gap-2">
                        <span class="rounded-lg bg-black/60 px-3 py-1.5 backdrop-blur-sm">
                            Batch Masak: {{ food.batchCooked || 'Sore Ini (Fresh)' }}
                        </span>
                        <span class="rounded-lg bg-black/60 px-3 py-1.5 backdrop-blur-sm hidden sm:inline-block">
                            {{ food.warmerGrade || 'Food Warmer Grade A' }}
                        </span>
                    </div>

                    <span class="rounded-lg bg-black/60 px-3 py-1.5 backdrop-blur-sm flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5 text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        Penyelamatan Emisi: {{ food.co2Saved }}
                    </span>
                </div>
            </div>

            <!-- DETAIL KONTEN & FORM RESERVASI DUA KOLOM PERSIS GAMBAR 3 -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- SISI KIRI: INFORMASI MAKANAN & MITRA (Span 8) -->
                <div class="lg:col-span-8 rounded-[28px] bg-white p-6 sm:p-8 border border-[#EAE0D5] shadow-sm space-y-6">
                    
                    <!-- Header Judul Menu & Tag -->
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-black text-[#2D1B1E] leading-tight">
                                {{ food.name }}
                            </h1>
                            <div class="mt-2.5 flex flex-wrap items-center gap-3 text-xs font-semibold text-[#8C7A72]">
                                <span class="flex items-center gap-1.5 text-[#2D1B1E] font-bold">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    {{ food.merchantName || food.merchant?.name || food.merchant }}
                                </span>
                                <span>•</span>
                                <span class="flex items-center gap-1 text-amber-500 font-bold">
                                    <svg class="h-3.5 w-3.5 fill-amber-400 text-amber-400 shrink-0" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    {{ food.rating }} ({{ food.reviewsCount || food.merchant?.reviews_count || 148 }} ulasan)
                                </span>
                                <span>•</span>
                                <span class="inline-flex items-center gap-1 rounded-lg bg-rose-50 px-2.5 py-1 text-[11px] font-bold text-rose-700 border border-rose-200">
                                    <svg class="h-3.5 w-3.5 text-rose-700 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    Higienis & Halal
                                </span>
                            </div>
                        </div>

                        <span class="rounded-xl bg-[#F5EFE6] px-3.5 py-1.5 text-xs font-bold text-brand-maroon self-start">
                            Menu Siap Santap Malam
                        </span>
                    </div>

                    <!-- Dietary & Allergen Badges Sesuai PRD Spec -->
                    <div class="flex flex-wrap items-center gap-2 pt-1 border-t border-[#F5EFE6]">
                        <span class="text-xs font-bold text-[#8C7A72] mr-1">Dietary & Alergen:</span>
                        <span
                            v-for="dTag in (food.dietaryTags || ['Halal', 'Bebas Kacang'])"
                            :key="dTag"
                            class="inline-flex items-center gap-1.5 rounded-xl px-2.5 py-1 text-xs font-bold border"
                            :class="{
                                'bg-emerald-50 text-emerald-800 border-emerald-300': dTag === 'Halal' || dTag === 'Vegetarian',
                                'bg-sky-50 text-sky-800 border-sky-300': dTag === 'Tidak Pedas',
                                'bg-amber-50 text-amber-800 border-amber-300': dTag.includes('Kacang') || dTag.includes('Gluten'),
                                'bg-rose-50 text-rose-800 border-rose-300': dTag === 'Pedas'
                            }"
                        >
                            <svg v-if="dTag === 'Halal'" class="h-3.5 w-3.5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <svg v-else class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="9" />
                                <path d="M12 8v8m-4-4h8" />
                            </svg>
                            <span>{{ dTag }}</span>
                        </span>

                        <!-- Badge Kandungan Alergen Khusus -->
                        <span v-if="food.allergens?.length" class="inline-flex items-center gap-1.5 rounded-xl bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-900 border border-amber-300">
                            <svg class="h-3.5 w-3.5 text-amber-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>Alergen: {{ food.allergens.join(', ') }}</span>
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 border border-emerald-300 px-2.5 py-1 text-xs font-bold text-emerald-800">
                            <svg class="h-3.5 w-3.5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Jaminan Kelayakan Konsumsi Hari Ini</span>
                        </span>
                    </div>

                    <!-- Price Card & Sisa Stok Alert Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- Box Harga Diskon -->
                        <div class="rounded-2xl bg-rose-50/60 p-4 border-2 border-rose-300 flex items-center justify-between shadow-xs">
                            <div>
                                <span class="text-xs text-[#9E8B83] line-through font-medium">
                                    Rp{{ food.priceOriginal.toLocaleString('id-ID') }}
                                </span>
                                <p class="text-2xl font-black text-brand-maroon">
                                    Rp{{ food.priceDiscount.toLocaleString('id-ID') }}
                                </p>
                            </div>
                            <span class="rounded-xl bg-brand-maroon px-3 py-1.5 text-xs font-black text-white shadow-sm">
                                Hemat {{ food.discountPercent }}%
                            </span>
                        </div>

                        <!-- Box Sisa Porsi Urgensi -->
                        <div class="rounded-2xl bg-rose-50/70 p-4 border-2 border-rose-300 flex items-center justify-between shadow-xs">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-100/90 text-rose-700 border border-rose-300/80">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-extrabold text-rose-900">Sisa {{ food.stock }} porsi</h4>
                                    <p class="text-[11px] text-rose-700 font-medium">Segera reservasi sebelum habis</p>
                                </div>
                            </div>
                            <span class="rounded-lg bg-white px-2.5 py-1 text-[10px] font-bold text-rose-700 shadow-sm border border-rose-300">
                                Cepat Habis
                            </span>
                        </div>

                    </div>

                    <!-- 3 Info Boxes: Jadwal, Alamat, Wadah -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        
                        <!-- Jadwal Pengambilan -->
                        <div class="rounded-2xl bg-amber-50/70 p-4 border-2 border-amber-300 shadow-xs">
                            <span class="flex items-center gap-1.5 text-[11px] font-bold text-amber-800">
                                <svg class="h-4 w-4 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10" stroke-width="2" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2" />
                                </svg>
                                Jadwal Pengambilan
                            </span>
                            <p class="mt-2 text-sm font-black text-[#2D1B1E]">{{ food.pickupTime }}</p>
                            <p class="mt-0.5 text-[10px] font-bold text-amber-900">{{ food.pickupDeadlineInfo }}</p>
                        </div>

                        <!-- Alamat Ambil -->
                        <div class="rounded-2xl bg-sky-50/70 p-4 border-2 border-sky-300 shadow-xs">
                            <span class="flex items-center gap-1.5 text-[11px] font-bold text-sky-800">
                                <svg class="h-4 w-4 text-sky-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Alamat Ambil
                            </span>
                            <p class="mt-2 text-sm font-black text-[#2D1B1E] line-clamp-1">{{ food.address }}</p>
                            <p class="mt-0.5 text-[10px] font-bold text-sky-700">{{ food.distanceMins }}</p>
                        </div>

                        <!-- Wadah Ramah Lingkungan -->
                        <div class="rounded-2xl bg-emerald-50/70 p-4 border-2 border-emerald-300 shadow-xs">
                            <span class="flex items-center gap-1.5 text-[11px] font-bold text-emerald-800">
                                <svg class="h-4 w-4 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Wadah Ramah Lingkungan
                            </span>
                            <p class="mt-1 text-[11px] text-emerald-800 leading-snug">
                                Bawa wadah sendiri untuk poin green rescue, atau pakai kotak warung tanpa biaya.
                            </p>
                        </div>

                    </div>

                    <!-- Deskripsi Makanan -->
                    <div class="pt-2">
                        <h3 class="text-sm font-bold text-[#2D1B1E]">Deskripsi Makanan</h3>
                        <p class="mt-2 text-xs sm:text-sm font-medium text-[#6E5A54] leading-relaxed">
                            {{ food.description }}
                        </p>
                    </div>

                    <!-- Komposisi Paket Menu Grid -->
                    <div class="pt-2">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-bold text-[#2D1B1E]">Komposisi Paket Menu</h3>
                            <span class="text-[11px] font-semibold text-[#8C7A72]">
                                {{ food.packages?.length || 0 }} Komponen Lengkap
                            </span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                            <div
                                v-for="(pkg, idx) in food.packages"
                                :key="idx"
                                class="group relative rounded-2xl px-2.5 py-3.5 text-center flex flex-col items-center justify-between gap-2 shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 min-h-[142px]"
                                :class="getCardTheme(pkg).card"
                            >
                                <!-- Icon Badge -->
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-2xl border shadow-xs transition-transform duration-300 group-hover:scale-110 shrink-0"
                                    :class="getCardTheme(pkg).badge"
                                >
                                    <FoodPackageIcon :icon="pkg.icon" :title="pkg.title" />
                                </div>

                                <!-- Title -->
                                <span class="text-[11px] font-extrabold text-[#2D1B1E] leading-snug line-clamp-3">
                                    {{ pkg.title }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Edukasi & Peringatan Alergen Resmi (Sesuai Kesepakatan UX) -->
                    <div class="rounded-2xl bg-amber-50/70 p-4 border-2 border-amber-200/90 text-amber-950 space-y-2">
                        <div class="flex items-center gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-200/70 text-amber-800 shrink-0">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-amber-950">Edukasi & Catatan Alergen Makanan Surplus</h4>
                                <p class="text-[10px] text-amber-800 font-semibold">Harap diperhatikan bagi rekan mahasiswa dengan riwayat alergi tertentu</p>
                            </div>
                        </div>
                        <p class="text-xs text-amber-900 leading-relaxed font-medium">
                            Menu ini disiapkan segar oleh gerai <strong>{{ food.merchantName || food.merchant?.name || food.merchant }}</strong> menggunakan dapur operasional bersama. Walaupun standar higienitas dipatuhi, kemungkinan kontak silang (*cross-contact*) dengan alergen seperti <strong>{{ food.allergens?.join(', ') || 'kacang, telur, kedelai, atau gluten' }}</strong> tetap ada. Jika memiliki alergi berat, cantumkan instruksi di catatan checkout atau hubungi penjual saat tiba di lokasi.
                        </p>
                    </div>

                    <!-- Profil Singkat Mitra Kantin -->
                    <div class="pt-4 border-t border-[#F5EFE6] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-maroon text-white font-black text-sm">
                                {{ food.merchant?.initial || food.merchantInitial || 'PF' }}
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-[#2D1B1E] flex items-center gap-2">
                                    {{ food.merchant?.name || food.merchantName || food.merchant }}
                                    <span class="rounded-md bg-[#F5EFE6] px-2 py-0.5 text-[10px] font-bold text-brand-maroon">
                                        {{ food.merchant?.campus_name || food.merchantZoneLabel || 'Mitra Kampus Surabaya' }}
                                    </span>
                                </h4>
                                <p class="text-xs text-[#8C7A72]">
                                    Bergabung sejak {{ food.merchantActiveSince || '2023' }} · {{ food.merchantRescuedCount || '1.000+' }} porsi diselamatkan
                                </p>
                            </div>
                        </div>

                        <button class="rounded-xl border border-brand-maroon px-5 py-2.5 text-xs font-bold text-brand-maroon hover:bg-brand-maroon hover:text-white transition">
                            Kunjungi Profil Warung
                        </button>
                    </div>

                </div>

                <!-- SISI KANAN: FORM RINGKASAN RESERVASI PERSIS GAMBAR 3 (Span 4) -->
                <div class="lg:col-span-4 space-y-3 sm:space-y-4">
                    
                    <!-- Kartu Reservasi Utama -->
                    <div class="rounded-2xl sm:rounded-[28px] bg-white p-4 sm:p-7 border border-[#EAE0D5] shadow-md sm:shadow-lg space-y-3.5 sm:space-y-6">
                        
                        <div class="flex items-center justify-between pb-2 sm:pb-3 border-b border-[#F5EFE6]">
                            <div>
                                <h2 class="text-sm sm:text-base font-extrabold text-[#2D1B1E]">Ringkasan Reservasi</h2>
                                <p class="text-[11px] text-[#8C7A72] mt-0.5">1 tiket untuk 1 menu (bisa pilih banyak porsi)</p>
                            </div>
                            <span class="rounded-md sm:rounded-lg bg-rose-600 px-2 sm:px-2.5 py-0.5 sm:py-1 text-[10px] sm:text-[11px] font-bold text-white shrink-0">
                                1 Tiket / Pesanan
                            </span>
                        </div>

                        <!-- Stepper Pemilih Jumlah Porsi Single-Item -->
                        <div>
                            <div class="flex items-center justify-between mb-1 sm:mb-2">
                                <label class="text-xs font-bold text-[#2D1B1E]">Jumlah Porsi</label>
                                <span class="text-[10px] sm:text-[11px] text-[#8C7A72]">Maks. {{ food.stock }} porsi</span>
                            </div>
                            
                            <div class="flex items-center justify-between rounded-xl sm:rounded-2xl bg-[#FAF7F2] p-1.5 sm:p-2.5 border border-[#D5C7BD] shadow-xs">
                                <button
                                    @click="decrement"
                                    :disabled="quantity <= 1"
                                    class="flex h-7 w-7 sm:h-9 sm:w-9 items-center justify-center rounded-lg sm:rounded-xl bg-white font-black text-xs sm:text-base text-gray-700 shadow-xs border border-[#EAE0D5] transition disabled:opacity-40 hover:bg-[#F5EFE6] hover:text-brand-maroon"
                                >
                                    -
                                </button>
                                <span class="text-sm sm:text-lg font-black text-[#2D1B1E]">{{ quantity }}</span>
                                <button
                                    @click="increment"
                                    :disabled="quantity >= food.stock"
                                    class="flex h-7 w-7 sm:h-9 sm:w-9 items-center justify-center rounded-lg sm:rounded-xl bg-white font-black text-xs sm:text-base text-gray-700 shadow-xs border border-[#EAE0D5] transition disabled:opacity-40 hover:bg-[#F5EFE6] hover:text-brand-maroon"
                                >
                                    +
                                </button>
                            </div>
                        </div>

                        <!-- Green Emission Impact Badge -->
                        <div class="rounded-xl sm:rounded-2xl bg-emerald-50/80 p-2 sm:p-3 text-center border border-emerald-300 shadow-xs">
                            <p class="text-[11px] sm:text-xs font-bold text-emerald-800 flex items-center justify-center gap-1.5">
                                <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4 shrink-0 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                                <span>Hemat emisi karbon ~{{ (0.85 * quantity).toFixed(1) }} kg CO2e</span>
                            </p>
                        </div>

                        <!-- Kalkulasi Rincian Pembayaran -->
                        <div class="space-y-1.5 sm:space-y-2.5 text-[11px] sm:text-xs text-[#6E5A54] border-t border-[#F5EFE6] pt-2.5 sm:pt-4">
                            <div class="flex justify-between">
                                <span>Harga Normal Porsi (x{{ quantity }})</span>
                                <span class="font-bold text-[#2D1B1E]">Rp{{ originalTotal.toLocaleString('id-ID') }}</span>
                            </div>
                            <div class="flex justify-between text-rose-700">
                                <span>Subsidi Surplus Pangan</span>
                                <span class="font-bold">-Rp{{ subsidySaved.toLocaleString('id-ID') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Biaya Layanan</span>
                                <span class="font-bold text-emerald-700">GRATIS</span>
                            </div>
                        </div>

                        <!-- Subtotal Pembayaran -->
                        <div class="border-t border-[#F5EFE6] pt-2.5 sm:pt-4">
                            <div class="flex items-baseline justify-between mb-2 sm:mb-3">
                                <div>
                                    <span class="text-[10px] sm:text-[11px] font-bold text-[#8C7A72] block">TOTAL PEMBAYARAN</span>
                                    <p class="text-xl sm:text-2xl font-black text-brand-maroon">
                                        Rp{{ subtotal.toLocaleString('id-ID') }}
                                    </p>
                                </div>
                                <span class="rounded-md sm:rounded-lg bg-[#FAF7F2] border border-[#EAE0D5] px-2 sm:px-2.5 py-0.5 sm:py-1 text-[10px] sm:text-[11px] font-bold text-[#6E5A54]">
                                    QRIS / Tunai
                                </span>
                            </div>

                            <!-- Tombol CTA Reservasi Sekarang -->
                            <Link
                                :href="route('checkout', { slug: food.slug, qty: quantity, packaging: ecoContainer })"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-brand-maroon py-2.5 sm:py-3.5 text-xs sm:text-sm font-bold text-white shadow-md sm:shadow-lg shadow-brand-maroon/20 hover:bg-brand-maroon-hover active:scale-95 transition"
                            >
                                <span>Reservasi Sekarang</span>
                                <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </Link>

                            <p class="mt-2 text-center text-[10px] sm:text-[11px] text-[#8C7A72] flex items-center justify-center gap-1 font-medium">
                                <svg class="h-3 w-3 sm:h-3.5 sm:w-3.5 text-brand-maroon shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <span>Konfirmasi instan via Barcode Kampus</span>
                            </p>
                        </div>

                    </div>

                    <!-- Kartu Info Aturan Food Rescue Unesa -->
                    <div class="rounded-xl sm:rounded-2xl bg-amber-50/70 p-2.5 sm:p-4 border border-amber-300 text-xs text-amber-900 space-y-0.5 sm:space-y-1 shadow-xs">
                        <h4 class="font-bold flex items-center gap-1.5 text-xs text-amber-900">
                            <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-amber-700 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01" />
                            </svg>
                            <span>Aturan Food Rescue Kampus Surabaya</span>
                        </h4>
                        <p class="text-[10px] sm:text-[11px] text-amber-800 leading-relaxed">
                            Tunjukkan kode pemesanan kepada staf {{ food.merchant?.name || food.merchantName || food.merchant }} saat mengambil makanan pukul {{ food.pickupTime }}.
                        </p>
                    </div>

                </div>

            </div>

        </div>
    </MahasiswaLayout>
</template>

