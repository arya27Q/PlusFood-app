<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MahasiswaLayout from '@/Layouts/MahasiswaLayout.vue';
import FoodCard from '@/Components/Mahasiswa/FoodCard.vue';
import { sampleFoods, campusZones } from '@/Utils/mockFoods';
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
    foods: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const foodsList = computed(() => {
    return (props.foods && props.foods.length > 0) ? props.foods : sampleFoods;
});

const maxPrice = ref(props.filters?.max_price || 25000);
const selectedCategory = ref(props.filters?.category || 'Semua');
const selectedCampus = ref(props.filters?.campus || 'all');
const selectedDistance = ref('2 km');
const searchQuery = ref(props.filters?.q || '');
const isLoading = ref(true);
const fetchError = ref(null);

// Dietary & Pantangan Alergi Filters
const filterHalal = ref(true);
const filterVegetarian = ref(false);
const filterTidakPedas = ref(false);

// Safety & Quality Filters
const filterVerified = ref(true);
const filterCookedToday = ref(true);
const filterRating = ref(false);
const filterSealed = ref(false);

const categories = ['Nasi', 'Mie', 'Soto', 'Roti', 'Camilan', 'Minuman', 'Semua'];
const distances = ['500 m', '1 km', '2 km', '3 km'];

const activeZones = computed(() => campusZones.filter(c => !c.isComingSoon));
const comingSoonZones = computed(() => campusZones.filter(c => c.isComingSoon));

// Modal Partner Onboarding & Student Waitlist
const showPartnerModal = ref(false);
const partnerType = ref('merchant'); // 'merchant' | 'student'
const partnerForm = ref({
    name: '',
    businessOrCampus: '',
    whatsapp: '',
    notes: ''
});
const formSubmitted = ref(false);

const openPartnerModal = (type = 'merchant') => {
    partnerType.value = type;
    formSubmitted.value = false;
    partnerForm.value = {
        name: '',
        businessOrCampus: '',
        whatsapp: '',
        notes: ''
    };
    showPartnerModal.value = true;
};

const closePartnerModal = () => {
    showPartnerModal.value = false;
};

const submitPartnerForm = () => {
    if (!partnerForm.value.name || !partnerForm.value.whatsapp) return;
    formSubmitted.value = true;
};

// Custom Dropdown Kampus State
const isCampusDropdownOpen = ref(false);

const selectCampusZone = (id) => {
    selectedCampus.value = id;
    isCampusDropdownOpen.value = false;
};

const currentCampus = computed(() => {
    return campusZones.find(c => c.id === selectedCampus.value) || campusZones[0];
});

const resetFilters = () => {
    searchQuery.value = '';
    fetchError.value = null;
    maxPrice.value = 25000;
    selectedCategory.value = 'Semua';
    selectedCampus.value = 'all';
    isCampusDropdownOpen.value = false;
    selectedDistance.value = '2 km';
    filterHalal.value = true;
    filterVegetarian.value = false;
    filterTidakPedas.value = false;
    filterVerified.value = true;
    filterCookedToday.value = true;
    filterRating.value = false;
    filterSealed.value = false;
};

// Filtered Foods
const filteredFoods = computed(() => {
    return foodsList.value.filter(item => {
        // Filter search keyword
        if (searchQuery.value && searchQuery.value.trim() !== '') {
            const q = searchQuery.value.trim().toLowerCase();
            const matchName = item.name.toLowerCase().includes(q) || (item.shortName && item.shortName.toLowerCase().includes(q));
            const matchMerchant = item.merchant.toLowerCase().includes(q);
            const matchCategory = item.category.toLowerCase().includes(q);
            if (!matchName && !matchMerchant && !matchCategory) return false;
        }
        // Filter price
        if (item.priceDiscount > maxPrice.value) return false;
        // Filter category
        if (selectedCategory.value !== 'Semua' && item.category !== selectedCategory.value) return false;
        // Filter campus zone
        if (selectedCampus.value !== 'all' && item.campusZone !== selectedCampus.value) return false;
        // Checkboxes filters
        if (filterRating.value && item.rating < 4.8) return false;
        // Dietary & Pantangan Alergi
        if (filterHalal.value && !(item.dietaryTags?.includes('Halal') || item.tags?.some(t => t.includes('Halal')))) return false;
        if (filterVegetarian.value && !(item.dietaryTags?.includes('Vegetarian') || item.category === 'Roti' || item.category === 'Camilan')) return false;
        if (filterTidakPedas.value && (item.dietaryTags?.includes('Pedas') && !item.dietaryTags?.includes('Tidak Pedas'))) return false;
        return true;
    });
});

const isMobileFilterOpen = ref(false);

onMounted(() => {
    try {
        isLoading.value = true;
        setTimeout(() => {
            isLoading.value = false;
        }, 350);

        if (typeof window !== 'undefined') {
            const urlParams = new URLSearchParams(window.location.search);
            const q = urlParams.get('q');
            if (q) searchQuery.value = q;

            const savedPref = localStorage.getItem('plusfood_preferences');
            if (savedPref) {
                try {
                    const p = JSON.parse(savedPref);
                    if (p.campus && p.campus !== 'all') selectedCampus.value = p.campus;
                    if (p.radius) selectedDistance.value = p.radius;
                    if (p.dietary) {
                        const halalTag = p.dietary.find(d => d.id === 'halal');
                        if (halalTag) filterHalal.value = halalTag.selected;
                        const vegTag = p.dietary.find(d => d.id === 'vegetarian');
                        if (vegTag) filterVegetarian.value = vegTag.selected;
                        const spicyTag = p.dietary.find(d => d.id === 'tidak_pedas');
                        if (spicyTag) filterTidakPedas.value = spicyTag.selected;
                    }
                } catch (e) {}
            }
        }
    } catch (err) {
        fetchError.value = 'Gagal memuat katalog makanan. Coba lagi ↻';
        isLoading.value = false;
    }
});
</script>

