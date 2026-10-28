<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import MitraLayout from '@/Layouts/MitraLayout.vue';
import { useKelolaMenu } from '@/Composables/useKelolaMenu';

const fileInputRef = ref(null);
const editFileInputRef = ref(null);

const props = defineProps({
    foods: {
        type: Array,
        default: () => [],
    },
    merchant: {
        type: Object,
        default: () => ({}),
    },
});

const {
    availableCampusZones,
    isCampusDropdownOpen,
    selectedCampusZone,
    selectCampusZone,
    getTagBadgeClass,
    activeListings,
    form,
    imageFile,
    imagePreview,
    handleImageUpload,
    removeImage,
    editImageFile,
    editImagePreview,
    handleEditImageUpload,
    isPublished,
    publishedFoodName,
    setPickupSession,
    discountPercent,
    capitalRecoveryRate,
    isFormValid,
    presetMenus,
    applyPreset,
    getMitraStoreInfo,
    isSubmitting,
    showSuccessModal,
    publishedItemDetails,
    showEditModal,
    editingItem,
    isUpdating,
    editForm,
    showDeleteConfirm,
    itemToDelete,
    isDeleting,
    scrollToListing,
    resetFormForNewItem,
    validationError,
    submitListing,
    openEditModal,
    saveEdit,
    openDeleteModal,
    confirmDelete,
    toggleStatus,
    adjustStock,
} = useKelolaMenu(props);
</script>

