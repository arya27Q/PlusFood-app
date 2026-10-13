<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MahasiswaLayout from '@/Layouts/MahasiswaLayout.vue';
import { sampleFoods } from '@/Utils/mockFoods';
import { ref } from 'vue';
import { useAuth } from '@/Composables/useAuth';

const auth = useAuth();
const searchQuery = ref('');
const foods = ref(sampleFoods);
</script>

<template>
    <MahasiswaLayout>
        <Head title="Beranda Mahasiswa - PlusFood" />

        <div class="space-y-8">
            
            <!-- HEADER SAMBUTAN & METRIK PENYELAMATAN -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <span class="text-xs font-semibold text-[#8C7A72]">
                        Rabu malam, 19.12 WIB · Jaringan Kampus Surabaya
                    </span>
                    <h1 class="mt-1 text-3xl sm:text-4xl font-extrabold tracking-tight text-[#2D1B1E] flex items-center gap-2">
                        Halo, {{ auth.user.value ? auth.user.value.name.split(' ')[0] : 'Raka' }}! <span class="text-3xl">👋</span>
                    </h1>
                    <p class="mt-1 text-sm font-medium text-[#6E5A54]">
                        Ada 24 porsi surplus di bawah Rp15.000 tersebar di berbagai sentra kampus Surabaya.
                    </p>
                </div>

                <!-- Card Metrik Gamifikasi Pojok Kanan -->
                <div class="flex items-center gap-3.5 rounded-2xl bg-white p-4 shadow-sm border border-[#EAE0D5] sm:min-w-[280px]">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.5 2 2 6.5 2 12c0 3.5 1.8 6.6 4.5 8.4C6.2 17 8 13.5 12 11c4-2.5 7.5-2 9.5-.5C21.8 8.5 17.5 2 12 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-[#8C7A72]">Kamu sudah menyelamatkan</p>
                        <p class="text-base font-extrabold text-[#2D1B1E]">
                            14 porsi · <span class="text-brand-maroon">hemat Rp168.000</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- SEARCH INPUT JUMBO -->
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#9E8B83] pointer-events-none">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari nasi, soto, roti, gorengan..."
                    class="w-full rounded-2xl border border-[#EAE0D5] bg-[#F5EFE6]/70 py-3.5 pl-12 pr-4 text-sm font-medium text-[#2D1B1E] placeholder-[#9E8B83] transition focus:border-brand-maroon focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-maroon/20"
                />
            </div>

            <!-- HERO BANNER PROMO & QUICK ACCESS CARDS -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
                
                <!-- Hero Banner Maroon: Penyelamat Akhir Bulan (Span 8) -->
                <div class="relative overflow-hidden rounded-[24px] bg-brand-maroon p-8 text-white shadow-md lg:col-span-8 flex flex-col justify-between min-h-[220px]">
                    <!-- Background texture -->
                    <div class="absolute inset-0 opacity-20 mix-blend-overlay">
                        <img
                            src="https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=1000&q=80"
                            alt="Food Banner"
                            class="h-full w-full object-cover"
                        />
                    </div>

                    <div class="relative z-10">
                        <span class="inline-block rounded-lg bg-white/20 px-3 py-1 text-xs font-bold backdrop-blur-sm">
                            Promo s/d 31 Okt
                        </span>
                        <h2 class="mt-4 text-3xl font-black tracking-tight sm:text-4xl">
                            Penyelamat<br />Akhir Bulan
                        </h2>
                        <p class="mt-2 text-sm text-white/85 max-w-md font-medium">
                            Diskon hingga 60% untuk makan malam hangat. Dompet tipis, perut tetap kenyang.
                        </p>
                    </div>
                </div>

                <!-- 2 Quick Action Cards (Span 4) -->
                <div class="flex flex-col gap-4 lg:col-span-4 justify-between">
                    
                    <!-- Flash Pickup Card -->
                    <Link
                        :href="route('katalog')"
                        class="group flex items-center justify-between rounded-[22px] bg-white p-5 border border-[#EAE0D5] shadow-sm transition-all hover:border-brand-maroon/40 hover:shadow-md"
                    >
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 transition group-hover:scale-105">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10" stroke-width="2" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-[#2D1B1E] group-hover:text-brand-maroon transition">Flash Pickup 19.30</h3>
                                <p class="text-xs text-[#8C7A72]">8 warung tutup dalam 1 jam</p>
                            </div>
                        </div>
                        <svg class="h-4 w-4 text-[#A8988F] transition-transform group-hover:translate-x-1 group-hover:text-brand-maroon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>

                    <!-- Dekat Kos Kamu Card -->
                    <Link
                        :href="route('katalog')"
                        class="group flex items-center justify-between rounded-[22px] bg-white p-5 border border-[#EAE0D5] shadow-sm transition-all hover:border-brand-maroon/40 hover:shadow-md"
                    >
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 transition group-hover:scale-105">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-[#2D1B1E] group-hover:text-brand-maroon transition">Dekat Kos Kamu</h3>
                                <p class="text-xs text-[#8C7A72]">Maks. 15 menit jalan kaki</p>
                            </div>
                        </div>
                        <svg class="h-4 w-4 text-[#A8988F] transition-transform group-hover:translate-x-1 group-hover:text-brand-maroon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>

                </div>
            </div>

            <!-- SECTION REKOMENDASI MALAM INI -->
            <div>
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-extrabold text-[#2D1B1E]">Rekomendasi Malam Ini</h2>
                    <Link :href="route('katalog')" class="text-xs font-bold text-brand-maroon hover:underline">
                        Lihat semua
                    </Link>
                </div>

                <!-- Grid Kartu Makanan (4 Kolom Sesuai Mockup Gambar 1) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div
                        v-for="item in foods.slice(0, 4)"
                        :key="item.id"
                        class="group rounded-[22px] bg-white border border-[#EAE0D5] p-3 shadow-sm transition-all hover:shadow-lg hover:border-brand-maroon/30 flex flex-col justify-between"
                    >
                        <!-- Image Container with Discount & Favorite -->
                        <div class="relative aspect-[16/10] w-full overflow-hidden rounded-2xl bg-[#FAF7F2]">
                            <img
                                :src="item.image"
                                :alt="item.name"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            />
                            <!-- Discount Badge -->
                            <span class="absolute top-2.5 left-2.5 rounded-lg bg-rose-600 px-2.5 py-1 text-[11px] font-extrabold text-white shadow-sm">
                                Diskon {{ item.discountPercent }}%
                            </span>
                            <!-- Heart Bookmark Button -->
                            <button class="absolute top-2.5 right-2.5 flex h-7 w-7 items-center justify-center rounded-full bg-white/90 text-gray-500 shadow-sm backdrop-blur-sm transition hover:text-rose-500 hover:scale-110">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>
                        </div>

                        <!-- Card Content -->
                        <div class="mt-3 flex-1 flex flex-col justify-between">
                            <div>
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-[#8C7A72]">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span>{{ item.merchant }}</span>
                                </p>
                                <Link :href="route('makanan.detail', item.slug)">
                                    <h3 class="mt-1 text-sm font-bold text-[#2D1B1E] line-clamp-1 hover:text-brand-maroon transition">
                                        {{ item.shortName }}
                                    </h3>
                                </Link>
                            </div>

                            <!-- Pricing & Stock -->
                            <div class="mt-2.5">
                                <div class="flex items-baseline gap-2">
                                    <span class="text-base font-extrabold text-brand-maroon">
                                        Rp{{ item.priceDiscount.toLocaleString('id-ID') }}
                                    </span>
                                    <span class="text-xs text-[#9E8B83] line-through font-medium">
                                        Rp{{ item.priceOriginal.toLocaleString('id-ID') }}
                                    </span>
                                </div>

                                <div class="mt-2 flex items-center justify-between border-t border-[#F5EFE6] pt-2 text-[11px] font-medium text-[#8C7A72]">
                                    <span class="flex items-center gap-1 font-semibold text-[#6E5A54] truncate max-w-[130px]" :title="item.merchantZoneLabel">
                                        <svg class="h-3 w-3 text-brand-maroon shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        </svg>
                                        <span class="truncate">{{ item.merchantZoneLabel || item.distance }}</span>
                                    </span>
                                    <span class="flex items-center gap-1 font-bold text-rose-700">
                                        <svg class="h-3 w-3 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2c-.67 0-1.28.37-1.58.96C9.28 5.2 6.5 8.9 6.5 13c0 3.03 2.47 5.5 5.5 5.5s5.5-2.47 5.5-5.5c0-4.1-2.78-7.8-3.92-10.04A1.75 1.75 0 0012 2zm0 14.5c-1.93 0-3.5-1.57-3.5-3.5 0-1.74 1.15-3.8 2.37-5.55.44-.64 1.82-.64 2.26 0 1.22 1.75 2.37 3.81 2.37 5.55 0 1.93-1.57 3.5-3.5 3.5z" />
                                        </svg>
                                        Sisa {{ item.stock }} porsi
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </MahasiswaLayout>
</template>
