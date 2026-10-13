<script setup>
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import ScanQrModal from '@/Components/Mitra/ScanQrModal.vue';
import { ref } from 'vue';

defineProps({
    title: {
        type: String,
        default: 'Mitra Dashboard - PlusFood',
    },
});

const isStoreOpen = ref(true);
const showScanModal = ref(false);

const handleValidated = (order) => {
    // Bisa trigger alert atau refresh jika ada listener
};
</script>

<template>
    <div class="min-h-screen bg-[#FAF7F2] text-[#2D1B1E] selection:bg-brand-maroon selection:text-white">
        
        <!-- ================= NAVBAR ATAS MITRA UMKM ================= -->
        <header class="sticky top-0 z-40 border-b border-[#EAE0D5] bg-white/95 backdrop-blur-md">
            <div class="mx-auto flex max-w-[1440px] items-center justify-between px-6 py-3 sm:px-8">
                
                <!-- Logo & Brand Mitra -->
                <div class="flex items-center gap-6">
                    <Link :href="route('mitra.dashboard')" class="flex items-center gap-3">
                        <ApplicationLogo :show-text="false" />
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="font-black text-base text-[#2D1B1E] tracking-tight">PlusFood</span>
                                <span class="rounded-md bg-amber-100 text-amber-900 px-1.5 py-0.2 text-[10px] font-black border border-amber-300">
                                    MITRA UMKM
                                </span>
                            </div>
                            <span class="text-[10px] text-[#8C7A72] block font-semibold leading-tight">Warung Bu Sari · Sentra UNESA</span>
                        </div>
                    </Link>

                    <!-- Navigasi Utama Mitra Sesuai Docs PRD -->
                    <nav class="hidden md:flex items-center gap-1 ml-4">
                        <Link
                            :href="route('mitra.dashboard')"
                            class="rounded-xl px-3.5 py-2 text-xs font-bold transition-all"
                            :class="route().current('mitra.dashboard') ? 'bg-brand-maroon text-white font-extrabold shadow-2xs' : 'text-[#6E5A54] hover:text-brand-maroon hover:bg-[#FAF7F2]'"
                        >
                            Dashboard
                        </Link>
                        <Link
                            :href="route('mitra.kelola-menu')"
                            class="rounded-xl px-3.5 py-2 text-xs font-bold transition-all"
                            :class="route().current('mitra.kelola-menu') ? 'bg-brand-maroon text-white font-extrabold shadow-2xs' : 'text-[#6E5A54] hover:text-brand-maroon hover:bg-[#FAF7F2]'"
                        >
                            Kelola Menu Surplus
                        </Link>
                        <Link
                            :href="route('mitra.pesanan')"
                            class="rounded-xl px-3.5 py-2 text-xs font-bold transition-all"
                            :class="route().current('mitra.pesanan') ? 'bg-brand-maroon text-white font-extrabold shadow-2xs' : 'text-[#6E5A54] hover:text-brand-maroon hover:bg-[#FAF7F2]'"
                        >
                            Pesanan Masuk
                        </Link>
                    </nav>
                </div>

                <!-- Sisi Kanan: Status Gerai, Action Buttons, Switcher POV -->
                <div class="flex items-center gap-3">
                    
                    <!-- Toggle Status Sesi Gerai -->
                    <button
                        @click="isStoreOpen = !isStoreOpen"
                        class="hidden sm:inline-flex items-center gap-2 rounded-xl px-3 py-1.5 text-xs font-bold border transition"
                        :class="isStoreOpen ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : 'bg-stone-100 text-stone-600 border-stone-300'"
                    >
                        <span class="h-2 w-2 rounded-full" :class="isStoreOpen ? 'bg-emerald-500 animate-ping' : 'bg-stone-400'"></span>
                        <span>{{ isStoreOpen ? 'Sesi Buka (Menerima Pesanan)' : 'Sesi Tutup' }}</span>
                    </button>

                    <!-- Tombol Buka Scanner QR Kasir -->
                    <button
                        @click="showScanModal = true"
                        class="inline-flex items-center gap-2 rounded-xl bg-amber-400 px-4 py-2 text-xs font-black text-stone-900 shadow-sm hover:bg-amber-300 transition hover:scale-105"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        <span>Scan QR</span>
                    </button>

                    <!-- Tombol Tambah Surplus -->
                    <Link
                        :href="route('mitra.kelola-menu')"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-brand-maroon px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-brand-maroon-hover transition"
                    >
                        <span>+ Tambah Surplus</span>
                    </Link>

                    <!-- Beralih ke Mode Mahasiswa -->
                    <Link
                        :href="route('beranda')"
                        title="Beralih ke Aplikasi Mahasiswa"
                        class="flex items-center gap-1.5 rounded-xl bg-[#F5EFE6] px-3 py-2 text-xs font-bold text-brand-maroon hover:bg-[#EAE0D5] transition"
                    >
                        <span>🎓 Mode Mahasiswa</span>
                    </Link>

                </div>

            </div>
        </header>

        <!-- ================= MAIN CONTENT SLOT ================= -->
        <main class="mx-auto max-w-[1440px] p-6 sm:p-8 animate-page-enter">
            <slot />
        </main>

        <!-- Global Modal Scanner QR Kasir -->
        <ScanQrModal
            v-if="showScanModal"
            @close="showScanModal = false"
            @validated="handleValidated"
        />

    </div>
</template>
