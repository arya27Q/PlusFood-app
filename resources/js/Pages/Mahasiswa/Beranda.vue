<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MahasiswaLayout from '@/Layouts/MahasiswaLayout.vue';
import FoodCard from '@/Components/Mahasiswa/FoodCard.vue';
import { sampleFoods, campusZones } from '@/Utils/mockFoods';
import { ref, computed } from 'vue';
import { useAuth } from '@/Composables/useAuth';

const props = defineProps({
    foods: {
        type: Array,
        default: () => [],
    },
});

const auth = useAuth();
const searchQuery = ref('');
const selectedCampus = ref('unesa-ketintang');
const selectedRadius = ref('2 km');
const selectedFastFilter = ref('Semua');
const showLocationModal = ref(false);
const isLoading = ref(true);

const radiuses = ['500 m', '1 km', '2 km', '3 km', '5 km'];
const availableCampuses = computed(() => campusZones.filter(c => !c.isComingSoon));

const currentCampusLabel = computed(() => {
    const found = campusZones.find(c => c.id === selectedCampus.value);
    return found ? found.name : 'UNESA Ketintang';
});

const fastFilters = [
    'Semua',
    'Nasi',
    'Roti',
    'Soto',
    'Halal Only',
    'Bebas Kacang',
    '< Rp15.000'
];

const foodsList = computed(() => {
    return (props.foods && props.foods.length > 0) ? props.foods : sampleFoods;
});

const filteredFoods = computed(() => {
    return foodsList.value.filter(item => {
        // Search query filter
        if (searchQuery.value) {
            const q = searchQuery.value.toLowerCase();
            const name = (item.name || '').toLowerCase();
            const shortName = (item.shortName || '').toLowerCase();
            const merchantStr = typeof item.merchant === 'string' ? item.merchant : (item.merchant?.name || item.merchantName || '');
            const matchMerchant = merchantStr.toLowerCase().includes(q);
            const category = (item.category || '').toLowerCase();
            if (!name.includes(q) && !shortName.includes(q) && !matchMerchant && !category.includes(q)) return false;
        }

        // Campus zone filter if not all
        const itemZone = item.campusZone ?? item.campus_zone ?? item.merchant?.campus_zone;
        if (selectedCampus.value !== 'all' && itemZone !== selectedCampus.value) {
            const distKm = typeof item.distanceKm === 'number' ? item.distanceKm :
                           parseFloat(String(item.distance || '1.2').replace(',', '.')) || 1.2;
            const maxRadius = selectedRadius.value === '500 m' ? 0.5 :
                              selectedRadius.value === '1 km' ? 1.0 :
                              selectedRadius.value === '2 km' ? 2.0 :
                              selectedRadius.value === '3 km' ? 3.0 : 5.0;
            if (distKm > maxRadius) {
                return false;
            }
        }

        // Fast filter bar
        if (selectedFastFilter.value === 'Nasi' && item.category !== 'Nasi') return false;
        if (selectedFastFilter.value === 'Roti' && item.category !== 'Roti') return false;
        if (selectedFastFilter.value === 'Soto' && item.category !== 'Soto') return false;
        const price = Number(item.priceDiscount ?? item.price_discount ?? 0);
        if (selectedFastFilter.value === '< Rp15.000' && price >= 15000) return false;

        const rawDietTags = item.dietaryTags ?? item.dietary_tags ?? [];
        const rawTags = item.tags ?? [];
        const allTags = [...rawDietTags, ...rawTags].map(t => String(t).toLowerCase());

        if (selectedFastFilter.value === 'Halal Only') {
            const isHalal = allTags.some(t => t.includes('halal'));
            if (!isHalal) return false;
        }
        if (selectedFastFilter.value === 'Bebas Kacang') {
            const isNutFree = allTags.some(t => t.includes('bebas kacang')) || (!item.name.toLowerCase().includes('kacang') && !(item.description || '').toLowerCase().includes('kacang'));
            if (!isNutFree) return false;
        }

        return true;
    });
});

