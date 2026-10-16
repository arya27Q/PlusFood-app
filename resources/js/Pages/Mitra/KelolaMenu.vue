<script setup>
import { Head } from '@inertiajs/vue3';
import MitraLayout from '@/Layouts/MitraLayout.vue';
import { sampleFoods, campusZones } from '@/Utils/mockFoods';
import { ref, computed } from 'vue';

// Zona kampus aktif di Surabaya (sinkron dengan filter mahasiswa)
const availableCampusZones = computed(() => campusZones.filter(z => !z.isComingSoon));

const isCampusDropdownOpen = ref(false);

const selectedCampusZone = computed(() => {
    return availableCampusZones.value.find(z => z.id === form.value.campusZoneId) || availableCampusZones.value[0] || { name: 'Pilih Zona Kampus' };
});

const selectCampusZone = (zoneId) => {
    form.value.campusZoneId = zoneId;
    isCampusDropdownOpen.value = false;
};

const getTagBadgeClass = (tag) => {
    const t = tag.toLowerCase();
    if (t.includes('halal')) return 'bg-emerald-600 text-white border-emerald-700';
    if (t.includes('kacang')) return 'bg-sky-600 text-white border-sky-700';
    if (t.includes('vegetarian') || t.includes('nabati')) return 'bg-teal-600 text-white border-teal-700';
    if (t.includes('gluten')) return 'bg-purple-600 text-white border-purple-700';
    return 'bg-slate-700 text-white border-slate-800';
};

// Listing lokal yang aktif untuk gerai Warung Bu Sari
const activeListings = ref([
    {
        id: 101,
        name: 'Nasi Ayam Bakar Bumbu Rujak Spesial',
        category: 'Nasi',
        campusZoneId: 'unesa-ketintang',
        campusZoneName: 'UNESA Ketintang',
        canteenLocation: 'Kantin Vokasi Lt. 1 Stand 04',
        priceOriginal: 25000,
        priceDiscount: 12000,
        stock: 3,
        pickupSession: 'malam',
        pickupStart: '19.30',
        pickupEnd: '20.30',
        status: 'aktif',
        tags: ['Halal', 'Bebas Kacang'],
        spiciness: 'Pedas Sedang',
        supportEcoPackaging: true,
        image: 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=500&q=80'
    },
    {
        id: 102,
        name: 'Sop Ayam & Sayur Segar Warung',
        category: 'Soto',
        campusZoneId: 'unesa-ketintang',
        campusZoneName: 'UNESA Ketintang',
        canteenLocation: 'Kantin Vokasi Lt. 1 Stand 04',
        priceOriginal: 18000,
        priceDiscount: 9000,
        stock: 2,
        pickupSession: 'sore',
        pickupStart: '19.00',
        pickupEnd: '20.30',
        status: 'aktif',
        tags: ['Halal', 'Bebas Kacang', 'Bebas Gluten'],
        spiciness: 'Tidak Pedas',
        supportEcoPackaging: true,
        image: 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=500&q=80'
    }
]);

// Form Model Input Kilat yang Selaras dengan Filter Katalog Mahasiswa
const form = ref({
    name: '',
    category: 'Nasi',
    campusZoneId: 'unesa-ketintang',
    canteenLocation: 'Kantin Vokasi Lt. 1 Stand 04',
    priceOriginal: 22000,
    priceDiscount: 11000,
    estimatedCost: 8000,
    stock: 4,
    pickupSession: 'malam', // 'siang', 'sore', 'malam', 'custom'
    pickupStart: '19.00',
    pickupEnd: '20.30',
    tags: ['Halal', 'Bebas Kacang'],
    spiciness: 'Tidak Pedas', // 'Tidak Pedas', 'Pedas Sedang', 'Sangat Pedas'
    supportEcoPackaging: true,
    ecoDiscount: 500,
    kitchenNote: 'Disimpan hangat di food warmer grade A sejak jam masak',
    image: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=500&q=80'
});

const isPublished = ref(false);

