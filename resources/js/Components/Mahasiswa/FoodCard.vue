<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    food: {
        type: Object,
        required: true,
    },
});

const isBookmarked = ref(false);
</script>

<template>
    <div class="group rounded-[22px] bg-white border border-[#EAE0D5] p-3 shadow-xs transition-all duration-300 hover:shadow-xl hover:border-brand-maroon/30 flex flex-col justify-between">
        
        <!-- Foto Produk & Badges Overlay -->
        <div class="relative aspect-[16/10] w-full overflow-hidden rounded-2xl bg-[#FAF7F2]">
            <img
                :src="food.image"
                :alt="food.name"
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                loading="lazy"
            />
            
            <!-- Badge Diskon di Pojok Kiri Atas -->
            <span class="absolute top-2.5 left-2.5 rounded-lg bg-rose-600 px-2.5 py-1 text-[11px] font-extrabold text-white shadow-sm flex items-center gap-1">
                <span>Diskon {{ food.discountPercent }}%</span>
            </span>

            <!-- Countdown Pill: Hitung Mundur Sisa Waktu -->
            <span class="absolute bottom-2.5 left-2.5 rounded-lg bg-black/75 backdrop-blur-xs px-2.5 py-1 text-[10px] font-bold text-white flex items-center gap-1.5 shadow-sm">
                <svg class="h-3 w-3 text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <circle cx="12" cy="12" r="10" stroke-width="2" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2" />
                </svg>
                <span>Sisa {{ food.minutesLeft || 35 }} mnt</span>
            </span>

            <!-- Tombol Bookmark / Favorit -->
            <button
                @click.stop="isBookmarked = !isBookmarked"
                class="absolute top-2.5 right-2.5 flex h-7 w-7 items-center justify-center rounded-full bg-white/95 text-gray-400 shadow-sm backdrop-blur-sm transition hover:text-rose-600 hover:scale-110 active:scale-95"
                :class="{ 'text-rose-600': isBookmarked }"
                title="Simpan ke favorit"
            >
                <svg class="h-4 w-4" :fill="isBookmarked ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </button>
        </div>

        <!-- Konten Kartu -->
        <div class="mt-3 flex-1 flex flex-col justify-between">
            <div>
                <!-- Warung & Rating Ringkas -->
                <div class="flex items-center justify-between text-[11px] font-semibold text-[#8C7A72]">
                    <p class="flex items-center gap-1.5 truncate max-w-[170px]" :title="food.merchant">
                        <svg class="h-3 w-3 text-[#A8988F] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span class="truncate">{{ food.merchant }}</span>
                    </p>
                    <span class="flex items-center gap-1 text-amber-500 font-bold shrink-0">
                        <svg class="h-3 w-3 fill-amber-400 text-amber-400" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <span>{{ food.rating }}</span>
                    </span>
                </div>

                <!-- Nama Makanan -->
                <Link :href="route('makanan.detail', food.slug)">
                    <h3 class="mt-1 text-sm font-bold text-[#2D1B1E] line-clamp-1 hover:text-brand-maroon transition leading-snug" :title="food.name">
                        {{ food.shortName || food.name }}
                    </h3>
                </Link>

                <!-- Dietary Badges Ringkas -->
                <div v-if="food.dietaryTags?.length" class="mt-1.5 flex flex-wrap gap-1">
                    <span
                        v-for="tag in food.dietaryTags.slice(0, 2)"
                        :key="tag"
                        class="rounded-md bg-[#F5EFE6] px-1.5 py-0.5 text-[9px] font-bold text-[#6E5A54]"
                    >
                        {{ tag }}
                    </span>
                </div>
            </div>

            <!-- Harga & Sisa Porsi -->
            <div class="mt-2.5">
                <div class="flex items-baseline gap-2">
                    <span class="text-base font-extrabold text-brand-maroon">
                        Rp{{ food.priceDiscount.toLocaleString('id-ID') }}
                    </span>
                    <span class="text-xs text-[#9E8B83] line-through font-medium">
                        Rp{{ food.priceOriginal.toLocaleString('id-ID') }}
                    </span>
                </div>

                <div class="mt-2 flex items-center justify-between border-t border-[#F5EFE6] pt-2 text-[11px] font-medium text-[#8C7A72]">
                    <span class="flex items-center gap-1 font-semibold text-[#6E5A54] truncate max-w-[125px]" :title="food.merchantZoneLabel || food.distance">
                        <svg class="h-3 w-3 text-brand-maroon shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                        <span class="truncate">{{ food.merchantZoneLabel || food.distance }}</span>
                    </span>

                    <span class="flex items-center gap-1 font-bold text-rose-700">
                        <svg class="h-3 w-3 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2c-.67 0-1.28.37-1.58.96C9.28 5.2 6.5 8.9 6.5 13c0 3.03 2.47 5.5 5.5 5.5s5.5-2.47 5.5-5.5c0-4.1-2.78-7.8-3.92-10.04A1.75 1.75 0 0012 2zm0 14.5c-1.93 0-3.5-1.57-3.5-3.5 0-1.74 1.15-3.8 2.37-5.55.44-.64 1.82-.64 2.26 0 1.22 1.75 2.37 3.81 2.37 5.55 0 1.93-1.57 3.5-3.5 3.5z" />
                        </svg>
                        <span>Sisa {{ food.stock }} porsi</span>
                    </span>
                </div>
            </div>

        </div>

    </div>
</template>
