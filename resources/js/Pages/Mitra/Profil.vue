<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MitraLayout from '@/Layouts/MitraLayout.vue';
import { useMitraProfil } from '@/Composables/useMitraProfil';

const props = defineProps({
    merchant: {
        type: Object,
        default: () => null,
    },
    user: {
        type: Object,
        default: () => null,
    },
    stats: {
        type: Object,
        default: () => null,
    },
});

const {
    isEditing,
    saveToast,
    localReviewDelta,
    storeForm,
    currentReviewsCount,
    currentRating,
    performanceStats,
    formatRupiah,
    showPasswordModal,
    passwordForm,
    passwordError,
    passwordSuccessToast,
    lastPasswordChange,
    openPasswordModal,
    handlePasswordSubmit,
    saveProfile,
    syncReviewDelta,
} = useMitraProfil(props);
</script>

<template>
    <MitraLayout title="Profil Gerai Mitra - PlusFood">
        <Head title="Profil Gerai Mitra - PlusFood UMKM" />
        <div class="mx-auto max-w-[1440px] space-y-6 sm:space-y-8">
            
            <!-- Breadcrumbs & Heading -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                        <Link :href="route('mitra.dashboard')" class="hover:text-brand-maroon transition">Mitra Dashboard</Link>
                        <span>/</span>
                        <span class="text-slate-800 font-bold">Profil Gerai</span>
                    </div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Profil & Informasi Gerai UMKM</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola data resmi warung, jam operasional, rekening kasir, dan keamanan akun mitra.</p>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        v-if="!isEditing"
                        @click="isEditing = true"
                        class="inline-flex items-center gap-2 rounded-xl bg-brand-maroon px-4 py-2 text-xs font-extrabold text-white shadow-xs hover:bg-brand-maroon-hover transition active:scale-95 cursor-pointer"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        <span>Ubah Informasi Gerai</span>
                    </button>
                    <div v-else class="flex items-center gap-2">
                        <button
                            @click="isEditing = false"
                            class="rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            @click="saveProfile"
                            class="rounded-xl bg-brand-maroon px-4 py-2 text-xs font-extrabold text-white shadow-xs hover:bg-brand-maroon-hover transition cursor-pointer"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Toast Notifikasi Sukses -->
            <Transition name="fade">
                <div v-if="saveToast" class="fixed bottom-6 right-6 z-50 rounded-2xl bg-slate-900 text-white px-4 py-3 shadow-2xl flex items-center gap-2.5 text-xs font-bold border border-slate-700">
                    <div class="h-6 w-6 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-black">✓</div>
                    <span>Data gerai berhasil disimpan!</span>
                </div>
            </Transition>
            <Transition name="fade">
                <div v-if="passwordSuccessToast" class="fixed bottom-6 right-6 z-50 rounded-2xl bg-slate-900 text-white px-4 py-3 shadow-2xl flex items-center gap-2.5 text-xs font-bold border border-slate-700">
                    <div class="h-6 w-6 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-black">✓</div>
                    <span>Password akun mitra berhasil diubah!</span>
                </div>
            </Transition>

            <!-- Grid 2 Kolom: Kiri Identitas Gerai & Metrik, Kanan Detail Form -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- ================= KOLOM KIRI (4 COLS): KARTU GERAI & PERFORMA ================= -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- Kartu Utama Gerai -->
                    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden text-center">
                        <!-- Banner Marun Solid -->
                        <div class="h-28 w-full bg-brand-maroon relative flex items-center justify-end p-3">
                            <span class="rounded-lg bg-white/20 backdrop-blur-sm text-white px-2 py-0.5 text-[10px] font-black border border-white/30">
                                MITRA RESMI
                            </span>
                        </div>

                        <!-- Avatar Merchant -->
                        <div class="relative -mt-12 flex justify-center">
                            <div class="relative">
                                <div class="w-24 h-24 rounded-2xl bg-[#700926] text-white text-3xl font-black border-4 border-white shadow-md flex items-center justify-center select-none overflow-hidden">
                                    <img
                                        v-if="props.merchant?.image"
                                        :src="props.merchant.image"
                                        :alt="storeForm.storeName"
                                        class="w-full h-full object-cover"
                                    />
                                    <span v-else class="text-3xl font-black text-amber-100 tracking-wider">
                                        {{ (storeForm.storeName || 'GM').trim().substring(0, 2).toUpperCase() }}
                                    </span>
                                </div>
                                <div class="absolute -bottom-1 -right-1 h-7 w-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center shadow-xs border-2 border-white" title="Terverifikasi">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Info Nama Gerai & Rating -->
                        <div class="px-6 pt-3 pb-6">
                            <h2 class="text-xl font-black text-slate-900 tracking-tight">{{ storeForm.storeName }}</h2>
                            <p class="text-xs font-semibold text-slate-500 mt-1 flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 text-brand-maroon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{ storeForm.canteenLocation }}</span>
                            </p>

                            <!-- Solid Green Box Badge Verifikasi (Sesuai Permintaan User) -->
                            <div class="mt-3 flex justify-center">
                                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-emerald-600 text-white font-extrabold text-xs shadow-xs border border-emerald-700">
                                    <svg class="w-3.5 h-3.5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Mitra Terverifikasi BEM UNESA</span>
                                </span>
                            </div>

                            <!-- Rating & Ulasan -->
                            <div class="mt-4 flex items-center justify-center gap-2 text-xs font-bold">
                                <div class="flex items-center text-amber-500">
                                    <span>★</span>
                                    <span class="text-slate-900 font-black ml-1">{{ currentRating.toFixed(1) }}</span>
                                </div>
                                <span class="text-slate-400">·</span>
                                <span class="text-slate-500 font-medium">{{ currentReviewsCount }} ulasan mahasiswa</span>
                            </div>
                        </div>

                        <!-- Metrik Performa 3 Kolom -->
                        <div class="grid grid-cols-3 border-t border-slate-100 bg-slate-50/50 divide-x divide-slate-100 py-4 px-2">
                            <div class="text-center">
                                <p class="text-lg font-black text-brand-maroon">{{ performanceStats.portions_rescued }}</p>
                                <p class="text-[10px] font-semibold text-slate-500">porsi rescued</p>
                            </div>
                            <div class="text-center">
                                <p class="text-lg font-black text-brand-maroon">{{ formatRupiah(performanceStats.recovered_capital) }}</p>
                                <p class="text-[10px] font-semibold text-slate-500">modal pulih</p>
                            </div>
                            <div class="text-center">
                                <p class="text-lg font-black text-emerald-600">{{ performanceStats.co2_prevented }} kg</p>
                                <p class="text-[10px] font-semibold text-slate-500">CO2e dicegah</p>
                            </div>
                        </div>
                    </div>

                    <!-- Jam Operasional & Pickup Card (Clean White) -->
                    <div class="rounded-3xl bg-white border border-slate-200 p-5 shadow-sm space-y-3.5">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <h3 class="text-xs font-black text-slate-900 flex items-center gap-1.5 uppercase tracking-wider">
                                <svg class="h-4 w-4 text-brand-maroon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.3">
                                    <circle cx="12" cy="12" r="9" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2" />
                                </svg>
                                <span>Jam Sesi Pickup Rutin</span>
                            </h3>
                            <span class="rounded bg-emerald-100 text-emerald-800 px-2 py-0.5 text-[9px] font-extrabold">
                                Aktif Tiap Hari
                            </span>
                        </div>

                        <div class="space-y-2 text-xs">
                            <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-200/80">
                                <span class="font-bold text-slate-700">Sesi Siang (Makan Siang)</span>
                                <span class="font-extrabold text-slate-900">13.00 – 15.00 WIB</span>
                            </div>
                            <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-200/80">
                                <span class="font-bold text-slate-700">Sesi Sore (Menjelang Tutup)</span>
                                <span class="font-extrabold text-slate-900">16.30 – 18.00 WIB</span>
                            </div>
                            <div class="flex items-center justify-between p-2 rounded-xl bg-brand-maroon text-white font-bold shadow-2xs">
                                <span>Sesi Malam (Surplus Utama)</span>
                                <span class="font-extrabold">19.00 – 20.30 WIB</span>
                            </div>
                        </div>

                        <p class="text-[11px] text-slate-500 leading-relaxed pt-1">
                            *Mahasiswa mendapatkan toleransi pengambilan hingga 15 menit sebelum gerai resmi tutup.
                        </p>
                    </div>

                </div>

                <!-- ================= KOLOM KANAN (8 COLS): FORM DETAIL & REKENING ================= -->
                <div class="lg:col-span-8 space-y-6">
                    
                    <!-- 1. KARTU IDENTITAS RESMI GERAI -->
                    <div class="rounded-3xl bg-white border border-slate-200 p-6 sm:p-7 shadow-sm space-y-5">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div>
                                <h3 class="text-base font-black text-slate-900">Data Legalitas & Penanggung Jawab</h3>
                                <p class="text-xs text-slate-500">Identitas resmi gerai untuk verifikasi BEM kampus dan kemitraan PlusFood.</p>
                            </div>
                            <!-- Solid green badge tersimpan otomatis -->
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-black px-3 py-1.5 rounded-xl bg-emerald-600 text-white shadow-xs">
                                <svg class="h-3.5 w-3.5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span>Tersimpan Otomatis</span>
                            </span>
                        </div>

                        <!-- Baris 1: Nama Gerai & Pemilik -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Gerai / Warung</label>
                                <input
                                    type="text"
                                    v-model="storeForm.storeName"
                                    :disabled="!isEditing"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:ring-1 focus:ring-brand-maroon focus:outline-none shadow-2xs transition disabled:text-slate-800 disabled:cursor-default"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Pemilik / Pengelola</label>
                                <input
                                    type="text"
                                    v-model="storeForm.ownerName"
                                    :disabled="!isEditing"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:ring-1 focus:ring-brand-maroon focus:outline-none shadow-2xs transition disabled:text-slate-800 disabled:cursor-default"
                                />
                            </div>
                        </div>

                        <!-- Baris 2: Kontak WA, Email, NIB -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">No. WhatsApp Kasir</label>
                                <input
                                    type="text"
                                    v-model="storeForm.phone"
                                    :disabled="!isEditing"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:ring-1 focus:ring-brand-maroon focus:outline-none shadow-2xs transition disabled:text-slate-800 disabled:cursor-default"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Bisnis</label>
                                <input
                                    type="email"
                                    v-model="storeForm.email"
                                    :disabled="!isEditing"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:ring-1 focus:ring-brand-maroon focus:outline-none shadow-2xs transition disabled:text-slate-800 disabled:cursor-default"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor Izin Usaha / NIB</label>
                                <input
                                    type="text"
                                    v-model="storeForm.nib"
                                    :disabled="!isEditing"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:ring-1 focus:ring-brand-maroon focus:outline-none shadow-2xs transition disabled:text-slate-800 disabled:cursor-default"
                                />
                            </div>
                        </div>

                        <!-- Baris 3: Lokasi Kampus & Titik Stand -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Zona Kampus Utama</label>
                                <input
                                    type="text"
                                    v-model="storeForm.campusZone"
                                    :disabled="!isEditing"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:ring-1 focus:ring-brand-maroon focus:outline-none shadow-2xs transition disabled:text-slate-800 disabled:cursor-default"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Stand / Titik Ambil</label>
                                <input
                                    type="text"
                                    v-model="storeForm.canteenLocation"
                                    :disabled="!isEditing"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:ring-1 focus:ring-brand-maroon focus:outline-none shadow-2xs transition disabled:text-slate-800 disabled:cursor-default"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- 2. KARTU REKENING & PENCIRAN DANA QRIS -->
                    <div class="rounded-3xl bg-white border border-slate-200 p-6 sm:p-7 shadow-sm space-y-5">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div>
                                <h3 class="text-base font-black text-slate-900">Rekening Kasir & Pencairan Dana</h3>
                                <p class="text-xs text-slate-500">Pendapatan penjualan surplus harian ditransfer otomatis setiap hari (H+1).</p>
                            </div>
                            <span class="rounded-lg bg-emerald-100 text-emerald-800 px-2.5 py-0.5 text-[10px] font-black border border-emerald-300">
                                QRIS Terverifikasi
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Bank Tujuan</label>
                                <input
                                    type="text"
                                    v-model="storeForm.bankName"
                                    :disabled="!isEditing"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:outline-none shadow-2xs transition disabled:text-slate-800"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor Rekening</label>
                                <input
                                    type="text"
                                    v-model="storeForm.bankAccount"
                                    :disabled="!isEditing"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:outline-none shadow-2xs transition disabled:text-slate-800"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Atas Nama Rekening</label>
                                <input
                                    type="text"
                                    v-model="storeForm.bankHolder"
                                    :disabled="!isEditing"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:border-brand-maroon focus:outline-none shadow-2xs transition disabled:text-slate-800"
                                />
                            </div>
                        </div>

                        <!-- Info Settlement Otomatis -->
                        <div class="rounded-2xl bg-slate-50 p-4 border border-slate-200 text-xs text-slate-700 flex items-start gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-600 text-white shrink-0 font-bold">
                                Rp
                            </div>
                            <div class="space-y-0.5">
                                <p class="font-extrabold text-slate-900">Settlement Dana Otomatis 0% Biaya Potongan Tambahan</p>
                                <p class="text-[11px] text-slate-500 leading-relaxed">
                                    Hasil transaksi mahasiswa yang divalidasi via Scan QR kasir akan diproses langsung ke rekening di atas tanpa biaya potongan tersembunyi.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- 3. KARTU KEAMANAN & AKSES AKUN -->
                    <div class="rounded-3xl bg-white border border-slate-200 p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200 text-brand-maroon flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-slate-900">Keamanan Akun Mitra</h4>
                                <p class="text-xs text-slate-500">Password terakhir diganti: <strong class="text-slate-700">{{ lastPasswordChange }}</strong></p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <button
                                @click="openPasswordModal"
                                class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 rounded-xl border border-brand-maroon px-4 py-2 text-xs font-bold text-brand-maroon hover:bg-rose-50 transition cursor-pointer"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                <span>Ganti Password</span>
                            </button>
                            <Link
                                :href="route('mitra.logout')"
                                method="post"
                                as="button"
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                            >
                                <span>Keluar</span>
                            </Link>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        <!-- ================= MODAL GANTI PASSWORD MITRA ================= -->
        <Transition name="fade">
            <div
                v-if="showPasswordModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4"
                @click.self="showPasswordModal = false"
            >
                <div class="w-full max-w-md rounded-3xl bg-white border border-slate-200 p-6 sm:p-7 shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-200">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-rose-100 text-brand-maroon flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <h3 class="text-base font-black text-slate-900">Ubah Password Akun</h3>
                        </div>
                        <button
                            @click="showPasswordModal = false"
                            class="w-7 h-7 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center transition"
                        >
                            ✕
                        </button>
                    </div>

                    <form @submit.prevent="handlePasswordSubmit" class="space-y-4">
                        <div v-if="passwordError" class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2">
                            <span>⚠</span>
                            <span>{{ passwordError }}</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Password Lama</label>
                            <input
                                v-model="passwordForm.oldPassword"
                                type="password"
                                required
                                placeholder="Masukkan password saat ini"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 focus:border-brand-maroon focus:outline-none shadow-2xs"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Password Baru</label>
                            <input
                                v-model="passwordForm.newPassword"
                                type="password"
                                required
                                placeholder="Minimal 8 karakter"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 focus:border-brand-maroon focus:outline-none shadow-2xs"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Konfirmasi Password Baru</label>
                            <input
                                v-model="passwordForm.confirmPassword"
                                type="password"
                                required
                                placeholder="Ulangi password baru"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 focus:border-brand-maroon focus:outline-none shadow-2xs"
                            />
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button
                                type="button"
                                @click="showPasswordModal = false"
                                class="rounded-xl px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 transition"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                class="rounded-xl bg-brand-maroon px-5 py-2 text-xs font-extrabold text-white shadow-xs hover:bg-brand-maroon-hover transition"
                            >
                                Simpan Password Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
    </MitraLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