// Mengubah Jendela Waktu Ambil berdasarkan Preset Sesi
const setPickupSession = (session) => {
    form.value.pickupSession = session;
    if (session === 'siang') {
        form.value.pickupStart = '13.00';
        form.value.pickupEnd = '15.00';
    } else if (session === 'sore') {
        form.value.pickupStart = '16.30';
        form.value.pickupEnd = '18.00';
    } else if (session === 'malam') {
        form.value.pickupStart = '19.00';
        form.value.pickupEnd = '20.30';
    }
};

// Kalkulasi Diskon & Smart Recovery Assistant Sesuai PRD
const discountPercent = computed(() => {
    if (!form.value.priceOriginal || form.value.priceOriginal <= 0) return 0;
    const diff = form.value.priceOriginal - form.value.priceDiscount;
    return Math.max(0, Math.round((diff / form.value.priceOriginal) * 100));
});

const capitalRecoveryRate = computed(() => {
    if (!form.value.estimatedCost || form.value.estimatedCost <= 0) return 100;
    return Math.round((form.value.priceDiscount / form.value.estimatedCost) * 100);
});

// Form Guard Validasi
const isFormValid = computed(() => {
    return form.value.name.trim() !== '' &&
           form.value.priceDiscount > 0 &&
           form.value.priceDiscount < form.value.priceOriginal &&
           form.value.stock > 0 &&
           form.value.canteenLocation.trim() !== '';
});

// Preset Menu Cepat Sesuai Makanan di Kampus
const presetMenus = [
    { 
        name: 'Nasi Ayam Bakar Bumbu Rujak', 
        cat: 'Nasi', 
        orig: 25000, 
        disc: 12000, 
        cost: 9000, 
        session: 'malam',
        tags: ['Halal', 'Bebas Kacang'], 
        spiciness: 'Pedas Sedang',
        canteen: 'Kantin Vokasi Stand 04',
        image: 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=500&q=80'
    },
    { 
        name: 'Nasi Campur Bali Ayam Suwir', 
        cat: 'Nasi', 
        orig: 24000, 
        disc: 12000, 
        cost: 8500, 
        session: 'malam',
        tags: ['Halal'], 
        spiciness: 'Pedas Sedang',
        canteen: 'Pujasera Ketintang Stand 02',
        image: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=500&q=80'
    },
    { 
        name: 'Sop Ayam & Sayur Segar Warung', 
        cat: 'Soto', 
        orig: 18000, 
        disc: 9000, 
        cost: 6000, 
        session: 'sore',
        tags: ['Halal', 'Bebas Kacang', 'Bebas Gluten'], 
        spiciness: 'Tidak Pedas',
        canteen: 'Kantin Vokasi Stand 04',
        image: 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=500&q=80'
    },
    { 
        name: 'Nasi Pecel Telur Dadar Madiun', 
        cat: 'Nasi', 
        orig: 16000, 
        disc: 8000, 
        cost: 5500, 
        session: 'siang',
        tags: ['Halal', 'Vegetarian'], 
        spiciness: 'Pedas Sedang',
        canteen: 'Kantin Pusat Stand 08',
        image: 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=500&q=80'
    },
    { 
        name: 'Roti Bakar Bandung Cokelat Keju', 
        cat: 'Roti', 
        orig: 18000, 
        disc: 9000, 
        cost: 6500, 
        session: 'malam',
        tags: ['Halal', 'Vegetarian'], 
        spiciness: 'Tidak Pedas',
        canteen: 'Sentra Jajanan Malam',
        image: 'https://images.unsplash.com/photo-1509722747041-616f39b57569?auto=format&fit=crop&w=500&q=80'
    }
];

const applyPreset = (preset) => {
    form.value.name = preset.name;
    form.value.category = preset.cat;
    form.value.priceOriginal = preset.orig;
    form.value.priceDiscount = preset.disc;
    form.value.estimatedCost = preset.cost;
    form.value.tags = [...preset.tags];
    form.value.spiciness = preset.spiciness;
    form.value.canteenLocation = preset.canteen;
    form.value.image = preset.image;
    setPickupSession(preset.session);
};