import { onMounted } from 'vue';
onMounted(() => {
    isLoading.value = true;
    setTimeout(() => {
        isLoading.value = false;
    }, 300);

    if (typeof window !== 'undefined') {
        const savedPref = localStorage.getItem('plusfood_preferences');
        if (savedPref) {
            try {
                const p = JSON.parse(savedPref);
                if (p.campus) selectedCampus.value = p.campus;
                if (p.radius) selectedRadius.value = p.radius;
            } catch (e) {}
        }
    }
});
</script>

<template>
    <MahasiswaLayout>
        <Head title="Beranda Mahasiswa - PlusFood" />

        <div class="space-y-7">
            
            <!-- ================= 1. HEADER SAMBUTAN & METRIK GAMIFIKASI ================= -->
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold text-[#8C7A72]">
                        <span>Rabu malam, 19.12 WIB</span>
                        <span>•</span>
                        <!-- Location & Radius Selector Pill Sesuai PRD/Spec (Interactive Button) -->
                        <button
                            type="button"
                            @click="showLocationModal = true"
                            class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1 border border-[#EAE0D5] text-[#2D1B1E] font-bold shadow-2xs hover:border-brand-maroon hover:shadow-xs transition cursor-pointer"
                            title="Klik untuk ubah lokasi kampus atau radius jangkauan"
                        >
                            <svg class="h-3.5 w-3.5 text-brand-maroon shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                            <span>{{ currentCampusLabel }} · Radius {{ selectedRadius }}</span>
                            <svg class="h-3 w-3 text-[#A8988F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>

                    <h1 class="mt-1.5 text-3xl sm:text-4xl font-extrabold tracking-tight text-[#2D1B1E] flex items-center gap-2">
                        Selamat Datang, {{ auth.user.value ? auth.user.value.name.split(' ')[0] : 'Raka' }}! 
                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-amber-100 text-amber-600 hover:rotate-12 transition-transform cursor-default">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11" />
                            </svg>
                        </span>
                    </h1>
                    <p class="mt-1 text-xs sm:text-sm font-medium text-[#6E5A54]">
                        Ada <strong class="text-brand-maroon font-bold">{{ filteredFoods.length }} porsi surplus</strong> ramah kantong siap diselamatkan di sekitarmu malam ini.
                    </p>
                </div>

                <!-- Metrik Gamifikasi Ringkas Sesuai Dokumen UI (Level Rescuer & Tabungan Emisi) -->
                <div class="flex items-center gap-3.5 rounded-[22px] bg-white p-4 shadow-xs border border-[#EAE0D5] lg:min-w-[340px]">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800 border-2 border-emerald-300 shadow-2xs">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.5 2 2 6.5 2 12c0 3.5 1.8 6.6 4.5 8.4C6.2 17 8 13.5 12 11c4-2.5 7.5-2 9.5-.5C21.8 8.5 17.5 2 12 2z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-md bg-emerald-100 px-2 py-0.5 text-[10px] font-extrabold text-emerald-800 border border-emerald-300/80">
                                Level 3: Green Rescuer
                            </span>
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                <svg class="h-3.5 w-3.5 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.5 2 2 6.5 2 12c0 3.5 1.8 6.6 4.5 8.4C6.2 17 8 13.5 12 11c4-2.5 7.5-2 9.5-.5C21.8 8.5 17.5 2 12 2z" />
                                </svg>
                                2.4 kg CO2e dicegah
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm font-black text-[#2D1B1E] mt-1">
                            14 porsi diselamatkan · <span class="text-brand-maroon">hemat Rp168.000</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- ================= 2. SEARCH BAR & QUICK ACCESS ================= -->
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#9E8B83] pointer-events-none">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari nasi ayam, soto koya, roti sobek, atau nama warung..."
                    class="w-full rounded-2xl border border-[#EAE0D5] bg-[#F5EFE6]/70 py-3.5 pl-12 pr-4 text-sm font-medium text-[#2D1B1E] placeholder-[#9E8B83] transition focus:border-brand-maroon focus:bg-white focus:outline-none"
                />
            </div>

            <!-- ================= 3. FILTER BAR CEPAT (HORIZONTAL SCROLL) SESUAI SPEC ================= -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                <span class="text-xs font-bold text-[#8C7A72] shrink-0 mr-1 flex items-center gap-1">
                    <svg class="h-3.5 w-3.5 text-brand-maroon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter Cepat:
                </span>
                <button
                    v-for="pill in fastFilters"
                    :key="pill"
                    @click="selectedFastFilter = pill"
                    class="rounded-xl px-3.5 py-1.5 text-xs font-bold whitespace-nowrap transition"
                    :class="selectedFastFilter === pill ? 'bg-brand-maroon text-white shadow-xs' : 'bg-white text-[#6E5A54] border border-[#EAE0D5] hover:bg-[#FAF7F2]'"
                >
                    {{ pill }}
                </button>
            </div>

            <!-- ================= 4. BANNER FLASH URGENSI & QUICK ACTIONS ================= -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
                
                <!-- Hero Banner Maroon: Flash Penyelamat Akhir Bulan (Span 8) -->
                <div class="relative overflow-hidden rounded-[26px] bg-gradient-to-br from-[#4A1521] via-brand-maroon to-[#2D0D14] p-8 text-white shadow-md lg:col-span-8 flex flex-col justify-between min-h-[220px]">
                    <!-- Background texture -->
                    <div class="absolute inset-0 opacity-20 mix-blend-overlay">
                        <img
                            src="https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=1000&q=80"
                            alt="Food Banner"
                            class="h-full w-full object-cover"
                        />
                    </div>

                    <div class="relative z-10 space-y-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-400/25 px-3 py-1 text-xs font-bold text-amber-300 border border-amber-400/40">
                                <svg class="h-3.5 w-3.5 text-amber-300" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2c-.67 0-1.28.37-1.58.96C9.28 5.2 6.5 8.9 6.5 13c0 3.03 2.47 5.5 5.5 5.5s5.5-2.47 5.5-5.5c0-4.1-2.78-7.8-3.92-10.04A1.75 1.75 0 0012 2zm0 14.5c-1.93 0-3.5-1.57-3.5-3.5 0-1.74 1.15-3.8 2.37-5.55.44-.64 1.82-.64 2.26 0 1.22 1.75 2.37 3.81 2.37 5.55 0 1.93-1.57 3.5-3.5 3.5z" />
                                </svg>
                                Flash Pickup: Tutup &lt; 45 Menit
                            </span>
                            <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-bold backdrop-blur-xs">
                                Diskon hingga 60%
                            </span>
                        </div>

                        <h2 class="text-3xl font-black tracking-tight sm:text-4xl leading-tight">
                            Penyelamat Akhir Bulan
                        </h2>
                        <p class="text-xs sm:text-sm text-white/85 max-w-lg font-medium leading-relaxed">
                            Makan malam hangat dari warung mitra favorit sekitar kampus. Ambil sebelum jam 20.30 WIB dengan diskon ekstrem.
                        </p>
                    </div>

                    <div class="relative z-10 pt-4 flex items-center gap-3">
                        <Link
                            :href="route('katalog')"
                            class="inline-flex items-center gap-2 rounded-xl bg-amber-400 px-5 py-2.5 text-xs font-extrabold text-stone-900 shadow-md hover:bg-amber-300 transition"
                        >
                            <span>Eksplorasi Katalog Lengkap</span>
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </Link>
                    </div>
                </div>

                <!-- 2 Quick Action Cards (Span 4) -->
                <div class="flex flex-col gap-4 lg:col-span-4 justify-between">
                    
                    <!-- Flash Pickup Card -->
                    <Link
                        :href="route('katalog')"
                        class="group flex items-center justify-between rounded-[22px] bg-white p-5 border border-[#EAE0D5] shadow-xs transition-all hover:border-brand-maroon/40 hover:shadow-md"
                    >
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 transition group-hover:scale-105 border border-rose-200">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10" stroke-width="2" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2" />
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-extrabold text-[#2D1B1E] group-hover:text-brand-maroon transition">Flash Pickup Malam</h3>
                                    <span class="rounded bg-rose-100 px-1.5 py-0.5 text-[9px] font-black text-rose-700">URGENT</span>
                                </div>
                                <p class="text-xs text-[#8C7A72]">8 warung tutup dalam 45 menit</p>
                            </div>
                        </div>
                        <svg class="h-4 w-4 text-[#A8988F] transition-transform group-hover:translate-x-1 group-hover:text-brand-maroon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>

                    <!-- Dekat Kos Kamu Card -->
                    <Link
                        :href="route('katalog')"
                        class="group flex items-center justify-between rounded-[22px] bg-white p-5 border border-[#EAE0D5] shadow-xs transition-all hover:border-brand-maroon/40 hover:shadow-md"
                    >
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 transition group-hover:scale-105 border border-amber-200">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-[#2D1B1E] group-hover:text-brand-maroon transition">Dekat Kos Kamu</h3>
                                <p class="text-xs text-[#8C7A72]">Maks. 15 menit jalan kaki (&lt; 2 km)</p>
                            </div>
                        </div>
                        <svg class="h-4 w-4 text-[#A8988F] transition-transform group-hover:translate-x-1 group-hover:text-brand-maroon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>

                </div>
            </div>

            <!-- ================= 5. SECTION REKOMENDASI MALAM INI ================= -->
            <div>
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-lg font-black text-[#2D1B1E]">Rekomendasi Malam Ini</h2>
                        <p class="text-xs text-[#8C7A72]">Pilihan porsi terbaik dengan diskon 40%–60%</p>
                    </div>
                    <Link :href="route('katalog')" class="text-xs font-bold text-brand-maroon hover:underline flex items-center gap-1">
                        <span>Lihat Semua Katalog</span>
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>
                </div>

                <!-- 1. LOADING SKELETON STATE (Modul 8 UX State) -->
                <div v-if="isLoading" class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5 gap-3 sm:gap-6">
                    <div v-for="n in 6" :key="n" class="rounded-2xl sm:rounded-[22px] bg-white p-2.5 sm:p-4 border border-[#EAE0D5] space-y-3.5 animate-pulse shadow-2xs">
                        <div class="h-28 sm:h-40 w-full rounded-xl sm:rounded-2xl bg-stone-200"></div>
                        <div class="space-y-2">
                            <div class="h-3.5 sm:h-4 w-3/4 rounded-lg bg-stone-200"></div>
                            <div class="h-2.5 sm:h-3 w-1/2 rounded-lg bg-stone-100"></div>
                        </div>
                        <div class="flex justify-between items-center pt-2 sm:pt-3 border-t border-stone-100">
                            <div class="space-y-1">
                                <div class="h-2.5 sm:h-3 w-10 sm:w-12 rounded bg-stone-100"></div>
                                <div class="h-4 sm:h-5 w-16 sm:w-20 rounded-lg bg-stone-200"></div>
                            </div>
                            <div class="h-6 sm:h-8 w-14 sm:w-20 rounded-xl bg-stone-200"></div>
                        </div>
                    </div>
                </div>

                <!-- 2. EMPTY STATE (Modul 8 UX State) -->
                <div v-else-if="filteredFoods.length === 0" class="rounded-[24px] bg-white p-12 text-center border border-[#EAE0D5] space-y-3">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 text-brand-maroon text-2xl">
                        🔍
                    </div>
                    <h3 class="text-base font-bold text-[#2D1B1E]">Yah, produk tidak ditemukan 🔍</h3>
                    <p class="text-xs text-[#8C7A72] max-w-sm mx-auto">
                        Coba sesuaikan kata kunci pencarian atau ubah tombol filter cepat di atas.
                    </p>
                    <button
                        @click="selectedFastFilter = 'Semua'; searchQuery = ''"
                        class="rounded-xl bg-brand-maroon px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-brand-maroon-hover transition"
                    >
                        Reset Filter Cepat ↻
                    </button>
                </div>

                <!-- 3. GRID DATA DINAMIS -->
                <div v-else class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5 gap-3 sm:gap-6">
                    <FoodCard
                        v-for="item in filteredFoods.slice(0, 10)"
                        :key="item.id"
                        :food="item"
                    />
                </div>
            </div>

            <!-- ================= MODAL LOCATION & RADIUS SELECTOR ================= -->
            <div
                v-if="showLocationModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 animate-page-enter"
            >
                <div class="relative w-full max-w-md rounded-[28px] bg-white p-6 sm:p-7 shadow-2xl border border-[#EAE0D5] space-y-5">
                    <!-- Close button -->
                    <button
                        @click="showLocationModal = false"
                        class="absolute top-5 right-5 flex h-7 w-7 items-center justify-center rounded-full bg-[#FAF7F2] text-gray-400 hover:text-gray-700 transition"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <div>
                        <div class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-2.5 py-0.5 text-[11px] font-bold text-brand-maroon mb-2">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                            <span>Location-Based Rescue</span>
                        </div>
                        <h3 class="text-lg font-black text-[#2D1B1E]">Pilih Kampus & Radius Lokasi</h3>
                        <p class="text-xs text-[#8C7A72]">Sesuaikan titik awal dan jarak tempuh makanan surplus terdekat.</p>
                    </div>

                    <!-- Pilihan Kampus Aktif -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-[#2D1B1E]">Kampus Acuan</label>
                        <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                            <button
                                v-for="campus in availableCampuses"
                                :key="campus.id"
                                type="button"
                                @click="selectedCampus = campus.id"
                                class="w-full flex items-center justify-between p-2.5 rounded-xl border text-xs font-bold text-left transition"
                                :class="selectedCampus === campus.id ? 'border-brand-maroon bg-[#F5EFE6] text-brand-maroon shadow-2xs' : 'border-[#EAE0D5] bg-[#FAF7F2] text-[#2D1B1E] hover:bg-white'"
                            >
                                <span class="truncate">{{ campus.name }}</span>
                                <svg v-if="selectedCampus === campus.id" class="h-4 w-4 text-brand-maroon shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Pilihan Radius -->
                    <div class="space-y-2 pt-1">
                        <label class="block text-xs font-bold text-[#2D1B1E]">Radius Maksimal (Jarak Tempuh)</label>
                        <div class="grid grid-cols-5 gap-1.5">
                            <button
                                v-for="r in radiuses"
                                :key="r"
                                type="button"
                                @click="selectedRadius = r"
                                class="py-2 rounded-xl border text-xs font-bold text-center transition"
                                :class="selectedRadius === r ? 'border-brand-maroon bg-brand-maroon text-white shadow-xs' : 'border-[#EAE0D5] bg-[#FAF7F2] text-[#6E5A54] hover:bg-white'"
                            >
                                {{ r }}
                            </button>
                        </div>
                    </div>

                    <!-- Tombol Terapkan -->
                    <div class="pt-2">
                        <button
                            type="button"
                            @click="showLocationModal = false"
                            class="w-full rounded-xl bg-brand-maroon py-3 text-xs font-bold text-white shadow-md hover:bg-brand-maroon-hover transition"
                        >
                            Terapkan Lokasi ({{ currentCampusLabel }} · Radius {{ selectedRadius }})
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </MahasiswaLayout>
</template>
