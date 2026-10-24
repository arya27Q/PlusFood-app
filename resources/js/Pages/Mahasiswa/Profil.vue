<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MahasiswaLayout from '@/Layouts/MahasiswaLayout.vue';
import { useMahasiswaProfil } from '@/Composables/useMahasiswaProfil';

const props = defineProps({
    user: {
        type: Object,
        default: () => null,
    },
    preference: {
        type: Object,
        default: () => null,
    },
    favoriteStores: {
        type: Array,
        default: () => [],
    },
    completedOrdersCount: {
        type: Number,
        default: 0,
    },
});

const {
    auth,
    isEditing,
    saveToast,
    saveToastMessage,
    activeUser,
    profileForm,
    availableCampuses,
    selectedCampus,
    selectedRadius,
    radiuses,
    dietaryTags,
    toggleDietary,
    selectedPayment,
    favoriteStores,
    lastPasswordChange,
    showPasswordModal,
    showOldPassword,
    showNewPassword,
    showConfirmPassword,
    passwordError,
    passwordSuccessToast,
    passwordForm,
    openPasswordModal,
    handlePasswordSubmit,
    saveProfile,
    savePreferences,
    userStats,
} = useMahasiswaProfil(props);
</script>

<template>
    <MahasiswaLayout>
        <Head title="Profil Mahasiswa - PlusFood" />

        <div class="space-y-6 w-full animate-fade-in pb-12">
            
            <!-- Toast Feedback Penyimpanan -->
            <div
                v-if="saveToast"
                class="fixed top-20 right-6 z-50 rounded-2xl bg-emerald-900 text-white px-5 py-3 shadow-xl flex items-center gap-3 border border-emerald-500 animate-fade-in"
            >
                <div class="h-6 w-6 rounded-full bg-emerald-500/30 flex items-center justify-center text-emerald-300 shrink-0">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span class="text-xs font-bold">{{ saveToastMessage }}</span>
            </div>

            <!-- Toast Ganti Password -->
            <div
                v-if="passwordSuccessToast"
                class="fixed top-20 right-6 z-50 rounded-2xl bg-emerald-900 text-white px-5 py-3 shadow-xl flex items-center gap-3 border border-emerald-500 animate-fade-in"
            >
                <div class="h-6 w-6 rounded-full bg-emerald-500/30 flex items-center justify-center text-emerald-300 shrink-0">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <span class="text-xs font-bold">Password akun berhasil diubah!</span>
            </div>

            <!-- ================= DUA KOLOM UTAMA ================= -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- ================= KOLOM KIRI (Profil & Warung Favorit) ================= -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- 1. KARTU IDENTITAS & GREEN LEVEL (Presisi Mockup) -->
                    <div class="rounded-3xl bg-white border border-[#E8DFD8] shadow-sm overflow-hidden text-center">
                        
                        <!-- Header Banner Marun Solid -->
                        <div class="h-28 w-full relative" style="background-color: #7A0C2E;"></div>

                        <!-- Avatar Bulat Overlap dengan Border Putih Tebal -->
                        <div class="relative -mt-12 flex justify-center">
                            <div class="relative">
                                <div
                                    class="w-24 h-24 rounded-full text-white text-3xl font-black border-4 border-white shadow-md flex items-center justify-center select-none"
                                    style="background-color: #7A0C2E;"
                                >
                                    {{ profileForm.name.charAt(0).toUpperCase() }}
                                </div>
                                <button
                                    @click="isEditing = true"
                                    class="absolute bottom-0 right-0 w-7 h-7 rounded-full bg-white text-stone-600 border border-stone-200 flex items-center justify-center shadow-xs hover:bg-[#FAF7F2] transition active:scale-95 cursor-pointer"
                                    title="Ubah Foto Profil"
                                >
                                    <svg class="w-3.5 h-3.5 text-stone-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Info Nama, Prodi, & Level -->
                        <div class="px-6 pt-3 pb-6">
                            <h2 class="text-xl font-black text-[#2D1B1E] tracking-tight">
                                {{ profileForm.name }}
                            </h2>
                            
                            <!-- Prodi Mahasiswa dengan SVG Topi Toga -->
                            <p class="text-xs font-semibold text-stone-500 mt-1 flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 text-stone-400" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z" />
                                </svg>
                                <span>{{ profileForm.major }}</span>
                            </p>

                            <!-- Badge Level dengan Daun Hijau (Solid Full Green Box) -->
                            <div class="mt-3 flex justify-center">
                                <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl bg-emerald-600 text-white font-extrabold text-xs shadow-xs border border-emerald-700">
                                    <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1-2.3A9.49 9.49 0 0017 8z"/>
                                        <path d="M21 3c-5.52 0-10 4.48-10 10 0 .73.08 1.44.24 2.12C12.79 13.62 15 11 17 8c2-3 4-5 4-5z"/>
                                    </svg>
                                    <span>Green Rescuer · Level {{ userStats.level }}</span>
                                </span>
                            </div>

                            <!-- Progress Level Bar -->
                            <div class="mt-5 space-y-1.5 text-left">
                                <div class="flex items-center justify-between text-xs font-bold">
                                    <span class="text-stone-700">{{ userStats.points }} / {{ userStats.nextLevelPoints }} poin</span>
                                    <span style="color: #7A0C2E;">{{ userStats.pointsRemaining }} lagi ke Level {{ userStats.level + 1 }}</span>
                                </div>
                                <div class="w-full h-2 rounded-full bg-[#EAE0D5] overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500" :style="{ width: userStats.progressPercent + '%', backgroundColor: '#7A0C2E' }"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Metrik Dampak 3 Kolom dengan SVG Icons Khusus -->
                        <div class="grid grid-cols-3 border-t border-[#F5EFE6] bg-[#FCFAF7] divide-x divide-[#F5EFE6] py-4 px-2">
                            <!-- Metrik 1: Porsi Makanan -->
                            <div class="text-center">
                                <div class="flex justify-center mb-1">
                                    <svg class="w-5 h-5 text-[#7A0C2E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10c0 4.97 4.03 9 9 9s9-4.03 9-9H3z" />
                                        <path stroke-linecap="round" d="M8 20h8" />
                                        <path stroke-linecap="round" d="M12 4v3m-4-2v2m8-2v2" />
                                    </svg>
                                </div>
                                <p class="text-lg font-black text-[#2D1B1E] mt-0.5">{{ userStats.portions }}</p>
                                <p class="text-[11px] font-medium text-stone-400">porsi</p>
                            </div>

                            <!-- Metrik 2: Uang Dihemat -->
                            <div class="text-center">
                                <div class="flex justify-center mb-1">
                                    <svg class="w-5 h-5 text-[#7A0C2E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <p class="text-lg font-black text-[#2D1B1E] mt-0.5">{{ userStats.moneySaved }}</p>
                                <p class="text-[11px] font-medium text-stone-400">dihemat</p>
                            </div>

                            <!-- Metrik 3: Emisi CO2e -->
                            <div class="text-center">
                                <div class="flex justify-center mb-1">
                                    <svg class="w-5 h-5 text-[#1D6A3A]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-lg font-black text-[#2D1B1E] mt-0.5">{{ userStats.co2Saved }}</p>
                                <p class="text-[11px] font-medium text-stone-400">CO2e</p>
                            </div>
                        </div>
                    </div>

                    <!-- 2. KARTU WARUNG FAVORIT -->
                    <div class="rounded-3xl bg-white border border-[#E8DFD8] p-5 shadow-sm space-y-3.5">
                        <h3 class="text-sm font-black text-[#2D1B1E] flex items-center gap-2">
                            <svg class="w-4 h-4 text-rose-600" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            <span>Warung Favorit</span>
                        </h3>

                        <div v-if="favoriteStores && favoriteStores.length > 0" class="space-y-2">
                            <Link
                                v-for="(store, idx) in favoriteStores"
                                :key="idx"
                                :href="route('katalog')"
                                class="flex items-center justify-between p-2.5 rounded-2xl hover:bg-[#FAF7F2] transition border border-transparent hover:border-[#EAE0D5] group"
                            >
                                <div class="flex items-center gap-3">
                                    <img
                                        :src="store.image"
                                        :alt="store.name"
                                        class="w-11 h-11 rounded-xl object-cover border border-stone-200"
                                    />
                                    <div class="text-left">
                                        <p class="text-xs font-bold text-[#2D1B1E] group-hover:text-brand-maroon transition">
                                            {{ store.name }}
                                        </p>
                                        <p class="text-[11px] text-stone-400">
                                            {{ store.rescuedCount }}
                                        </p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-stone-300 group-hover:text-brand-maroon group-hover:translate-x-0.5 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </div>
                        <div v-else class="text-center py-5 px-3 rounded-2xl bg-[#FAF7F2] border border-dashed border-[#EAE0D5] space-y-1.5">
                            <p class="text-xs font-bold text-stone-600">Belum ada warung favorit</p>
                            <p class="text-[11px] text-stone-400">Warung yang makanannya kamu selamatkan akan otomatis tercatat di sini.</p>
                            <Link :href="route('katalog')" class="inline-block text-[11px] font-extrabold text-brand-maroon hover:underline pt-1">
                                Jelajahi Makanan →
                            </Link>
                        </div>
                    </div>

                </div>

                <!-- ================= KOLOM KANAN (Data Diri, Preferensi, Notifikasi, Keamanan) ================= -->
                <div class="lg:col-span-8 space-y-6">
                    
                    <!-- 1. KARTU DATA DIRI -->
                    <div class="rounded-3xl bg-white border border-[#E8DFD8] p-6 sm:p-7 shadow-sm space-y-5">
                        
                        <!-- Header Data Diri -->
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-black text-[#2D1B1E]">
                                Data Diri
                            </h3>
                            <button
                                v-if="!isEditing"
                                @click="isEditing = true"
                                class="inline-flex items-center gap-1.5 text-xs font-bold transition cursor-pointer"
                                style="color: #7A0C2E;"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                <span>Ubah</span>
                            </button>
                            <div v-else class="flex items-center gap-2">
                                <button
                                    @click="isEditing = false"
                                    class="rounded-xl px-3 py-1.5 text-xs font-bold text-stone-600 hover:bg-stone-100 transition"
                                >
                                    Batal
                                </button>
                                <button
                                    @click="saveProfile"
                                    class="rounded-xl px-4 py-1.5 text-xs font-bold text-white shadow-xs transition"
                                    style="background-color: #7A0C2E;"
                                >
                                    Simpan
                                </button>
                            </div>
                        </div>

                        <!-- Baris 1: Nama, NIM, No WA -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-stone-600 mb-1.5">
                                    Nama Lengkap
                                </label>
                                <input
                                    v-model="profileForm.name"
                                    :disabled="!isEditing"
                                    type="text"
                                    class="w-full rounded-xl border px-3.5 py-2.5 text-xs font-semibold text-slate-800 transition"
                                    :class="isEditing ? 'border-[#7A0C2E] bg-white ring-1 ring-[#7A0C2E]/20 focus:outline-none' : 'border-slate-200 bg-white text-slate-800 shadow-2xs cursor-default'"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-stone-600 mb-1.5 flex items-center justify-between">
                                    <span>NIM</span>
                                    <span class="text-[10px] font-bold text-stone-400 bg-stone-100 px-1.5 py-0.5 rounded-md">Opsional</span>
                                </label>
                                <input
                                    v-model="profileForm.nim"
                                    :disabled="!isEditing"
                                    type="text"
                                    placeholder="Opsional (boleh dikosongkan)"
                                    class="w-full rounded-xl border px-3.5 py-2.5 text-xs font-semibold text-slate-800 transition"
                                    :class="isEditing ? 'border-[#7A0C2E] bg-white ring-1 ring-[#7A0C2E]/20 focus:outline-none' : 'border-slate-200 bg-white text-slate-800 shadow-2xs cursor-default'"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-stone-600 mb-1.5">
                                    No. WhatsApp
                                </label>
                                <input
                                    v-model="profileForm.whatsapp"
                                    :disabled="!isEditing"
                                    type="text"
                                    class="w-full rounded-xl border px-3.5 py-2.5 text-xs font-semibold text-slate-800 transition"
                                    :class="isEditing ? 'border-[#7A0C2E] bg-white ring-1 ring-[#7A0C2E]/20 focus:outline-none' : 'border-slate-200 bg-white text-slate-800 shadow-2xs cursor-default'"
                                />
                            </div>
                        </div>

                        <!-- Baris 2: Email Kampus & Alamat Kos -->
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                            <div class="sm:col-span-5">
                                <label class="block text-xs font-bold text-stone-600 mb-1.5">
                                    Email Kampus
                                </label>
                                <input
                                    v-model="profileForm.email"
                                    :disabled="!isEditing"
                                    type="email"
                                    class="w-full rounded-xl border px-3.5 py-2.5 text-xs font-semibold text-slate-800 transition"
                                    :class="isEditing ? 'border-[#7A0C2E] bg-white ring-1 ring-[#7A0C2E]/20 focus:outline-none' : 'border-slate-200 bg-white text-slate-800 shadow-2xs cursor-default'"
                                />
                            </div>

                            <div class="sm:col-span-7">
                                <label class="block text-xs font-bold text-stone-600 mb-1.5">
                                    Alamat Kos
                                </label>
                                <input
                                    v-model="profileForm.address"
                                    :disabled="!isEditing"
                                    type="text"
                                    class="w-full rounded-xl border px-3.5 py-2.5 text-xs font-semibold text-slate-800 transition"
                                    :class="isEditing ? 'border-[#7A0C2E] bg-white ring-1 ring-[#7A0C2E]/20 focus:outline-none' : 'border-slate-200 bg-white text-slate-800 shadow-2xs cursor-default'"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- 2. KARTU PREFERENSI KAMPUS & KONSUMSI (SINKRON DENGAN MODEL LOKASI & RADIUS MOCKFOODS) -->
                    <div class="rounded-3xl bg-white border border-[#E8DFD8] p-6 sm:p-7 shadow-sm space-y-5">
                        
                        <!-- Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-[#F5EFE6] gap-2">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white shrink-0" style="background-color: #7A0C2E;">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <circle cx="12" cy="11" r="3" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-[#2D1B1E]">
                                        Preferensi Kampus & Konsumsi
                                    </h3>
                                    <p class="text-[11px] text-stone-400">
                                        Titik kampus acuan, radius makanan surplus, dan kebutuhan diet Anda.
                                    </p>
                                </div>
                            </div>
                            <span class="self-start sm:self-auto inline-flex items-center gap-1.5 text-[11px] font-black px-3 py-1.5 rounded-xl bg-emerald-600 text-white shadow-xs">
                                <svg class="h-3.5 w-3.5 text-white shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span>Tersimpan Otomatis</span>
                            </span>
                        </div>

                        <!-- Grid 2 Sub-kolom -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                            
                            <!-- SUB-KOLOM 1: Lokasi Kampus & Jangkauan -->
                            <div class="space-y-4">
                                <!-- Kampus Utama Acuan -->
                                <div>
                                    <label class="block text-xs font-bold text-stone-700 mb-1.5 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-[#7A0C2E]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                        </svg>
                                        <span>Kampus Utama (Titik Acuan)</span>
                                    </label>
                                    <div class="relative">
                                        <select
                                            v-model="selectedCampus"
                                            @change="savePreferences"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:border-[#7A0C2E] focus:outline-none shadow-2xs transition appearance-none cursor-pointer pr-9"
                                        >
                                            <option
                                                v-for="campus in availableCampuses"
                                                :key="campus.id"
                                                :value="campus.id"
                                            >
                                                {{ campus.name }} ({{ campus.hint.split('dari ')[1] || campus.city }})
                                            </option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-stone-400">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="text-[11px] text-stone-400 mt-1">
                                        Katalog & beranda akan memprioritaskan makanan surplus di sekitar kampus ini.
                                    </p>
                                </div>

                                <!-- Radius Jangkauan Default -->
                                <div>
                                    <label class="block text-xs font-bold text-stone-700 mb-1.5 flex items-center justify-between">
                                        <span>Radius Jangkauan Ambil</span>
                                        <span class="text-xs font-extrabold text-[#7A0C2E]">Maks. {{ selectedRadius }}</span>
                                    </label>
                                    <div class="grid grid-cols-5 gap-1.5">
                                        <button
                                            v-for="r in radiuses"
                                            :key="r"
                                            type="button"
                                            @click="selectedRadius = r; savePreferences()"
                                            class="py-2 rounded-xl text-xs font-bold transition border cursor-pointer"
                                            :class="selectedRadius === r ? 'text-white shadow-xs border-[#7A0C2E]' : 'bg-white text-slate-700 hover:bg-slate-50 border-slate-200 shadow-2xs'"
                                            :style="selectedRadius === r ? 'background-color: #7A0C2E;' : ''"
                                        >
                                            {{ r }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- SUB-KOLOM 2: Preferensi Diet & Metode Bayar -->
                            <div class="space-y-4">
                                <!-- Kebutuhan Diet -->
                                <div>
                                    <label class="block text-xs font-bold text-stone-700 mb-1.5 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-[#7A0C2E]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                        </svg>
                                        <span>Kebutuhan Diet & Pantangan</span>
                                    </label>
                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            v-for="tag in dietaryTags"
                                            :key="tag.id"
                                            @click="toggleDietary(tag)"
                                            type="button"
                                            class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold transition active:scale-95 border cursor-pointer"
                                            :class="tag.selected ? 'border-rose-300' : 'bg-white border-slate-200 text-slate-700 shadow-2xs hover:border-slate-300'"
                                            :style="tag.selected ? 'background-color: #FFF5F5; color: #7A0C2E;' : ''"
                                        >
                                            <svg v-if="tag.selected" class="w-3 h-3 text-[#7A0C2E] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>{{ tag.label }}</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Metode Pembayaran Utama -->
                                <div>
                                    <label class="block text-xs font-bold text-stone-700 mb-1.5 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-[#7A0C2E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="2" y="5" width="20" height="14" rx="2" />
                                            <line x1="2" y1="10" x2="22" y2="10" />
                                        </svg>
                                        <span>Metode Pembayaran Utama</span>
                                    </label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <button
                                            type="button"
                                            @click="selectedPayment = 'QRIS'; savePreferences()"
                                            class="flex items-center justify-center gap-2 rounded-xl py-2 px-3 text-xs font-bold transition border cursor-pointer"
                                            :class="selectedPayment === 'QRIS' ? 'border-rose-300 shadow-xs' : 'bg-white text-slate-700 border-slate-200 shadow-2xs hover:bg-slate-50'"
                                            :style="selectedPayment === 'QRIS' ? 'background-color: #FFF5F5; color: #7A0C2E;' : ''"
                                        >
                                            <span class="font-extrabold tracking-wide">QRIS</span>
                                            <span class="text-[10px] text-stone-400">(Instan)</span>
                                        </button>
                                        <button
                                            type="button"
                                            @click="selectedPayment = 'Tunai'; savePreferences()"
                                            class="flex items-center justify-center gap-2 rounded-xl py-2 px-3 text-xs font-bold transition border cursor-pointer"
                                            :class="selectedPayment === 'Tunai' ? 'border-rose-300 shadow-xs' : 'bg-white text-slate-700 border-slate-200 shadow-2xs hover:bg-slate-50'"
                                            :style="selectedPayment === 'Tunai' ? 'background-color: #FFF5F5; color: #7A0C2E;' : ''"
                                        >
                                            <span>Tunai</span>
                                            <span class="text-[10px] text-stone-400">(Ambil di Tempat)</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- 3. KARTU KEAMANAN AKUN -->
                    <div class="rounded-3xl bg-white border border-[#E8DFD8] p-5 sm:p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                        
                        <!-- Left Info dengan Shield SVG -->
                        <div class="flex items-center gap-3.5 text-left w-full sm:w-auto">
                            <div class="w-12 h-12 rounded-2xl border border-amber-200 flex items-center justify-center shrink-0" style="background-color: #FFF9F2; color: #7A0C2E;">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-[#2D1B1E]">
                                    Keamanan Akun
                                </h4>
                                <p class="text-xs text-stone-400 mt-0.5">
                                    Password terakhir diganti: <span class="font-bold text-stone-600">{{ lastPasswordChange }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Right Actions: Ganti Password & Keluar -->
                        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                            <button
                                @click="openPasswordModal"
                                class="inline-flex items-center gap-2 rounded-xl border-2 px-4 py-2 text-xs font-bold bg-white hover:bg-rose-50 transition active:scale-95 shadow-xs cursor-pointer"
                                style="border-color: #7A0C2E; color: #7A0C2E;"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>Ganti Password</span>
                            </button>

                            <button
                                @click="auth.logout()"
                                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-rose-700 hover:text-rose-900 transition hover:bg-rose-50 rounded-xl cursor-pointer"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Keluar</span>
                            </button>
                        </div>
                    </div>

                </div>

            </div>

            <!-- ================= MODAL GANTI PASSWORD ================= -->
            <div
                v-if="showPasswordModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 animate-fade-in"
            >
                <div class="w-full max-w-sm rounded-[24px] bg-white p-6 shadow-2xl border border-[#E8DFD8] space-y-4">
                    <div class="flex items-center justify-between border-b border-[#F5EFE6] pb-3">
                        <div>
                            <h3 class="text-base font-black text-[#2D1B1E]">
                                Ganti Password Akun
                            </h3>
                            <p class="text-[11px] text-stone-400">
                                Masukkan password lama & baru Anda.
                            </p>
                        </div>
                        <button
                            @click="showPasswordModal = false"
                            class="text-stone-400 hover:text-stone-700 w-7 h-7 rounded-full hover:bg-stone-100 flex items-center justify-center transition cursor-pointer"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Alert Error jika ada -->
                    <div
                        v-if="passwordError"
                        class="rounded-xl bg-rose-50 border border-rose-200 p-2.5 text-xs text-rose-800 font-bold flex items-center gap-2"
                    >
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>{{ passwordError }}</span>
                    </div>

                    <form @submit.prevent="handlePasswordSubmit" class="space-y-3.5">
                        <!-- Password Lama -->
                        <div>
                            <label class="block text-xs font-bold text-stone-600 mb-1">
                                Password Lama
                            </label>
                            <div class="relative">
                                <input
                                    v-model="passwordForm.current_password"
                                    :type="showOldPassword ? 'text' : 'password'"
                                    required
                                    placeholder="••••••••"
                                    class="w-full rounded-xl border border-[#EAE0D5] px-3.5 py-2 text-xs font-semibold focus:border-[#7A0C2E] focus:outline-none pr-9"
                                />
                                <button
                                    type="button"
                                    @click="showOldPassword = !showOldPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-stone-400 hover:text-stone-600 transition cursor-pointer"
                                    tabindex="-1"
                                >
                                    <svg v-if="!showOldPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Password Baru -->
                        <div>
                            <label class="block text-xs font-bold text-stone-600 mb-1">
                                Password Baru
                            </label>
                            <div class="relative">
                                <input
                                    v-model="passwordForm.password"
                                    :type="showNewPassword ? 'text' : 'password'"
                                    required
                                    placeholder="Minimal 8 karakter"
                                    class="w-full rounded-xl border border-[#EAE0D5] px-3.5 py-2 text-xs font-semibold focus:border-[#7A0C2E] focus:outline-none pr-9"
                                />
                                <button
                                    type="button"
                                    @click="showNewPassword = !showNewPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-stone-400 hover:text-stone-600 transition cursor-pointer"
                                    tabindex="-1"
                                >
                                    <svg v-if="!showNewPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div>
                            <label class="block text-xs font-bold text-stone-600 mb-1">
                                Konfirmasi Password Baru
                            </label>
                            <div class="relative">
                                <input
                                    v-model="passwordForm.password_confirmation"
                                    :type="showConfirmPassword ? 'text' : 'password'"
                                    required
                                    placeholder="Ulangi password baru"
                                    class="w-full rounded-xl border border-[#EAE0D5] px-3.5 py-2 text-xs font-semibold focus:border-[#7A0C2E] focus:outline-none pr-9"
                                />
                                <button
                                    type="button"
                                    @click="showConfirmPassword = !showConfirmPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-stone-400 hover:text-stone-600 transition cursor-pointer"
                                    tabindex="-1"
                                >
                                    <svg v-if="!showConfirmPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5 pt-2">
                            <button
                                @click="showPasswordModal = false"
                                type="button"
                                class="flex-1 rounded-xl border border-stone-200 py-2.5 text-xs font-bold text-stone-600 hover:bg-stone-50 transition cursor-pointer"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                class="flex-1 rounded-xl py-2.5 text-xs font-bold text-white transition shadow-xs cursor-pointer active:scale-95"
                                style="background-color: #7A0C2E;"
                            >
                                Simpan Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </MahasiswaLayout>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.25s ease-out forwards;
}
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(6px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