const submitListing = () => {
    if (!isFormValid.value) return;

    const selectedZone = availableCampusZones.value.find(z => z.id === form.value.campusZoneId);

    activeListings.value.unshift({
        id: Date.now(),
        name: form.value.name,
        category: form.value.category,
        campusZoneId: form.value.campusZoneId,
        campusZoneName: selectedZone?.name || 'UNESA Ketintang',
        canteenLocation: form.value.canteenLocation,
        priceOriginal: Number(form.value.priceOriginal),
        priceDiscount: Number(form.value.priceDiscount),
        stock: Number(form.value.stock),
        pickupSession: form.value.pickupSession,
        pickupStart: form.value.pickupStart,
        pickupEnd: form.value.pickupEnd,
        status: 'aktif',
        tags: [...form.value.tags],
        spiciness: form.value.spiciness,
        supportEcoPackaging: form.value.supportEcoPackaging,
        image: form.value.image
    });

    isPublished.value = true;
    setTimeout(() => {
        isPublished.value = false;
        form.value.name = '';
    }, 2500);
};

const toggleStatus = (item) => {
    item.status = item.status === 'aktif' ? 'habis' : 'aktif';
};

const adjustStock = (item, delta) => {
    const next = item.stock + delta;
    if (next >= 0) {
        item.stock = next;
        if (next === 0) item.status = 'habis';
        else item.status = 'aktif';
    }
};
</script>