<template>
    <MitraLayout>
        <Head title="Kelola Menu Surplus Kilat - PlusFood Mitra" />

        <div class="space-y-7">
            
            <!-- Header Halaman & Navigasi Cepat -->
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-black text-slate-900">Kelola & Input Surplus Kilat</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        Posting sisa makanan layak konsumsi dalam kurang dari 1 menit, selaras dengan filter lokasi & pantangan mahasiswa.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        @click="scrollToListing"
                        class="inline-flex items-center gap-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 text-xs font-black shadow-sm transition active:scale-95 cursor-pointer"
                    >
                        <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span>Kelola Menu Aktif ({{ activeListings.length }})</span>
                    </button>
                    <span class="rounded-xl bg-emerald-600 text-white px-3.5 py-2.5 text-xs font-black shadow-xs">
                        {{ activeListings.filter(l => l.status === 'aktif').length }} Tayang
                    </span>
                </div>
            </div>

            <!-- Toast Sukses Posting -->
            <div v-if="isPublished" class="rounded-2xl bg-emerald-600 p-4 text-white shadow-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 animate-in fade-in zoom-in-95">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-extrabold">Menu Surplus Berhasil Dipublikasikan ke Mahasiswa!</p>
                        <p class="text-xs text-white/90">
                            Menu "{{ publishedFoodName || form.name }}" langsung muncul di Beranda & Katalog Mahasiswa serta siap dicheckout.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 self-end sm:self-center">
                    <button
                        type="button"
                        @click="resetFormForNewItem"
                        class="rounded-xl bg-white text-emerald-800 hover:bg-emerald-50 px-3.5 py-2 text-xs font-black shadow-xs transition active:scale-95 cursor-pointer"
                    >
                        ➕ Publikasikan Menu Lagi
                    </button>
                    <button
                        type="button"
                        @click="isPublished = false"
                        class="h-8 w-8 rounded-lg bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition cursor-pointer"
                        title="Tutup"
                    >
                        ✕
                    </button>
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
                                    id="input-food-name"
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

                        <!-- 1.5. Upload Foto Makanan Surplus -->
                        <div class="space-y-2.5 p-4 rounded-2xl bg-slate-50/70 border border-slate-200/80">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                    <div class="flex h-5 w-5 items-center justify-center rounded-md bg-rose-100 text-brand-maroon">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <span>Foto Makanan Surplus</span>
                                </label>
                                <span class="text-[10px] text-slate-500 font-semibold">JPG, PNG, WEBP (Maks 5MB)</span>
                            </div>

                            <!-- Hidden file input -->
                            <input
                                ref="fileInputRef"
                                type="file"
                                accept="image/jpeg,image/png,image/jpg,image/webp"
                                class="hidden"
                                @change="handleImageUpload"
                            />

                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3.5 bg-white p-3 rounded-xl border border-slate-200 shadow-2xs">
                                <!-- Preview Gambar -->
                                <div class="relative shrink-0">
                                    <img
                                        :src="imagePreview"
                                        alt="Preview Makanan"
                                        class="h-20 w-24 sm:h-22 sm:w-28 rounded-xl object-cover border border-slate-200 bg-slate-100 shadow-2xs"
                                    />
                                    <span
                                        v-if="imageFile"
                                        class="absolute -top-1.5 -right-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-emerald-600 text-white shadow"
                                        title="Foto kustom siap diupload"
                                    >
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </span>
                                </div>

                                <!-- Aksi Upload & Keterangan -->
                                <div class="flex-1 min-w-0 space-y-1.5">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <button
                                            type="button"
                                            @click="fileInputRef?.click()"
                                            class="inline-flex items-center gap-1.5 rounded-xl bg-brand-maroon hover:bg-brand-maroon-hover text-white px-3.5 py-2 text-xs font-bold shadow-xs transition active:scale-95 cursor-pointer"
                                        >
                                            <svg class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>{{ imageFile ? 'Ganti Foto' : 'Upload Foto Makanan' }}</span>
                                        </button>

                                        <button
                                            v-if="imageFile"
                                            type="button"
                                            @click="removeImage"
                                            class="inline-flex items-center gap-1 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-2 text-xs font-semibold transition active:scale-95 cursor-pointer"
                                        >
                                            <span>Reset ke Preset</span>
                                        </button>
                                    </div>

                                    <p class="text-[11px] text-slate-500">
                                        <span v-if="imageFile" class="text-emerald-700 font-bold flex items-center gap-1">
                                            <span>✓ Foto terpilih: {{ imageFile.name }} ({{ (imageFile.size / 1024).toFixed(0) }} KB)</span>
                                        </span>
                                        <span v-else>
                                            Foto ini akan langsung tampil di Beranda, Katalog, dan Detail Menu Mahasiswa saat dipublikasikan.
                                        </span>
                                    </p>
                                </div>
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
                            <div v-if="validationError" class="mb-3 rounded-xl bg-rose-50 border-2 border-rose-300 p-3 text-xs font-bold text-rose-800 flex items-center gap-2 animate-bounce">
                                <svg class="h-4 w-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>{{ validationError }}</span>
                            </div>

                            <button
                                type="submit"
                                :disabled="isSubmitting"
                                class="w-full rounded-xl bg-brand-maroon py-3.5 text-xs font-black text-white shadow-md hover:bg-brand-maroon-hover active:scale-[0.99] transition disabled:opacity-50 cursor-pointer flex items-center justify-center gap-2"
                            >
                                <span v-if="isSubmitting">Sedang Mempublikasikan...</span>
                                <span v-else> Publikasikan Surplus Sekarang ({{ form.stock }} Porsi)</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ================= SISI KANAN: DAFTAR LISTING AKTIF HARI INI (Span 5) ================= -->
                <div class="lg:col-span-5 space-y-4">
                    <div id="daftar-listing-aktif" class="rounded-[26px] bg-white p-6 border border-slate-200/90 shadow-sm space-y-4">
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
                                class="rounded-2xl p-4 border space-y-3.5 transition shadow-2xs"
                                :class="[
                                    (item.status === 'habis' || Number(item.stock) <= 0)
                                        ? 'bg-slate-100 border-slate-300 text-slate-500 shadow-none'
                                        : 'bg-white border-slate-200/90 hover:border-slate-300'
                                ]"
                            >
                                <div class="flex items-start gap-3">
                                    <div class="relative shrink-0">
                                        <img
                                            :src="item.image"
                                            :alt="item.name"
                                            class="h-16 w-16 rounded-xl object-cover bg-slate-100 border border-slate-200 transition"
                                            :class="{ 'grayscale contrast-75 opacity-70': item.status === 'habis' || Number(item.stock) <= 0 }"
                                        />
                                        <span
                                            v-if="item.status === 'habis' || Number(item.stock) <= 0"
                                            class="absolute inset-0 flex items-center justify-center rounded-xl bg-slate-900/60 text-[10px] font-black uppercase tracking-wider text-white backdrop-blur-[1px]"
                                        >
                                            Habis
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <span
                                                class="rounded-md text-xs font-bold px-2 py-0.5"
                                                :class="(item.status === 'habis' || Number(item.stock) <= 0) ? 'bg-slate-200 text-slate-600' : 'bg-rose-50 text-brand-maroon'"
                                            >
                                                {{ item.category }}
                                            </span>
                                            <div class="flex items-center gap-1.5">
                                                <button
                                                    @click="toggleStatus(item)"
                                                    class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-bold transition shadow-xs cursor-pointer"
                                                    :class="(item.status === 'aktif' && Number(item.stock) > 0) ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-slate-300 text-slate-700 hover:bg-slate-400'"
                                                >
                                                    <span class="h-1.5 w-1.5 rounded-full" :class="(item.status === 'aktif' && Number(item.stock) > 0) ? 'bg-white animate-pulse' : 'bg-slate-500'"></span>
                                                    <span>{{ (item.status === 'aktif' && Number(item.stock) > 0) ? 'TAYANG' : 'HABIS' }}</span>
                                                </button>
                                                <!-- Tombol Edit Menu -->
                                                <button
                                                    @click="openEditModal(item)"
                                                    class="p-1 rounded-lg text-slate-500 hover:text-brand-maroon hover:bg-rose-50 transition cursor-pointer"
                                                    title="Edit Menu"
                                                >
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <!-- Tombol Hapus / Batalkan Publikasi -->
                                                <button
                                                    @click="openDeleteModal(item)"
                                                    class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                                    title="Batalkan / Hapus Menu"
                                                >
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        <h3
                                            class="text-sm font-bold truncate mt-1"
                                            :class="(item.status === 'habis' || Number(item.stock) <= 0) ? 'text-slate-500 line-through' : 'text-slate-900'"
                                        >
                                            {{ item.name }}
                                        </h3>
                                        <p class="text-xs font-extrabold mt-0.5" :class="(item.status === 'habis' || Number(item.stock) <= 0) ? 'text-slate-500' : 'text-brand-maroon'">
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
                                    <div class="flex items-center gap-1.5 text-xs font-bold" :class="(item.status === 'habis' || Number(item.stock) <= 0) ? 'text-slate-500' : 'text-brand-maroon'">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="9" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2" />
                                        </svg>
                                        <span>Ambil: {{ item.pickupStart }}–{{ item.pickupEnd }} WIB</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-slate-600">Sisa:</span>
                                        <button
                                            @click="adjustStock(item, -1)"
                                            :disabled="Number(item.stock) <= 0"
                                            class="h-7 w-7 rounded-lg bg-white border border-slate-200 font-bold text-slate-800 text-sm hover:bg-slate-100 cursor-pointer shadow-2xs transition flex items-center justify-center disabled:opacity-30 disabled:cursor-not-allowed"
                                        >
                                            -
                                        </button>
                                        <span
                                            class="font-extrabold text-sm px-1"
                                            :class="Number(item.stock) <= 0 ? 'text-rose-600' : 'text-slate-900'"
                                        >
                                            {{ Number(item.stock) <= 0 ? '0 (Habis)' : item.stock }}
                                        </span>
                                        <button
                                            @click="adjustStock(item, 1)"
                                            class="h-7 w-7 rounded-lg bg-white border border-slate-200 font-bold text-slate-800 text-sm hover:bg-slate-100 cursor-pointer shadow-2xs transition flex items-center justify-center"
                                        >
                                            +
                                        </button>
                                    </div>
                                </div>

                                <!-- Tombol Aksi Nyata: Edit & Batalkan/Hapus Publikasi -->
                                <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                                    <button
                                        type="button"
                                        @click="openEditModal(item)"
                                        class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 py-2 px-3 text-xs font-bold text-slate-800 transition active:scale-95 cursor-pointer shadow-2xs"
                                    >
                                        <svg class="h-3.5 w-3.5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span>Edit Menu</span>
                                    </button>
                                    <button
                                        type="button"
                                        @click="openDeleteModal(item)"
                                        class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 py-2 px-3 text-xs font-bold text-rose-700 transition active:scale-95 cursor-pointer shadow-2xs"
                                    >
                                        <svg class="h-3.5 w-3.5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span>Hapus Menu</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>

        <!-- ================= 1. MODAL EFEK SUKSES PUBLIKASI SURPLUS ================= -->
        <Teleport to="body">
            <div
                v-if="showSuccessModal"
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 backdrop-blur-md p-4 animate-in fade-in"
                style="top: 0; left: 0; width: 100vw; height: 100vh;"
            >
                <div class="relative w-full max-w-md rounded-[32px] bg-white p-6 sm:p-7 shadow-2xl border-2 border-emerald-400 text-center space-y-5 animate-in zoom-in-95">
                    
                    <!-- Decorative Particle Badge -->
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-emerald-100 text-emerald-600 border-2 border-emerald-300 shadow-lg shadow-emerald-600/20">
                        <svg class="h-10 w-10 animate-bounce" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-800 px-3 py-1 text-xs font-black border border-emerald-300 uppercase tracking-wider">
                            ✨ Publikasi Berhasil!
                        </span>
                        <h3 class="text-2xl font-black text-slate-900 mt-2">Menu Surplus Resmi Tayang</h3>
                        <p class="text-xs text-slate-600 mt-1 max-w-xs mx-auto">
                            Menu Anda sudah aktif dan langsung dapat diselamatkan oleh mahasiswa di Beranda & Katalog.
                        </p>
                    </div>

                    <!-- Ringkasan Menu yang Baru Dipublikasikan -->
                    <div v-if="publishedItemDetails" class="rounded-2xl bg-slate-50 p-4 border border-slate-200 text-left space-y-2.5">
                        <div class="flex items-center gap-3">
                            <img
                                :src="publishedItemDetails.image"
                                :alt="publishedItemDetails.name"
                                class="h-14 w-14 rounded-xl object-cover bg-slate-200 shrink-0 border border-slate-200"
                            />
                            <div class="min-w-0 flex-1">
                                <span class="rounded bg-rose-50 text-brand-maroon text-[10px] font-black px-1.5 py-0.5">
                                    {{ publishedItemDetails.category }}
                                </span>
                                <h4 class="text-sm font-bold text-slate-900 truncate mt-0.5">
                                    {{ publishedItemDetails.name }}
                                </h4>
                                <p class="text-xs font-black text-brand-maroon">
                                    Rp{{ Number(publishedItemDetails.priceDiscount || 0).toLocaleString('id-ID') }}
                                    <span class="text-slate-400 line-through font-normal text-[11px] ml-1">
                                        Rp{{ Number(publishedItemDetails.priceOriginal || 0).toLocaleString('id-ID') }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-200/80 flex items-center justify-between text-xs text-slate-600 font-bold">
                            <span class="flex items-center gap-1">
                                <span>📦 Sisa:</span>
                                <strong class="text-slate-900">{{ publishedItemDetails.stock }} Porsi</strong>
                            </span>
                            <span class="flex items-center gap-1 text-amber-800">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <circle cx="12" cy="12" r="9" stroke-width="2" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2" />
                                </svg>
                                <span>{{ publishedItemDetails.pickupTime }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="space-y-2 pt-1">
                        <button
                            @click="scrollToListing"
                            class="w-full rounded-xl bg-brand-maroon py-3 text-xs font-black text-white shadow-md hover:bg-brand-maroon-hover transition active:scale-95 cursor-pointer"
                        >
                            Lihat di Daftar Menu Listing ({{ activeListings.length }} Menu)
                        </button>
                        
                        <div class="flex gap-2">
                            <button
                                @click="showSuccessModal = false"
                                class="flex-1 rounded-xl bg-slate-100 hover:bg-slate-200 py-2.5 text-xs font-bold text-slate-700 transition cursor-pointer"
                            >
                                + Tambah Menu Lain
                            </button>
                            <Link
                                :href="route('katalog')"
                                target="_blank"
                                class="flex-1 inline-flex items-center justify-center gap-1 rounded-xl border border-slate-300 hover:border-brand-maroon py-2.5 text-xs font-bold text-slate-800 hover:text-brand-maroon transition"
                            >
                                <span>Buka Mahasiswa</span>
                                <svg class="h-3.5 w-3.5 -rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </Link>
                        </div>
                    </div>

                </div>
            </div>
        </Teleport>

        <!-- ================= 2. MODAL EDIT MENU SURPLUS ================= -->
        <Teleport to="body">
            <div
                v-if="showEditModal"
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/75 backdrop-blur-sm p-4 animate-in fade-in"
                style="top: 0; left: 0; width: 100vw; height: 100vh;"
            >
                <div class="relative w-full max-w-lg rounded-[28px] bg-white p-6 sm:p-7 shadow-2xl border border-slate-200 max-h-[92vh] overflow-y-auto space-y-5 animate-in zoom-in-95">
                    
                    <button
                        @click="showEditModal = false"
                        class="absolute top-5 right-5 flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 transition"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 text-amber-900 px-3 py-1 text-[11px] font-extrabold border border-amber-300">
                            Edit Data Menu
                        </span>
                        <h3 class="text-xl font-black text-slate-900 mt-1.5">Perbarui Menu Surplus</h3>
                        <p class="text-xs text-slate-500">Sesuaikan harga diskon, porsi tersisa, atau jendela waktu pengambilan.</p>
                    </div>

                    <form @submit.prevent="saveEdit" class="space-y-4 text-left">
                        <!-- Upload / Ganti Foto di Edit Modal -->
                        <div>
                            <label class="block text-xs font-bold text-slate-900 mb-1">Foto Menu Makanan</label>
                            <input
                                ref="editFileInputRef"
                                type="file"
                                accept="image/jpeg,image/png,image/jpg,image/webp"
                                class="hidden"
                                @change="handleEditImageUpload"
                            />
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <img
                                    :src="editImagePreview"
                                    alt="Foto Menu"
                                    class="h-16 w-20 rounded-lg object-cover border border-slate-200 bg-slate-100 shrink-0"
                                />
                                <div class="space-y-1">
                                    <button
                                        type="button"
                                        @click="editFileInputRef?.click()"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 hover:border-brand-maroon px-3 py-1.5 text-xs font-bold text-slate-800 hover:text-brand-maroon transition cursor-pointer shadow-2xs"
                                    >
                                        <svg class="h-3.5 w-3.5 text-brand-maroon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>Ganti Foto</span>
                                    </button>
                                    <p v-if="editImageFile" class="text-[10px] text-emerald-700 font-bold">
                                        ✓ Foto baru: {{ editImageFile.name }}
                                    </p>
                                    <p v-else class="text-[10px] text-slate-500">
                                        Pilih file gambar jika ingin memperbarui foto makanan.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-900 mb-1">Nama Menu *</label>
                            <input
                                type="text"
                                v-model="editForm.name"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:outline-none"
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-900 mb-1">Kategori</label>
                                <select
                                    v-model="editForm.category"
                                    class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:outline-none"
                                >
                                    <option value="Nasi">Nasi</option>
                                    <option value="Mie">Mie</option>
                                    <option value="Soto">Soto</option>
                                    <option value="Roti">Roti</option>
                                    <option value="Camilan">Camilan</option>
                                    <option value="Minuman">Minuman</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-900 mb-1">Sisa Porsi (Stok) *</label>
                                <input
                                    type="number"
                                    v-model="editForm.stock"
                                    min="0"
                                    max="100"
                                    required
                                    class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:outline-none"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-900 mb-1">Harga Normal (Rp) *</label>
                                <input
                                    type="number"
                                    v-model="editForm.priceOriginal"
                                    step="500"
                                    required
                                    class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:outline-none"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-900 mb-1">Harga Diskon Surplus *</label>
                                <input
                                    type="number"
                                    v-model="editForm.priceDiscount"
                                    step="500"
                                    required
                                    class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs font-bold text-brand-maroon focus:border-brand-maroon focus:outline-none"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-900 mb-1">Jam Ambil Mulai</label>
                                <input
                                    type="text"
                                    v-model="editForm.pickupStart"
                                    placeholder="19.00"
                                    class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:outline-none"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-900 mb-1">Jam Ambil Selesai</label>
                                <input
                                    type="text"
                                    v-model="editForm.pickupEnd"
                                    placeholder="20.30"
                                    class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:outline-none"
                                />
                            </div>
                        </div>

                        <!-- Tag Pantangan Alergi -->
                        <div>
                            <label class="block text-xs font-bold text-slate-900 mb-1.5">Tag Alergen & Kualitas:</label>
                            <div class="grid grid-cols-3 gap-2 text-xs">
                                <label class="flex items-center gap-1.5 p-2 rounded-lg bg-slate-50 border border-slate-200 cursor-pointer">
                                    <input type="checkbox" value="Halal" v-model="editForm.tags" class="rounded text-brand-maroon" />
                                    <span>Halal</span>
                                </label>
                                <label class="flex items-center gap-1.5 p-2 rounded-lg bg-slate-50 border border-slate-200 cursor-pointer">
                                    <input type="checkbox" value="Bebas Kacang" v-model="editForm.tags" class="rounded text-brand-maroon" />
                                    <span>Bebas Kacang</span>
                                </label>
                                <label class="flex items-center gap-1.5 p-2 rounded-lg bg-slate-50 border border-slate-200 cursor-pointer">
                                    <input type="checkbox" value="Vegetarian" v-model="editForm.tags" class="rounded text-brand-maroon" />
                                    <span>Vegetarian</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-900 mb-1">Catatan Makanan</label>
                            <textarea
                                v-model="editForm.kitchenNote"
                                rows="2"
                                class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs text-slate-900 focus:border-brand-maroon focus:outline-none"
                            ></textarea>
                        </div>

                        <div class="pt-2 flex gap-2">
                            <button
                                type="button"
                                @click="showEditModal = false"
                                class="flex-1 rounded-xl bg-slate-100 hover:bg-slate-200 py-3 text-xs font-bold text-slate-700 transition cursor-pointer"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="isUpdating"
                                class="flex-1 rounded-xl bg-brand-maroon hover:bg-brand-maroon-hover py-3 text-xs font-black text-white shadow-md transition disabled:opacity-50 cursor-pointer"
                            >
                                <span v-if="isUpdating">Menyimpan...</span>
                                <span v-else>Simpan Perubahan</span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </Teleport>

        <!-- ================= 3. MODAL BATALKAN / HAPUS PUBLIKASI ================= -->
        <Teleport to="body">
            <div
                v-if="showDeleteConfirm"
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/75 backdrop-blur-sm p-4 animate-in fade-in"
                style="top: 0; left: 0; width: 100vw; height: 100vh;"
            >
                <div class="relative w-full max-w-sm rounded-[28px] bg-white p-6 sm:p-7 shadow-2xl border border-slate-200 text-center space-y-4 animate-in zoom-in-95">
                    
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 border-2 border-rose-300">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>

                    <div>
                        <h3 class="text-lg font-black text-slate-900">Batalkan & Hapus Publikasi?</h3>
                        <p class="text-xs text-slate-600 mt-1">
                            Menu <strong class="text-slate-900">"{{ itemToDelete?.name }}"</strong> akan ditarik dari katalog mahasiswa dan tidak dapat dipesan lagi.
                        </p>
                    </div>

                    <div class="pt-2 flex gap-2">
                        <button
                            type="button"
                            @click="showDeleteConfirm = false"
                            class="flex-1 rounded-xl bg-slate-100 hover:bg-slate-200 py-3 text-xs font-bold text-slate-700 transition cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="confirmDelete"
                            :disabled="isDeleting"
                            class="flex-1 rounded-xl bg-rose-600 hover:bg-rose-700 py-3 text-xs font-black text-white shadow-md transition disabled:opacity-50 cursor-pointer"
                        >
                            <span v-if="isDeleting">Menghapus...</span>
                            <span v-else>Ya, Hapus</span>
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </MitraLayout>
</template>