<template>
    <MahasiswaLayout>
        <Head title="Katalog Surplus - PlusFood" />

        <!-- Mobile Filter Toggle Bar (< lg) -->
        <div class="lg:hidden flex items-center justify-between bg-white rounded-2xl p-3.5 border border-[#EAE0D5] shadow-xs mb-4">
            <div class="flex items-center gap-2.5">
                <div class="h-9 w-9 rounded-xl bg-brand-maroon/10 text-brand-maroon flex items-center justify-center font-bold shrink-0">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-black text-[#2D1B1E] truncate">Filter & Kategori</p>
                    <p class="text-[10px] text-[#8C7A72] truncate">{{ currentCampus.name }} · {{ selectedCategory }}</p>
                </div>
            </div>
            <button
                type="button"
                @click="isMobileFilterOpen = !isMobileFilterOpen"
                class="rounded-xl px-3.5 py-2 text-xs font-bold transition flex items-center gap-1.5 shrink-0 cursor-pointer shadow-2xs"
                :class="isMobileFilterOpen ? 'bg-brand-maroon text-white' : 'bg-[#FAF7F2] text-brand-maroon border border-[#EAE0D5]'"
            >
                <span>{{ isMobileFilterOpen ? 'Tutup Filter' : 'Buka Filter' }}</span>
                <svg class="h-3.5 w-3.5 transition-transform" :class="{ 'rotate-180': isMobileFilterOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- ================= SISI KIRI: PANEL FILTER PERSIS GAMBAR 2 ================= -->
            <div
                class="lg:col-span-3 rounded-[24px] bg-white p-6 border border-[#EAE0D5] shadow-sm space-y-6"
                :class="[ isMobileFilterOpen ? 'block' : 'hidden lg:block' ]"
            >
                
                <!-- Filter Header & Reset -->
                <div class="flex items-center justify-between pb-2 border-b border-[#F5EFE6]">
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-brand-maroon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        <h2 class="text-base font-extrabold text-[#2D1B1E]">Filter</h2>
                    </div>
                    <button @click="resetFilters" class="text-xs font-semibold text-[#8C7A72] hover:text-brand-maroon transition">
                        Reset
                    </button>
                </div>

                <!-- 1. Rentang Harga Slider -->
                <div>
                    <label class="block text-xs font-bold text-[#2D1B1E] mb-2">Rentang Harga</label>
                    <input
                        type="range"
                        min="5000"
                        max="25000"
                        step="1000"
                        v-model="maxPrice"
                        class="w-full accent-brand-maroon cursor-pointer"
                    />
                    <div class="mt-2 flex items-center justify-between text-[11px] font-semibold text-[#8C7A72]">
                        <span>Rp5.000</span>
                        <span class="rounded-full bg-brand-maroon px-2.5 py-0.5 text-white font-bold text-xs">
                            Rp{{ Number(maxPrice).toLocaleString('id-ID') }}
                        </span>
                        <span>Rp25.000</span>
                    </div>
                </div>

                <!-- 2. Kategori Pill Buttons -->
                <div>
                    <label class="block text-xs font-bold text-[#2D1B1E] mb-2">Kategori</label>
                    <div class="flex flex-wrap gap-1.5">
                        <button
                            v-for="cat in categories"
                            :key="cat"
                            @click="selectedCategory = cat"
                            class="rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                            :class="selectedCategory === cat ? 'bg-brand-maroon text-white shadow-xs font-bold' : 'bg-white text-slate-700 border border-slate-200 shadow-2xs hover:border-brand-maroon/50 hover:bg-slate-50'"
                        >
                            {{ cat }}
                        </button>
                    </div>
                </div>

                <!-- 3. Jarak & Lokasi Kampus Acuan (Surabaya) -->
                <div>
                    <label class="flex items-center gap-1.5 text-xs font-bold text-[#2D1B1E] mb-2">
                        <svg class="h-3.5 w-3.5 text-brand-maroon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                        Lokasi Kampus / Kota Acuan
                    </label>

                    <!-- Custom Dropdown Pilihan Kampus / Kota dengan Icon Vektor SVG -->
                    <div class="relative mb-2.5">
                        <button
                            type="button"
                            @click="isCampusDropdownOpen = !isCampusDropdownOpen"
                            class="w-full flex items-center justify-between rounded-xl border-2 bg-white py-2 px-3 text-xs font-bold transition shadow-2xs text-left"
                            :class="isCampusDropdownOpen ? 'border-brand-maroon ring-2 ring-brand-maroon/20 text-[#2D1B1E]' : 'border-slate-200 text-[#2D1B1E] hover:border-brand-maroon/50'"
                        >
                            <div class="flex items-center gap-2 truncate">
                                <!-- Icon Vektor SVG Khusus (Bukan Emoji) -->
                                <div v-if="currentCampus.isComingSoon" class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-800 border border-amber-300">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.63 8.41m5.96 5.96a14.926 14.926 0 01-5.84 2.58m-.12-8.54L4.8 3.59a2 2 0 00-2.83 2.83l4.83 4.83" />
                                    </svg>
                                </div>
                                <div v-else class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-rose-100 text-brand-maroon border border-rose-200">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v7" />
                                    </svg>
                                </div>
                                <span class="truncate font-extrabold text-[#2D1B1E]">{{ currentCampus.name }}</span>
                            </div>

                            <svg
                                class="h-4 w-4 text-[#8C7A72] shrink-0 transition-transform duration-200 ml-1"
                                :class="{ 'rotate-180 text-brand-maroon': isCampusDropdownOpen }"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Invisible backdrop to close on outside click -->
                        <div v-if="isCampusDropdownOpen" @click="isCampusDropdownOpen = false" class="fixed inset-0 z-20"></div>

                        <!-- Dropdown Menu List -->
                        <div
                            v-if="isCampusDropdownOpen"
                            class="absolute left-0 right-0 top-full mt-1.5 z-30 max-h-72 overflow-y-auto rounded-2xl bg-white border-2 border-slate-200 p-1.5 shadow-2xl space-y-2"
                        >
                            <!-- Group 1: Surabaya (Aktif) -->
                            <div>
                                <div class="px-2 py-1 flex items-center justify-between text-[10px] font-black tracking-wider uppercase text-slate-500">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="h-3 w-3 text-brand-maroon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        </svg>
                                        Kota Surabaya
                                    </span>
                                    <span class="rounded bg-emerald-100 px-1.5 py-0.5 text-[9px] font-extrabold text-emerald-800">
                                        Aktif
                                    </span>
                                </div>
                                <div class="space-y-0.5">
                                    <button
                                        v-for="c in activeZones"
                                        :key="c.id"
                                        type="button"
                                        @click="selectCampusZone(c.id)"
                                        class="w-full flex items-center justify-between rounded-xl px-2.5 py-2 text-xs font-bold text-left transition"
                                        :class="selectedCampus === c.id ? 'bg-brand-maroon text-white shadow-xs' : 'text-slate-800 hover:bg-slate-50'"
                                    >
                                        <span class="flex items-center gap-2 truncate">
                                            <svg
                                                class="h-3.5 w-3.5 shrink-0"
                                                :class="selectedCampus === c.id ? 'text-white' : 'text-brand-maroon'"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v7" />
                                            </svg>
                                            <span class="truncate">{{ c.name }}</span>
                                        </span>
                                        <svg v-if="selectedCampus === c.id" class="h-4 w-4 shrink-0 text-white" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Group 2: Kota Ekspansi (Segera Hadir) -->
                            <div class="border-t border-slate-100 pt-1">
                                <div class="px-2 py-1 flex items-center justify-between text-[10px] font-black tracking-wider uppercase text-slate-500">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="h-3 w-3 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.63 8.41m5.96 5.96a14.926 14.926 0 01-5.84 2.58m-.12-8.54L4.8 3.59a2 2 0 00-2.83 2.83l4.83 4.83" />
                                        </svg>
                                        Kota Ekspansi
                                    </span>
                                    <span class="rounded bg-amber-100 px-1.5 py-0.5 text-[9px] font-extrabold text-amber-800">
                                        Segera Hadir
                                    </span>
                                </div>
                                <div class="space-y-0.5">
                                    <button
                                        v-for="c in comingSoonZones"
                                        :key="c.id"
                                        type="button"
                                        @click="selectCampusZone(c.id)"
                                        class="w-full flex items-center justify-between rounded-xl px-2.5 py-2 text-xs font-bold text-left transition"
                                        :class="selectedCampus === c.id ? 'bg-brand-maroon text-white shadow-xs' : 'text-slate-800 hover:bg-slate-50'"
                                    >
                                        <span class="flex items-center gap-2 truncate">
                                            <svg
                                                class="h-3.5 w-3.5 shrink-0"
                                                :class="selectedCampus === c.id ? 'text-white' : 'text-amber-600'"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.63 8.41m5.96 5.96a14.926 14.926 0 01-5.84 2.58m-.12-8.54L4.8 3.59a2 2 0 00-2.83 2.83l4.83 4.83" />
                                            </svg>
                                            <span class="truncate">{{ c.name }}</span>
                                        </span>
                                        <span class="flex items-center gap-1.5">
                                            <span
                                                class="rounded px-1.5 py-0.5 text-[9px] font-extrabold"
                                                :class="selectedCampus === c.id ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-900 border border-amber-200'"
                                            >
                                                Rekrutmen
                                            </span>
                                            <svg v-if="selectedCampus === c.id" class="h-4 w-4 shrink-0 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Distance Radiuses -->
                    <div class="grid grid-cols-4 gap-1">
                        <button
                            v-for="d in distances"
                            :key="d"
                            @click="selectedDistance = d"
                            class="rounded-lg py-1.5 text-[11px] font-bold text-center border-2 transition"
                            :class="selectedDistance === d ? 'border-brand-maroon bg-brand-maroon text-white shadow-xs font-extrabold' : 'border-slate-200 bg-white text-slate-700 shadow-2xs hover:border-brand-maroon/50 hover:bg-slate-50'"
                        >
                            {{ d }}
                        </button>
                    </div>
                    <p class="mt-2 text-[10px] font-semibold text-slate-500 leading-tight">
                        {{ currentCampus.hint }}
                    </p>
                </div>

                <!-- 4. Preferensi Menu & Pantangan Alergi -->
                <div class="space-y-2.5 pt-2 border-t border-slate-100">
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-1.5 text-xs font-bold text-[#2D1B1E]">
                            <svg class="h-3.5 w-3.5 text-brand-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            Pantangan & Preferensi Diet
                        </label>
                        <span class="text-[10px] font-black text-white bg-emerald-600 px-2.5 py-0.5 rounded-lg shadow-2xs">
                            Aman Anak Kos
                        </span>
                    </div>

                    <div class="space-y-2">
                        <label
                            class="flex items-center justify-between p-2.5 rounded-xl border cursor-pointer transition"
                            :class="filterHalal ? 'bg-rose-50/50 border-brand-maroon/50 shadow-2xs' : 'bg-white border-slate-200 shadow-2xs hover:border-slate-300 hover:bg-slate-50/50'"
                        >
                            <span class="flex items-center gap-2 text-xs font-semibold text-slate-800">
                                <input type="checkbox" v-model="filterHalal" class="rounded border-slate-300 text-brand-maroon focus:ring-brand-maroon h-4 w-4" />
                                <span>Halal Tersertifikasi</span>
                            </span>
                            <span class="text-[10px] font-extrabold text-emerald-800 bg-emerald-100/80 px-2 py-0.5 rounded-md border border-emerald-200/60">100% Halal</span>
                        </label>

                        <label
                            class="flex items-center justify-between p-2.5 rounded-xl border cursor-pointer transition"
                            :class="filterVegetarian ? 'bg-rose-50/50 border-brand-maroon/50 shadow-2xs' : 'bg-white border-slate-200 shadow-2xs hover:border-slate-300 hover:bg-slate-50/50'"
                        >
                            <span class="flex items-center gap-2 text-xs font-semibold text-slate-800">
                                <input type="checkbox" v-model="filterVegetarian" class="rounded border-slate-300 text-brand-maroon focus:ring-brand-maroon h-4 w-4" />
                                <span>Vegetarian / Nabati</span>
                            </span>
                            <span class="text-[10px] font-extrabold text-emerald-800 bg-emerald-100/80 px-2 py-0.5 rounded-md border border-emerald-200/60">Tanpa Daging</span>
                        </label>

                        <label
                            class="flex items-center justify-between p-2.5 rounded-xl border cursor-pointer transition"
                            :class="filterTidakPedas ? 'bg-rose-50/50 border-brand-maroon/50 shadow-2xs' : 'bg-white border-slate-200 shadow-2xs hover:border-slate-300 hover:bg-slate-50/50'"
                        >
                            <span class="flex items-center gap-2 text-xs font-semibold text-slate-800">
                                <input type="checkbox" v-model="filterTidakPedas" class="rounded border-slate-300 text-brand-maroon focus:ring-brand-maroon h-4 w-4" />
                                <span>Tidak Pedas (Aman Lambung)</span>
                            </span>
                            <span class="text-[10px] font-extrabold text-sky-800 bg-sky-100/80 px-2 py-0.5 rounded-md border border-sky-200/60">Bebas Cabe</span>
                        </label>
                    </div>

                    <!-- Edukasi & Disclaimer Alergen Ringkas -->
                    <div class="rounded-xl bg-amber-50/80 p-2.5 border border-amber-200/90 text-amber-950 space-y-1">
                        <div class="flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 text-amber-700 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="16" x2="12" y2="12" />
                                <line x1="12" y1="8" x2="12.01" y2="8" />
                            </svg>
                            <span class="text-[11px] font-extrabold text-amber-900">Edukasi Alergi Mahasiswa</span>
                        </div>
                        <p class="text-[10px] text-amber-900 leading-relaxed font-medium">
                            Makanan dimasak di dapur UMKM bersama. Jika memiliki alergi berat (kacang/seafood), cek komposisi bahan di detail menu & tuliskan catatan saat checkout.
                        </p>
                    </div>
                </div>

                <!-- 5. Jaminan & Standar Mutu Checkboxes -->
                <div class="space-y-2 pt-2 border-t border-[#F5EFE6]">
                    <label class="block text-xs font-bold text-[#2D1B1E] mb-2">Jaminan & Standar Mutu</label>
                    
                    <label class="flex items-center gap-2 text-xs font-medium text-[#6E5A54] cursor-pointer">
                        <input type="checkbox" v-model="filterVerified" class="rounded border-[#D5C7BD] text-brand-maroon focus:ring-brand-maroon" />
                        <span>Mitra terverifikasi BEM / Kampus</span>
                    </label>
                    <label class="flex items-center gap-2 text-xs font-medium text-[#6E5A54] cursor-pointer">
                        <input type="checkbox" v-model="filterCookedToday" class="rounded border-[#D5C7BD] text-brand-maroon focus:ring-brand-maroon" />
                        <span>Dimasak hari ini (&lt; 6 jam)</span>
                    </label>
                    <label class="flex items-center gap-2 text-xs font-medium text-[#6E5A54] cursor-pointer">
                        <input type="checkbox" v-model="filterRating" class="rounded border-[#D5C7BD] text-brand-maroon focus:ring-brand-maroon" />
                        <span>Rating 4.8+</span>
                    </label>
                    <label class="flex items-center gap-2 text-xs font-medium text-[#6E5A54] cursor-pointer">
                        <input type="checkbox" v-model="filterSealed" class="rounded border-[#D5C7BD] text-brand-maroon focus:ring-brand-maroon" />
                        <span>Kemasan higienis & tersegel</span>
                    </label>
                </div>

                <!-- Tombol Terapkan Filter -->
                <div class="pt-2">
                    <button class="w-full rounded-xl bg-brand-maroon py-3 text-center text-xs font-bold text-white shadow-md shadow-brand-maroon/20 hover:bg-brand-maroon-hover transition">
                        Terapkan · {{ filteredFoods.length }} hasil
                    </button>
                </div>
            </div>

            <!-- ================= SISI KANAN: KATALOG ATAU COMING SOON ================= -->
            <div class="lg:col-span-9 space-y-6">
                
                <!-- 1. JIKA KOTA DALAM TAHAP COMING SOON / REKRUTMEN MITRA -->
                <div v-if="currentCampus.isComingSoon" class="space-y-6">
                    <!-- Hero Banner Rekrutmen -->
                    <div class="relative overflow-hidden rounded-[28px] bg-gradient-to-br from-[#4A1521] via-brand-maroon to-[#2D0D14] p-8 sm:p-10 text-white shadow-lg">
                        <div class="relative z-10 max-w-2xl space-y-4">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-400/20 px-3.5 py-1 text-xs font-bold text-amber-300 border border-amber-400/30">
                                <svg class="h-3.5 w-3.5 text-amber-300 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.63 8.41m5.96 5.96a14.926 14.926 0 01-5.84 2.58m-.12-8.54L4.8 3.59a2 2 0 00-2.83 2.83l4.83 4.83" />
                                </svg>
                                Ekspansi Kota Baru: Tahap Rekrutmen Mitra Pelopor
                            </span>
                            <h1 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight">
                                PlusFood Segera Hadir di {{ currentCampus.city }}!
                            </h1>
                            <p class="text-sm sm:text-base text-white/80 leading-relaxed font-medium">
                                Saat ini kami sedang membuka pendaftaran untuk UMKM kuliner di sekitar <span class="text-amber-300 font-bold">{{ currentCampus.featuredCampuses }}</span> sebelum pemesanan resmi dibuka untuk umum.
                            </p>
                            
                            <!-- Action Buttons CTA dengan SVG Icon -->
                            <div class="flex flex-wrap items-center gap-3 pt-2">
                                <button
                                    @click="openPartnerModal('merchant')"
                                    class="inline-flex items-center gap-2.5 rounded-xl bg-amber-400 px-5 py-3 text-xs sm:text-sm font-black text-stone-900 shadow-md hover:bg-amber-300 transition hover:scale-[1.02]"
                                >
                                    <div class="flex h-5 w-5 items-center justify-center rounded-lg bg-stone-900/10 text-stone-900 shrink-0">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <span>Daftarkan Usaha Kuliner Saya (Mitra)</span>
                                </button>
                                <button
                                    @click="openPartnerModal('student')"
                                    class="inline-flex items-center gap-2.5 rounded-xl bg-white/15 backdrop-blur-sm border border-white/25 px-5 py-3 text-xs sm:text-sm font-bold text-white hover:bg-white/25 transition hover:scale-[1.02]"
                                >
                                    <div class="flex h-5 w-5 items-center justify-center rounded-lg bg-white/20 text-white shrink-0">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v7" />
                                        </svg>
                                    </div>
                                    <span>Vote & Masuk Waitlist Mahasiswa</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Decorative background element -->
                        <div class="absolute -right-16 -top-16 h-80 w-80 rounded-full bg-rose-500/20 blur-3xl pointer-events-none" />
                    </div>

                    <!-- Progress Kuota & Kawasan Target -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Box Status Kuota Mitra -->
                        <div class="rounded-[24px] bg-white p-6 border-2 border-[#EAE0D5] shadow-xs space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-800 border-2 border-amber-300 shadow-2xs">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-[#2D1B1E]">Target Kuota Mitra Pelopor</h3>
                                        <p class="text-[11px] text-[#8C7A72]">Syarat rilis pemesanan publik</p>
                                    </div>
                                </div>
                                <span class="rounded-lg bg-amber-50 px-2.5 py-1 text-xs font-black text-amber-800 border border-amber-200">
                                    {{ Math.round((currentCampus.merchantCurrent / currentCampus.merchantTarget) * 100) }}%
                                </span>
                            </div>

                            <!-- Progress Bar -->
                            <div>
                                <div class="flex items-center justify-between text-xs font-bold text-[#2D1B1E] mb-1.5">
                                    <span>{{ currentCampus.merchantCurrent }} Mitra Terverifikasi</span>
                                    <span class="text-[#8C7A72]">Target {{ currentCampus.merchantTarget }} Warung</span>
                                </div>
                                <div class="h-3 w-full rounded-full bg-[#FAF7F2] border border-[#EAE0D5] overflow-hidden p-0.5">
                                    <div
                                        class="h-full rounded-full bg-gradient-to-r from-amber-400 to-emerald-500 transition-all duration-700"
                                        :style="{ width: `${(currentCampus.merchantCurrent / currentCampus.merchantTarget) * 100}%` }"
                                    />
                                </div>
                            </div>

                            <p class="text-xs text-[#6E5A54] leading-relaxed">
                                Kurang <strong class="text-brand-maroon">{{ currentCampus.merchantTarget - currentCampus.merchantCurrent }} mitra warung lagi</strong> agar katalog makanan surplus di {{ currentCampus.city }} dibuka untuk umum!
                            </p>
                        </div>

                        <!-- Box Dukungan Mahasiswa & Kawasan Prioritas -->
                        <div class="rounded-[24px] bg-white p-6 border-2 border-[#EAE0D5] shadow-xs space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800 border-2 border-emerald-300 shadow-2xs">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v7" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-[#2D1B1E]">Dukungan Mahasiswa</h3>
                                    <p class="text-[11px] text-[#8C7A72]">Permintaan rescue lokal</p>
                                </div>
                            </div>

                            <div class="p-3.5 rounded-xl bg-[#FAF7F2] border border-[#EAE0D5]">
                                <p class="text-xs font-bold text-[#2D1B1E] flex items-center gap-2">
                                    <svg class="h-4 w-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2c-.67 0-1.28.37-1.58.96C9.28 5.2 6.5 8.9 6.5 13c0 3.03 2.47 5.5 5.5 5.5s5.5-2.47 5.5-5.5c0-4.1-2.78-7.8-3.92-10.04A1.75 1.75 0 0012 2zm0 14.5c-1.93 0-3.5-1.57-3.5-3.5 0-1.74 1.15-3.8 2.37-5.55.44-.64 1.82-.64 2.26 0 1.22 1.75 2.37 3.81 2.37 5.55 0 1.93-1.57 3.5-3.5 3.5z" />
                                    </svg>
                                    <span>{{ currentCampus.votesCount }}+ Mahasiswa siap menyelamatkan makanan</span>
                                </p>
                                <p class="text-[11px] text-[#8C7A72] mt-1">
                                    Begitu kuota mitra terpenuhi, mahasiswa di waitlist akan langsung diundang dengan voucher diskon perdana.
                                </p>
                            </div>

                            <div>
                                <span class="text-[11px] font-bold text-[#8C7A72] uppercase tracking-wider block mb-1">Kawasan Prioritas Pilot:</span>
                                <p class="text-xs font-semibold text-[#2D1B1E] flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5 text-brand-maroon shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <circle cx="12" cy="11" r="3" stroke-width="2" />
                                    </svg>
                                    <span>{{ currentCampus.keyAreas }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- 3 Benefit Menjadi Mitra Pelopor -->
                    <div class="rounded-[28px] bg-[#FAF7F2] p-6 sm:p-7 border border-[#EAE0D5]">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-5">
                            <div>
                                <h3 class="text-base font-extrabold text-[#2D1B1E]">Mengapa UMKM Kuliner Perlu Daftar Jadi Mitra Pelopor?</h3>
                                <p class="text-xs text-[#8C7A72] mt-0.5">Keuntungan eksklusif khusus UMKM yang bergabung di periode awal</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 self-start rounded-full bg-amber-100 px-3 py-1 text-[11px] font-extrabold text-amber-800 border border-amber-300">
                                Benefit Eksklusif Mitra
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
                            <!-- Benefit 1 -->
                            <div class="rounded-[22px] bg-white p-5 sm:p-6 border border-[#EAE0D5] shadow-xs hover:shadow-md hover:-translate-y-1 transition duration-200 flex flex-col justify-between space-y-3">
                                <div>
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 shadow-xs mb-3.5">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-extrabold text-[#2D1B1E] leading-snug">0% Komisi (3 Bulan Pertama)</h4>
                                    <p class="text-xs text-[#6E5A54] leading-relaxed mt-2">
                                        Seluruh hasil penjualan surplus 100% masuk ke pemilik usaha tanpa potongan biaya aplikasi.
                                    </p>
                                </div>
                                <div class="pt-3 border-t border-[#F5EFE6] flex items-center gap-1.5 text-[11px] font-bold text-amber-700">
                                    <svg class="h-3.5 w-3.5 text-amber-600 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Keuntungan Bersih 100%</span>
                                </div>
                            </div>

                            <!-- Benefit 2 -->
                            <div class="rounded-[22px] bg-white p-5 sm:p-6 border border-[#EAE0D5] shadow-xs hover:shadow-md hover:-translate-y-1 transition duration-200 flex flex-col justify-between space-y-3">
                                <div>
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-600 border border-sky-200 shadow-xs mb-3.5">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-extrabold text-[#2D1B1E] leading-snug">Promosi ke Ribuan Mahasiswa</h4>
                                    <p class="text-xs text-[#6E5A54] leading-relaxed mt-2">
                                        Warung Anda langsung dikenal oleh mahasiswa kampus terdekat sebagai gerai kuliner ramah kantong.
                                    </p>
                                </div>
                                <div class="pt-3 border-t border-[#F5EFE6] flex items-center gap-1.5 text-[11px] font-bold text-sky-700">
                                    <svg class="h-3.5 w-3.5 text-sky-600 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Jangkauan Pasar Kampus</span>
                                </div>
                            </div>

                            <!-- Benefit 3 -->
                            <div class="rounded-[22px] bg-white p-5 sm:p-6 border border-[#EAE0D5] shadow-xs hover:shadow-md hover:-translate-y-1 transition duration-200 flex flex-col justify-between space-y-3">
                                <div>
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 shadow-xs mb-3.5">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-extrabold text-[#2D1B1E] leading-snug">Nol Makanan Terbuang (Zero Waste)</h4>
                                    <p class="text-xs text-[#6E5A54] leading-relaxed mt-2">
                                        Bahan dan masakan berkualitas yang berpotensi sisa diubah jadi pendapatan tambahan setiap malam.
                                    </p>
                                </div>
                                <div class="pt-3 border-t border-[#F5EFE6] flex items-center gap-1.5 text-[11px] font-bold text-emerald-700">
                                    <svg class="h-3.5 w-3.5 text-emerald-600 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Optimalisasi Stok & Cuan</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. JIKA KOTA AKTIF (SURABAYA) -->
                <template v-else>
                    <!-- Header Katalog & Sort Dropdown -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <div>
                            <h1 class="text-2xl font-black text-[#2D1B1E]">Katalog Surplus</h1>
                            <p class="text-xs font-medium text-[#8C7A72] mt-0.5">
                                {{ filteredFoods.length }} makanan tersedia · {{ currentCampus.name }} · di bawah Rp{{ Number(maxPrice).toLocaleString('id-ID') }}
                            </p>
                        </div>

                        <!-- Sort Pill Badge -->
                        <div class="inline-flex items-center gap-2 rounded-xl bg-[#F5EFE6] px-3.5 py-1.5 text-xs font-bold text-brand-maroon border border-[#EAE0D5] self-start sm:self-auto">
                            <span>Urutkan: Terdekat</span>
                        </div>
                    </div>

                    <!-- Search Input Bar (Fitur Pencarian Kata Kunci) -->
                    <div class="relative mb-6">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#9E8B83] pointer-events-none">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari makanan favorit, warung, atau kategori..."
                            class="w-full rounded-2xl border border-[#EAE0D5] bg-white py-3 pl-11 pr-10 text-xs font-medium text-[#2D1B1E] placeholder-[#9E8B83] transition focus:border-brand-maroon focus:outline-none shadow-2xs"
                        />
                        <button
                            v-if="searchQuery"
                            @click="searchQuery = ''"
                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-xs text-[#8C7A72] hover:text-[#2D1B1E]"
                            title="Hapus pencarian"
                        >
                            ✕
                        </button>
                    </div>

                    <!-- 1. LOADING SKELETON STATE (Modul 8 UX State) -->
                    <div v-if="isLoading" class="grid grid-cols-2 sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-6">
                        <div v-for="n in 6" :key="n" class="rounded-2xl sm:rounded-[24px] bg-white p-2.5 sm:p-4 border border-[#EAE0D5] space-y-3.5 animate-pulse shadow-2xs">
                            <div class="h-28 sm:h-44 w-full rounded-xl sm:rounded-2xl bg-stone-200"></div>
                            <div class="space-y-2">
                                <div class="h-3.5 sm:h-4 w-3/4 rounded-lg bg-stone-200"></div>
                                <div class="h-2.5 sm:h-3 w-1/2 rounded-lg bg-stone-100"></div>
                            </div>
                            <div class="flex justify-between items-center pt-2 sm:pt-3 border-t border-stone-100">
                                <div class="space-y-1">
                                    <div class="h-2.5 sm:h-3 w-10 sm:w-14 rounded bg-stone-100"></div>
                                    <div class="h-4 sm:h-5 w-16 sm:w-24 rounded-lg bg-stone-200"></div>
                                </div>
                                <div class="h-6 sm:h-8 w-14 sm:w-24 rounded-xl bg-stone-200"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. ERROR STATE (Modul 8 UX State) -->
                    <div v-else-if="fetchError" class="rounded-[24px] bg-white p-12 text-center border border-rose-200 shadow-sm space-y-3">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 mb-2">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-[#2D1B1E]">Gagal memuat data. Coba lagi ↻</h3>
                        <p class="text-xs text-[#8C7A72] max-w-sm mx-auto">{{ fetchError }}</p>
                        <button @click="resetFilters" class="mt-4 rounded-xl bg-brand-maroon px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-brand-maroon-hover transition">
                            Muat Ulang
                        </button>
                    </div>

                    <!-- 3. EMPTY STATE (Modul 8 UX State) -->
                    <div v-else-if="filteredFoods.length === 0" class="rounded-[24px] bg-white p-12 text-center border border-[#EAE0D5] space-y-3">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-rose-50 text-brand-maroon mb-2 text-2xl">
                            🔍
                        </div>
                        <h3 class="text-base font-bold text-[#2D1B1E]">Yah, produk tidak ditemukan 🔍</h3>
                        <p class="text-xs text-[#8C7A72] max-w-sm mx-auto">
                            Tidak ada menu surplus yang cocok dengan filter atau kata kunci "{{ searchQuery }}". Coba ubah kategori atau naikkan rentang harga.
                        </p>
                        <button @click="resetFilters" class="mt-4 rounded-xl bg-brand-maroon px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-brand-maroon-hover transition">
                            Reset Filter ↻
                        </button>
                    </div>

                    <!-- 4. GRID DATA DINAMIS -->
                    <div v-else class="grid grid-cols-2 sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-6">
                        <FoodCard
                            v-for="item in filteredFoods"
                            :key="item.id"
                            :food="item"
                        />
                    </div>
                </template>

            </div>

        </div>

        <!-- ================= MODAL REGISTRASI MITRA PELOPOR / WAITLIST ================= -->
        <div v-if="showPartnerModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div class="relative w-full max-w-lg rounded-[28px] bg-white p-6 sm:p-8 shadow-2xl border border-[#EAE0D5] space-y-5 animate-in fade-in zoom-in-95 duration-200">
                <!-- Close Button -->
                <button
                    @click="closePartnerModal"
                    class="absolute top-5 right-5 flex h-8 w-8 items-center justify-center rounded-full bg-[#FAF7F2] text-[#8C7A72] hover:text-[#2D1B1E] transition"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div v-if="!formSubmitted" class="space-y-4">
                    <!-- Header Modal -->
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-brand-maroon uppercase tracking-wide">
                            {{ currentCampus.city }} · Ekspansi PlusFood
                        </span>
                        <h3 class="text-xl font-black text-[#2D1B1E]">
                            {{ partnerType === 'merchant' ? 'Formulir Mitra Kuliner Pelopor' : 'Vote & Masuk Waitlist Mahasiswa' }}
                        </h3>
                        <p class="text-xs text-[#8C7A72]">
                            {{ partnerType === 'merchant' ? 'Daftarkan warung atau resto Anda untuk mendapatkan slot prioritas food rescue.' : 'Bantu percepat pembukaan PlusFood di kampus Anda!' }}
                        </p>
                    </div>

                    <!-- Role Selector Toggle -->
                    <div class="grid grid-cols-2 gap-2 p-1 rounded-xl bg-[#FAF7F2] border border-[#EAE0D5] text-xs font-bold">
                        <button
                            type="button"
                            @click="partnerType = 'merchant'"
                            class="py-2.5 px-3 rounded-lg transition flex items-center justify-center gap-2"
                            :class="partnerType === 'merchant' ? 'bg-brand-maroon text-white shadow-xs' : 'text-[#6E5A54] hover:bg-white'"
                        >
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Pemilik Usaha Kuliner</span>
                        </button>
                        <button
                            type="button"
                            @click="partnerType = 'student'"
                            class="py-2.5 px-3 rounded-lg transition flex items-center justify-center gap-2"
                            :class="partnerType === 'student' ? 'bg-brand-maroon text-white shadow-xs' : 'text-[#6E5A54] hover:bg-white'"
                        >
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v7" />
                            </svg>
                            <span>Mahasiswa / BEM</span>
                        </button>
                    </div>

                    <!-- Form Inputs -->
                    <form @submit.prevent="submitPartnerForm" class="space-y-3 pt-1">
                        <div>
                            <label class="block text-xs font-bold text-[#2D1B1E] mb-1">
                                {{ partnerType === 'merchant' ? 'Nama Pemilik / Pengelola' : 'Nama Lengkap Mahasiswa' }}
                            </label>
                            <input
                                type="text"
                                v-model="partnerForm.name"
                                required
                                placeholder="Contoh: Budi Santoso"
                                class="w-full rounded-xl border border-[#EAE0D5] bg-[#FAF7F2] px-3.5 py-2.5 text-xs font-medium text-[#2D1B1E] focus:border-brand-maroon focus:bg-white focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#2D1B1E] mb-1">
                                {{ partnerType === 'merchant' ? 'Nama Usaha / Warung / Resto' : 'Asal Kampus & Jurusan' }}
                            </label>
                            <input
                                type="text"
                                v-model="partnerForm.businessOrCampus"
                                required
                                :placeholder="partnerType === 'merchant' ? 'Contoh: Warung Barokah Suhat Malang' : 'Contoh: Universitas Brawijaya - Teknik'"
                                class="w-full rounded-xl border border-[#EAE0D5] bg-[#FAF7F2] px-3.5 py-2.5 text-xs font-medium text-[#2D1B1E] focus:border-brand-maroon focus:bg-white focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#2D1B1E] mb-1">
                                Nomor WhatsApp Aktif
                            </label>
                            <input
                                type="tel"
                                v-model="partnerForm.whatsapp"
                                required
                                placeholder="Contoh: 081234567890"
                                class="w-full rounded-xl border border-[#EAE0D5] bg-[#FAF7F2] px-3.5 py-2.5 text-xs font-medium text-[#2D1B1E] focus:border-brand-maroon focus:bg-white focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#2D1B1E] mb-1">
                                {{ partnerType === 'merchant' ? 'Alamat / Area Terdekat' : 'Pesan Tambahan (Opsional)' }}
                            </label>
                            <input
                                type="text"
                                v-model="partnerForm.notes"
                                :placeholder="partnerType === 'merchant' ? 'Contoh: Jl. Sigura-gura No. 12 (Dekat Gerbang UB)' : 'Ingin rekomendasi warung langganan kamu?'"
                                class="w-full rounded-xl border border-[#EAE0D5] bg-[#FAF7F2] px-3.5 py-2.5 text-xs font-medium text-[#2D1B1E] focus:border-brand-maroon focus:bg-white focus:outline-none"
                            />
                        </div>

                        <div class="pt-2">
                            <button
                                type="submit"
                                class="w-full rounded-xl bg-brand-maroon py-3 text-xs font-bold text-white shadow-md hover:bg-brand-maroon-hover transition"
                            >
                                {{ partnerType === 'merchant' ? 'Kirim Pendaftaran Mitra Pelopor' : 'Kirim Vote & Gabung Waitlist' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Success Feedback State -->
                <div v-else class="text-center py-6 space-y-4">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700 border-2 border-emerald-300 shadow-md">
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-[#2D1B1E]">
                        {{ partnerType === 'merchant' ? 'Pendaftaran Mitra Diterima!' : 'Vote Berhasil Dicatat!' }}
                    </h3>
                    <p class="text-xs text-[#6E5A54] leading-relaxed max-w-sm mx-auto">
                        {{ partnerType === 'merchant' 
                            ? 'Terima kasih telah bergabung sebagai pelopor food rescue di ' + currentCampus.city + '! Tim kemitraan PlusFood akan menghubungi WhatsApp Anda dalam 1x24 jam untuk verifikasi menu & jadwal operasional.'
                            : 'Terima kasih dukungannya! Anda telah masuk dalam daftar prioritas pertama yang akan menerima kode voucher saat PlusFood resmi meluncur di ' + currentCampus.city + '.' 
                        }}
                    </p>
                    <button
                        @click="closePartnerModal"
                        class="rounded-xl bg-brand-maroon px-6 py-2.5 text-xs font-bold text-white hover:bg-brand-maroon-hover transition"
                    >
                        Tutup Jendela
                    </button>
                </div>
            </div>
        </div>
    </MahasiswaLayout>
</template>