<template>
    <MitraLayout>
        <Head title="Kelola Menu Surplus Kilat - PlusFood Mitra" />

        <div class="space-y-7">
            
            <!-- Header Halaman -->
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-black text-slate-900">Kelola & Input Surplus Kilat</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        Posting sisa makanan layak konsumsi dalam kurang dari 1 menit, selaras dengan filter lokasi & pantangan mahasiswa.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="rounded-xl bg-emerald-600 text-white px-3.5 py-1.5 text-xs font-black shadow-xs">
                        {{ activeListings.filter(l => l.status === 'aktif').length }} Menu Sedang Tayang
                    </span>
                </div>
            </div>

            <!-- Toast Sukses Posting -->
            <div v-if="isPublished" class="rounded-2xl bg-emerald-600 p-4 text-white shadow-lg flex items-center justify-between animate-in fade-in zoom-in-95">
                <div class="flex items-center gap-3">
                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <div>
                        <p class="text-sm font-extrabold">Listing Berhasil Dipublikasikan!</p>
                        <p class="text-xs text-white/90">
                            Menu ini langsung muncul di katalog mahasiswa sesuai zona kampus dan pantangan alergi yang dipilih.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- ================= SISI KIRI: FORMULIR INPUT KILAT (Span 7) ================= -->
                <div class="lg:col-span-7 rounded-[26px] bg-white p-6 sm:p-7 border border-slate-200/90 shadow-sm space-y-6">
                    <div class="border-b border-slate-100 pb-3">
                        <span class="rounded bg-rose-50 px-2 py-0.5 text-[10px] font-black text-brand-maroon uppercase">
                            Input Singkat &lt; 1 Menit · Sinkron Katalog Mahasiswa
                        </span>
                        <h2 class="text-lg font-black text-slate-900 mt-1">Formulir Menu Surplus Baru</h2>
                        <p class="text-xs text-slate-500">
                            Lengkapi lokasi stand, sesi jam pickup, serta tag alergen agar mahasiswa mudah menemukan makanan Anda.
                        </p>
                    </div>

                    <!-- Pilihan Cepat Preset Menu -->
                    <div>
                        <label class="block text-xs font-bold text-slate-900 mb-2">Preset Menu Cepat (Klik untuk Isi Otomatis):</label>
                        <div class="flex flex-wrap gap-1.5">
                            <button
                                v-for="p in presetMenus"
                                :key="p.name"
                                type="button"
                                @click="applyPreset(p)"
                                class="rounded-xl bg-white hover:bg-slate-50 border border-slate-200/90 px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-2xs hover:border-brand-maroon transition cursor-pointer"
                            >
                                + {{ p.name }}
                            </button>
                        </div>
                    </div>

                    <form @submit.prevent="submitListing" class="space-y-4">
                        
                        <!-- 1. Nama Menu & Kategori -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-900 mb-1">Nama Makanan Surplus *</label>
                                <input
                                    type="text"
                                    v-model="form.name"
                                    required
                                    placeholder="Contoh: Nasi Ayam Bakar Bumbu Rujak"
                                    class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:ring-1 focus:ring-brand-maroon focus:outline-none shadow-2xs transition"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-900 mb-1">Kategori</label>
                                <select
                                    v-model="form.category"
                                    class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:ring-1 focus:ring-brand-maroon focus:outline-none shadow-2xs cursor-pointer transition"
                                >
                                    <option value="Nasi">Nasi / Berat</option>
                                    <option value="Soto">Soto / Kuah</option>
                                    <option value="Roti">Roti / Pastry</option>
                                    <option value="Camilan">Camilan</option>
                                    <option value="Minuman">Minuman</option>
                                </select>
                            </div>
                        </div>

                        <!-- 2. Lokasi Stand & Zona Kampus (Sinkron dengan Mahasiswa) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-4 rounded-2xl bg-slate-50/70 border border-slate-200/80">
                            <div>
                                <label class="block text-xs font-bold text-slate-900 mb-1 flex items-center gap-1.5">
                                    <div class="flex h-5 w-5 items-center justify-center rounded-md bg-rose-100 text-brand-maroon">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <span>Zona Kampus Mahasiswa *</span>
                                </label>
                                <div class="relative">
                                    <button
                                        type="button"
                                        @click="isCampusDropdownOpen = !isCampusDropdownOpen"
                                        class="w-full flex items-center justify-between rounded-xl border-2 bg-white py-2 px-3 text-xs font-bold transition shadow-2xs text-left"
                                        :class="isCampusDropdownOpen ? 'border-brand-maroon ring-2 ring-brand-maroon/20 text-slate-900' : 'border-slate-200 text-slate-900 hover:border-brand-maroon/50'"
                                    >
                                        <div class="flex items-center gap-2 truncate">
                                            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-rose-100 text-brand-maroon border border-rose-200">
                                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v7" />
                                                </svg>
                                            </div>
                                            <span class="truncate font-extrabold text-slate-900">{{ selectedCampusZone.name }}</span>
                                        </div>

                                        <svg
                                            class="h-4 w-4 text-slate-400 shrink-0 transition-transform duration-200 ml-1"
                                            :class="{ 'rotate-180 text-brand-maroon': isCampusDropdownOpen }"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <!-- Backdrop to close on click outside -->
                                    <div v-if="isCampusDropdownOpen" @click="isCampusDropdownOpen = false" class="fixed inset-0 z-20"></div>

                                    <!-- Dropdown Menu List -->
                                    <div
                                        v-if="isCampusDropdownOpen"
                                        class="absolute left-0 right-0 top-full mt-1.5 z-30 max-h-60 overflow-y-auto rounded-2xl bg-white border-2 border-slate-200 p-1.5 shadow-2xl space-y-1"
                                    >
                                        <div class="px-2 py-1 flex items-center justify-between text-[10px] font-black tracking-wider uppercase text-slate-500 border-b border-slate-100 pb-1">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="h-3 w-3 text-brand-maroon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                </svg>
                                                Kota Surabaya
                                            </span>
                                            <span class="rounded bg-emerald-100 px-1.5 py-0.2 text-[9px] font-extrabold text-emerald-800">
                                                Aktif
                                            </span>
                                        </div>
                                        <div class="space-y-0.5 pt-0.5">
                                            <button
                                                v-for="zone in availableCampusZones"
                                                :key="zone.id"
                                                type="button"
                                                @click="selectCampusZone(zone.id)"
                                                class="w-full flex items-center justify-between rounded-xl px-2.5 py-2 text-xs font-bold text-left transition cursor-pointer"
                                                :class="form.campusZoneId === zone.id ? 'bg-brand-maroon text-white shadow-xs' : 'text-slate-800 hover:bg-slate-50'"
                                            >
                                                <span class="flex items-center gap-2 truncate">
                                                    <svg
                                                        class="h-3.5 w-3.5 shrink-0"
                                                        :class="form.campusZoneId === zone.id ? 'text-white' : 'text-brand-maroon'"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                                                    >
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v7" />
                                                    </svg>
                                                    <span class="truncate">{{ zone.name }}</span>
                                                </span>
                                                <svg v-if="form.campusZoneId === zone.id" class="h-4 w-4 shrink-0 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-500 mt-1">Sesuai filter pencarian mahasiswa di katalog.</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-900 mb-1 flex items-center gap-1.5">
                                    <div class="flex h-5 w-5 items-center justify-center rounded-md bg-slate-200 text-slate-700">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <span>Nama Stand / Titik Ambil *</span>
                                </label>
                                <input
                                    type="text"
                                    v-model="form.canteenLocation"
                                    required
                                    placeholder="Contoh: Kantin Vokasi Lt. 1 Stand 04"
                                    class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:outline-none shadow-2xs"
                                />
                                <p class="text-[10px] text-slate-500 mt-1">Petunjuk lokasi agar mahasiswa tidak salah tempat.</p>
                            </div>
                        </div>
                        <!-- 3. Jendela Waktu Pengambilan (Pickup Window Preset) -->
                        <div class="space-y-2.5 p-4 rounded-2xl bg-slate-50/70 border border-slate-200/80">
                            <label class="block text-xs font-bold text-slate-900 flex items-center justify-between">
                                <span class="flex items-center gap-1.5">
                                    <div class="flex h-5 w-5 items-center justify-center rounded-md bg-slate-200 text-slate-700">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <circle cx="12" cy="12" r="9" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2" />
                                        </svg>
                                    </div>
                                    <span>Jendela Waktu Pengambilan (Pickup Window)</span>
                                </span>
                                <span class="text-[10px] text-brand-maroon font-extrabold uppercase">Toleransi 15 Mnt Sebelum Tutup</span>
                            </label>

                            <!-- Preset Sesi Tombol dengan SVG Modern -->
                            <div class="grid grid-cols-3 gap-2">
                                <button
                                    type="button"
                                    @click="setPickupSession('siang')"
                                    class="rounded-xl border p-2 text-center text-xs font-bold transition flex flex-col items-center justify-center gap-1 cursor-pointer"
                                    :class="form.pickupSession === 'siang' ? 'bg-brand-maroon text-white border-brand-maroon shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100/70'"
                                >
                                    <div class="flex items-center gap-1.5">
                                        <svg class="h-3.5 w-3.5" :class="form.pickupSession === 'siang' ? 'text-amber-300' : 'text-amber-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <circle cx="12" cy="12" r="4" />
                                            <path stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41" />
                                        </svg>
                                        <span>Sesi Siang</span>
                                    </div>
                                    <div class="text-[10px] opacity-80 font-normal">13.00 – 15.00</div>
                                </button>
                                <button
                                    type="button"
                                    @click="setPickupSession('sore')"
                                    class="rounded-xl border p-2 text-center text-xs font-bold transition flex flex-col items-center justify-center gap-1 cursor-pointer"
                                    :class="form.pickupSession === 'sore' ? 'bg-brand-maroon text-white border-brand-maroon shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100/70'"
                                >
                                    <div class="flex items-center gap-1.5">
                                        <svg class="h-3.5 w-3.5" :class="form.pickupSession === 'sore' ? 'text-amber-300' : 'text-orange-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v4m0 0l-2-2m2 2l2-2M4 17h16M7 21h10M6 13a6 6 0 0112 0" />
                                        </svg>
                                        <span>Sesi Sore</span>
                                    </div>
                                    <div class="text-[10px] opacity-80 font-normal">16.30 – 18.00</div>
                                </button>
                                <button
                                    type="button"
                                    @click="setPickupSession('malam')"
                                    class="rounded-xl border p-2 text-center text-xs font-bold transition flex flex-col items-center justify-center gap-1 cursor-pointer"
                                    :class="form.pickupSession === 'malam' ? 'bg-brand-maroon text-white border-brand-maroon shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100/70'"
                                >
                                    <div class="flex items-center gap-1.5">
                                        <svg class="h-3.5 w-3.5" :class="form.pickupSession === 'malam' ? 'text-amber-300' : 'text-indigo-600'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                        </svg>
                                        <span>Sesi Malam</span>
                                    </div>
                                    <div class="text-[10px] opacity-80 font-normal">19.00 – 20.30</div>
                                </button>
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Jam Mulai Ambil</label>
                                    <input
                                        type="text"
                                        v-model="form.pickupStart"
                                        placeholder="19.00"
                                        class="w-full rounded-xl border border-slate-200 bg-white p-2 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:outline-none shadow-2xs"
                                    />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Batas Akhir Ambil</label>
                                    <input
                                        type="text"
                                        v-model="form.pickupEnd"
                                        placeholder="20.30"
                                        class="w-full rounded-xl border border-slate-200 bg-white p-2 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:outline-none shadow-2xs"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- 4. Harga Normal, Harga Diskon & Modal Pokok -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-900 mb-1">Harga Normal (Rp) *</label>
                                <input
                                    type="number"
                                    v-model="form.priceOriginal"
                                    step="1000"
                                    required
                                    class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:outline-none shadow-2xs transition"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-900 mb-1">Harga Diskon (Rp) *</label>
                                <input
                                    type="number"
                                    v-model="form.priceDiscount"
                                    step="1000"
                                    required
                                    class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs font-bold text-brand-maroon focus:border-brand-maroon focus:outline-none shadow-2xs transition"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-900 mb-1">Modal Bahan Dasar</label>
                                <input
                                    type="number"
                                    v-model="form.estimatedCost"
                                    step="500"
                                    class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs font-bold text-slate-600 focus:border-brand-maroon focus:outline-none shadow-2xs transition"
                                />
                            </div>
                        </div>

                        <!-- Smart Break-Even Assistant Card -->
                        <div class="rounded-2xl bg-slate-50/90 p-4 border border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-2xs">
                            <div>
                                <span class="text-[10px] font-black uppercase text-slate-500 tracking-wider">
                                    Smart Break-Even Assistant
                                </span>
                                <p class="text-xs font-bold text-slate-900 mt-0.5">
                                    Diskon <strong class="text-rose-700 font-black">{{ discountPercent }}%</strong> · Pemulihan Modal: <strong class="text-emerald-700 font-black">{{ capitalRecoveryRate }}%</strong>
                                </p>
                                <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-1.5">
                                    <svg v-if="capitalRecoveryRate >= 100" class="h-3.5 w-3.5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <svg v-else class="h-3.5 w-3.5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <span>
                                        {{ capitalRecoveryRate >= 100 ? 'Modal aman 100% dan Anda masih mendapatkan margin tipis!' : 'Harga diskon menutup ' + capitalRecoveryRate + '% dari biaya bahan dasar Anda.' }}
                                    </span>
                                </p>
                            </div>
                            <span class="rounded-xl bg-brand-maroon text-white px-3 py-1.5 text-xs font-black self-start sm:self-center shadow-xs">
                                {{ discountPercent }}% OFF
                            </span>
                        </div>

                        <!-- 5. Sisa Porsi & Tingkat Kepedasan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-900 mb-1">Sisa Porsi (Stok) *</label>
                                <input
                                    type="number"
                                    v-model="form.stock"
                                    min="1"
                                    max="50"
                                    required
                                    class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:outline-none shadow-2xs transition"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-900 mb-1">Tingkat Kepedasan</label>
                                <select
                                    v-model="form.spiciness"
                                    class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:outline-none shadow-2xs cursor-pointer transition"
                                >
                                    <option value="Tidak Pedas">Tidak Pedas (Aman)</option>
                                    <option value="Pedas Sedang">Pedas Sedang</option>
                                    <option value="Sangat Pedas">Sangat Pedas</option>
                                </select>
                            </div>
                        </div>

                        <!-- 6. Tag Pantangan Alergi & Dietary (Lengkap Sesuai Mahasiswa) -->
                        <div class="rounded-2xl p-4 bg-slate-50/70 border border-slate-200/80 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-900">
                                    Tag Pantangan Alergi & Keamanan Konsumen:
                                </label>
                                <span class="text-[10px] text-slate-500">Centang yang sesuai bahan</span>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 text-xs">
                                <label class="flex items-center gap-2 p-2.5 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-brand-maroon transition shadow-2xs">
                                    <input type="checkbox" value="Halal" v-model="form.tags" class="rounded text-brand-maroon focus:ring-brand-maroon" />
                                    <span class="font-bold text-slate-900">Halal</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-brand-maroon transition shadow-2xs">
                                    <input type="checkbox" value="Bebas Kacang" v-model="form.tags" class="rounded text-brand-maroon focus:ring-brand-maroon" />
                                    <span class="font-bold text-slate-900">Bebas Kacang</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-brand-maroon transition shadow-2xs">
                                    <input type="checkbox" value="Bebas Gluten" v-model="form.tags" class="rounded text-brand-maroon focus:ring-brand-maroon" />
                                    <span class="font-bold text-slate-900">Bebas Gluten</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-brand-maroon transition shadow-2xs">
                                    <input type="checkbox" value="Bebas Seafood" v-model="form.tags" class="rounded text-brand-maroon focus:ring-brand-maroon" />
                                    <span class="font-bold text-slate-900">Bebas Seafood</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-brand-maroon transition shadow-2xs">
                                    <input type="checkbox" value="Bebas Produk Susu" v-model="form.tags" class="rounded text-brand-maroon focus:ring-brand-maroon" />
                                    <span class="font-bold text-slate-900">Bebas Susu/Dairy</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-brand-maroon transition shadow-2xs">
                                    <input type="checkbox" value="Vegetarian" v-model="form.tags" class="rounded text-brand-maroon focus:ring-brand-maroon" />
                                    <span class="font-bold text-slate-900">Vegetarian</span>
                                </label>
                            </div>

                            <p class="text-[10px] text-slate-500 italic pt-1">
                                ℹ️ Informasi pantangan ini akan menjadi dasar filter pencarian mahasiswa di katalog.
                            </p>
                        </div>

                        <!-- 7. Dukungan Eco-Packaging (Bawa Wadah Sendiri) -->
                        <div class="rounded-2xl p-4 bg-emerald-50/80 border border-emerald-200/90 flex items-center justify-between gap-3 shadow-2xs">
                            <div class="space-y-0.5">
                                <span class="text-[10px] font-black uppercase text-emerald-800 tracking-wider flex items-center gap-1.5">
                                    <div class="flex h-4 w-4 items-center justify-center rounded-full bg-emerald-600 text-white">
                                        <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span>Program Gerakan Ramah Lingkungan</span>
                                </span>
                                <p class="text-xs font-bold text-slate-900">
                                    Dukung Mahasiswa Bawa Wadah Makan Sendiri
                                </p>
                                <p class="text-[11px] text-slate-600">
                                    Mengurangi biaya kemasan mika plastik Anda dan mengurangi timbunan sampah kampus.
                                </p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" v-model="form.supportEcoPackaging" class="sr-only peer" />
                                <div class="w-11 h-6 bg-stone-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>

                        <!-- Tombol Submit Publikasi -->
                        <div class="pt-2">
                            <button
                                type="submit"
                                :disabled="!isFormValid"
                                class="w-full rounded-xl bg-brand-maroon py-3.5 text-xs font-black text-white shadow-md hover:bg-brand-maroon-hover transition disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
                            >
                                Publikasikan Surplus Sekarang ({{ form.stock }} Porsi)
                            </button>
                            <p v-if="!isFormValid" class="text-[10px] text-rose-600 mt-1.5 text-center">
                                * Pastikan nama menu, lokasi stand, dan harga terisi dengan benar.
                            </p>
                        </div>
                    </form>
                </div>

                <!-- ================= SISI KANAN: DAFTAR LISTING AKTIF HARI INI (Span 5) ================= -->
                <div class="lg:col-span-5 space-y-4">
                    <div class="rounded-[26px] bg-white p-6 border border-slate-200/90 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <div>
                                <h2 class="text-base font-black text-slate-900">Daftar Listing Aktif Hari Ini</h2>
                                <p class="text-[11px] text-slate-500">Tampil langsung di aplikasi mahasiswa</p>
                            </div>
                            <span class="rounded-lg bg-emerald-600 text-white px-2.5 py-1 text-xs font-black shadow-xs">
                                {{ activeListings.length }} Menu
                            </span>
                        </div>

                        <!-- List Items -->
                        <div class="space-y-3">
                            <div
                                v-for="item in activeListings"
                                :key="item.id"
                                class="rounded-2xl p-4 border border-slate-200/90 bg-white space-y-3.5 transition shadow-2xs hover:border-slate-300"
                                :class="{ 'opacity-60': item.status === 'habis' }"
                            >
                                <div class="flex items-start gap-3">
                                    <img :src="item.image" :alt="item.name" class="h-16 w-16 rounded-xl object-cover bg-slate-100 shrink-0 border border-slate-200" />
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <span class="rounded-md bg-rose-50 text-brand-maroon text-xs font-bold px-2 py-0.5">
                                                {{ item.category }}
                                            </span>
                                            <button
                                                @click="toggleStatus(item)"
                                                class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-bold transition shadow-xs cursor-pointer"
                                                :class="item.status === 'aktif' ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300'"
                                            >
                                                <span class="h-1.5 w-1.5 rounded-full" :class="item.status === 'aktif' ? 'bg-white animate-pulse' : 'bg-slate-400'"></span>
                                                <span>{{ item.status === 'aktif' ? 'TAYANG' : 'HABIS' }}</span>
                                            </button>
                                        </div>
                                        <h3 class="text-sm font-bold text-slate-900 truncate mt-1">{{ item.name }}</h3>
                                        <p class="text-xs font-extrabold text-brand-maroon mt-0.5">
                                            Rp{{ item.priceDiscount.toLocaleString('id-ID') }}
                                            <span class="text-xs text-slate-400 line-through font-normal ml-1.5">Rp{{ item.priceOriginal.toLocaleString('id-ID') }}</span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Detail Lokasi & Pantangan Alergi -->
                                <div class="rounded-xl bg-white p-3 border border-slate-200 shadow-2xs space-y-2.5">
                                    <div class="flex items-center justify-between text-slate-900 font-medium">
                                        <div class="flex items-center gap-1.5 min-w-0">
                                            <svg class="h-3.5 w-3.5 text-brand-maroon shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span class="truncate font-bold text-xs text-slate-900">{{ item.canteenLocation }}</span>
                                        </div>
                                        <span class="text-slate-500 text-xs shrink-0 ml-2 font-medium">{{ item.campusZoneName }}</span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 pt-0.5">
                                        <span
                                            v-for="t in item.tags"
                                            :key="t"
                                            class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-bold shadow-xs tracking-normal"
                                            :class="getTagBadgeClass(t)"
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full bg-white shrink-0"></span>
                                            <span>{{ t }}</span>
                                        </span>
                                        <span v-if="item.supportEcoPackaging" class="inline-flex items-center gap-1.5 rounded-full bg-emerald-600 text-white border border-emerald-700 px-3 py-1 text-xs font-bold shadow-xs tracking-normal">
                                            <svg class="h-3.5 w-3.5 text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>Wadah Sendiri OK</span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Stepper Stok Cepat & Rentang Waktu -->
                                <div class="flex items-center justify-between pt-1 border-t border-slate-100 text-xs">
                                    <div class="flex items-center gap-1.5 text-xs font-bold text-brand-maroon">
                                        <svg class="h-3.5 w-3.5 text-brand-maroon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="9" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2" />
                                        </svg>
                                        <span>Ambil: {{ item.pickupStart }}–{{ item.pickupEnd }} WIB</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-slate-600">Sisa:</span>
                                        <button
                                            @click="adjustStock(item, -1)"
                                            class="h-7 w-7 rounded-lg bg-white border border-slate-200 font-bold text-slate-800 text-sm hover:bg-slate-100 cursor-pointer shadow-2xs transition flex items-center justify-center"
                                        >
                                            -
                                        </button>
                                        <span class="font-extrabold text-sm text-slate-900 px-1">{{ item.stock }}</span>
                                        <button
                                            @click="adjustStock(item, 1)"
                                            class="h-7 w-7 rounded-lg bg-white border border-slate-200 font-bold text-slate-800 text-sm hover:bg-slate-100 cursor-pointer shadow-2xs transition flex items-center justify-center"
                                        >
                                            +
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </MitraLayout>
</template>
