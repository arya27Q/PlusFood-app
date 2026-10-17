<script setup>
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/UI/ApplicationLogo.vue';
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
const isCollapsed = ref(false);
const isMobileOpen = ref(false);

const toggleSidebar = () => {
    if (window.innerWidth < 768) {
        isMobileOpen.value = !isMobileOpen.value;
    } else {
        isCollapsed.value = !isCollapsed.value;
    }
};

const handleValidated = (order) => {
    // Listener scan QR kasir
};

const navItems = [
    {
        name: 'Dashboard',
        route: 'mitra.dashboard',
        icon: 'dashboard',
    },
    {
        name: 'Kelola Menu Surplus',
        route: 'mitra.kelola-menu',
        icon: 'menu',
    },
    {
        name: 'Pesanan Masuk',
        route: 'mitra.pesanan',
        icon: 'orders',
        badge: '3',
    },
    {
        name: 'Profil Gerai',
        route: 'mitra.profil',
        icon: 'store',
    },
];
</script>

<template>
    <div class="min-h-screen bg-[#F8FAFC] text-slate-900 selection:bg-brand-maroon selection:text-white antialiased font-sans">
        
        <!-- ================= MOBILE BACKDROP OVERLAY ================= -->
        <transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isMobileOpen"
                @click="isMobileOpen = false"
                class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs md:hidden"
            ></div>
        </transition>

        <!-- ================= SIDEBAR TETAP (FIXED) DI SISI KIRI ================= -->
        <aside
            class="fixed inset-y-0 left-0 z-40 flex flex-col justify-between border-r border-slate-200/90 bg-white transition-all duration-300 ease-in-out h-screen overflow-hidden"
            :class="[
                isMobileOpen ? 'translate-x-0 w-64' : '-translate-x-full md:translate-x-0',
                isCollapsed ? 'md:w-20' : 'md:w-64'
            ]"
        >
            <!-- Bagian Atas: Header Logo & Brand -->
            <div class="flex flex-col shrink-0">
                
                <!-- Header Sidebar saat Buka (Expanded) -->
                <div v-if="!isCollapsed" class="flex items-center justify-between border-b border-slate-100 h-16 px-4">
                    <Link :href="route('mitra.dashboard')" class="flex items-center gap-3 overflow-hidden">
                        <ApplicationLogo :show-text="false" class="h-9 w-9 shrink-0" />
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <span class="font-black text-base text-slate-900 tracking-tight">PlusFood</span>
                                <span class="rounded-md bg-amber-100 text-amber-900 px-1.5 py-0.2 text-[10px] font-black border border-amber-300">
                                    MITRA
                                </span>
                            </div>
                            <span class="text-[10px] text-slate-500 block font-semibold leading-tight truncate">Warung Bu Sari</span>
                        </div>
                    </Link>

                    <div class="flex items-center gap-1">
                        <!-- Tombol Tutup Layar Mobile -->
                        <button
                            @click="isMobileOpen = false"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 md:hidden cursor-pointer"
                            title="Tutup Menu"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <!-- Tombol Minimize / Collapse Sidebar Desktop -->
                        <button
                            @click="isCollapsed = true"
                            class="hidden md:flex p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer"
                            title="Tutup Sidebar"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Header Sidebar saat Tertutup (Collapsed) - Terpusat & Tidak Terpotong -->
                <div v-else class="flex items-center justify-center border-b border-slate-100 h-16 w-full">
                    <button
                        @click="isCollapsed = false"
                        class="p-2 rounded-xl hover:bg-slate-100 transition cursor-pointer flex items-center justify-center group"
                        title="Buka Sidebar Penuh"
                    >
                        <ApplicationLogo :show-text="false" class="h-8 w-8 shrink-0 transition group-hover:scale-105" />
                    </button>
                </div>

                <!-- Menu Navigasi Sidebar -->
                <nav class="space-y-1.5 overflow-y-auto" :class="isCollapsed ? 'py-4 px-2' : 'p-3'">
                    <Link
                        v-for="item in navItems"
                        :key="item.route"
                        :href="route(item.route)"
                        @click="isMobileOpen = false"
                        class="flex items-center rounded-2xl text-xs font-bold transition-all group"
                        :class="[
                            route().current(item.route)
                                ? 'bg-brand-maroon text-white font-extrabold shadow-sm'
                                : 'text-slate-600 hover:text-brand-maroon hover:bg-slate-100/70',
                            isCollapsed
                                ? 'w-11 h-11 mx-auto justify-center p-0'
                                : 'gap-3 px-3.5 py-2.5'
                        ]"
                        :title="isCollapsed ? item.name : ''"
                    >
                        <!-- Icon 4 Kotak Bulat Dashboard Sesuai Gambar Referensi -->
                        <svg
                            v-if="item.icon === 'dashboard'"
                            class="h-5 w-5 shrink-0"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                        >
                            <rect x="3" y="3" width="7.5" height="7.5" rx="2.5" />
                            <rect x="13.5" y="3" width="7.5" height="7.5" rx="2.5" />
                            <rect x="3" y="13.5" width="7.5" height="7.5" rx="2.5" />
                            <rect x="13.5" y="13.5" width="7.5" height="7.5" rx="2.5" />
                        </svg>

                        <!-- Icon Kelola Menu Surplus -->
                        <svg
                            v-else-if="item.icon === 'menu'"
                            class="h-5 w-5 shrink-0"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>

                        <!-- Icon Pesanan Masuk -->
                        <svg
                            v-else-if="item.icon === 'orders'"
                            class="h-5 w-5 shrink-0"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>

                        <!-- Icon Profil Gerai -->
                        <svg
                            v-else-if="item.icon === 'store'"
                            class="h-5 w-5 shrink-0"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>

                        <span v-show="!isCollapsed" class="truncate whitespace-nowrap">{{ item.name }}</span>

                        <!-- Badge Jumlah Pesanan Baru -->
                        <span
                            v-if="item.badge && !isCollapsed"
                            class="ml-auto rounded-full bg-rose-500 text-white px-2 py-0.5 text-[10px] font-black shrink-0"
                        >
                            {{ item.badge }}
                        </span>
                    </Link>
                </nav>
            </div>

            <!-- Bagian Bawah Sidebar: Profil Ringkas Mitra -->
            <div class="border-t border-slate-100 p-3 shrink-0">
                <Link
                    :href="route('mitra.profil')"
                    @click="isMobileOpen = false"
                    v-if="!isCollapsed"
                    class="rounded-2xl p-2.5 bg-slate-50 border border-slate-200/80 flex items-center gap-3 transition hover:bg-slate-100/80"
                >
                    <div class="h-9 w-9 rounded-xl bg-brand-maroon text-white flex items-center justify-center font-black text-xs shrink-0 shadow-2xs">
                        WB
                    </div>
                    <div class="min-w-0 flex-1 overflow-hidden">
                        <p class="text-xs font-bold text-slate-900 truncate">Warung Bu Sari</p>
                        <p class="text-[10px] text-slate-500 truncate">Kantin Vokasi Stand 04</p>
                    </div>
                </Link>
                <div v-else class="flex justify-center">
                    <button
                        @click="isCollapsed = false"
                        class="h-10 w-10 rounded-xl bg-brand-maroon text-white flex items-center justify-center font-bold text-xs shadow-2xs cursor-pointer hover:opacity-90 transition"
                        title="Warung Bu Sari · Klik untuk buka sidebar"
                    >
                        WB
                    </button>
                </div>
            </div>
        </aside>

        <!-- ================= MAIN CONTENT WRAPPER ================= -->
        <div
            class="flex flex-col min-w-0 min-h-screen transition-[padding] duration-300 ease-in-out"
            :class="isCollapsed ? 'md:pl-20' : 'md:pl-64'"
        >
            <!-- TOPBAR HEADER MITRA UMKM -->
            <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/95 backdrop-blur-md shadow-[0_1px_4px_rgba(0,0,0,0.03)] h-16 flex items-center px-3 sm:px-6">
                <div class="flex items-center justify-between w-full">
                    
                    <!-- Sisi Kiri: Tombol Toggle Buka-Tutup Sidebar & Nama Panel -->
                    <div class="flex items-center gap-2 sm:gap-3">
                        <button
                            @click="toggleSidebar"
                            class="p-2 rounded-xl text-slate-600 hover:text-brand-maroon hover:bg-slate-100 transition cursor-pointer"
                            :title="isCollapsed ? 'Buka Sidebar Penuh' : 'Tutup Sidebar'"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>

                        <div class="hidden sm:flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-900">Panel Gerai UMKM</span>
                            <span class="text-xs text-slate-400">·</span>
                            <span class="text-xs text-slate-500 font-medium">Warung Bu Sari (UNESA)</span>
                        </div>
                    </div>

                    <!-- Sisi Kanan: Action Buttons Status Sesi, Scan QR, Tambah Surplus -->
                    <div class="flex items-center gap-1.5 sm:gap-2.5 shrink-0">
                        
                        <!-- Toggle Status Sesi Gerai -->
                        <button
                            @click="isStoreOpen = !isStoreOpen"
                            class="inline-flex items-center gap-1.5 sm:gap-2 rounded-xl px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs font-bold transition shadow-xs cursor-pointer whitespace-nowrap"
                            :class="isStoreOpen ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300'"
                        >
                            <span class="h-2 w-2 rounded-full" :class="isStoreOpen ? 'bg-white animate-ping' : 'bg-slate-400'"></span>
                            <span class="hidden md:inline">{{ isStoreOpen ? 'Sesi Buka (Menerima Pesanan)' : 'Sesi Tutup' }}</span>
                            <span class="md:hidden">{{ isStoreOpen ? 'Buka' : 'Tutup' }}</span>
                        </button>

                        <!-- Tombol Buka Scanner QR Kasir -->
                        <button
                            @click="showScanModal = true"
                            class="inline-flex items-center gap-1.5 sm:gap-2 rounded-xl bg-amber-400 px-2.5 sm:px-3.5 py-1.5 sm:py-2 text-xs font-bold text-stone-900 shadow-sm hover:bg-amber-300 transition hover:scale-105 whitespace-nowrap cursor-pointer"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            <span class="hidden sm:inline">Scan QR</span>
                        </button>

                        <!-- Tombol Tambah Surplus Kilat -->
                        <Link
                            :href="route('mitra.kelola-menu')"
                            class="inline-flex items-center gap-1 sm:gap-1.5 rounded-xl bg-brand-maroon px-2.5 sm:px-3.5 py-1.5 sm:py-2 text-xs font-bold text-white shadow-sm hover:bg-brand-maroon-hover transition whitespace-nowrap"
                        >
                            <span class="hidden sm:inline">+ Tambah Surplus</span>
                            <span class="sm:hidden">+ Surplus</span>
                        </Link>

                    </div>

                </div>
            </header>

            <!-- ================= MAIN CONTENT SLOT ================= -->
            <main class="flex-1 p-3 sm:p-5 lg:p-6 pb-24 md:pb-6 w-full mx-auto animate-page-enter">
                <slot />
            </main>

        </div>

        <!-- ================= BOTTOM NAVIGATION BAR MITRA (MOBILE-FIRST) ================= -->
        <nav class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/90 py-2 px-2 flex items-center justify-around shadow-[0_-4px_20px_rgba(0,0,0,0.05)]">
            <Link
                :href="route('mitra.dashboard')"
                class="flex-1 flex flex-col items-center gap-1 transition-colors"
                :class="route().current('mitra.dashboard') ? 'text-brand-maroon font-black' : 'text-slate-500 hover:text-brand-maroon font-medium'"
            >
                <div class="p-1 rounded-xl" :class="route().current('mitra.dashboard') ? 'bg-rose-50' : ''">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <rect x="3" y="3" width="7.5" height="7.5" rx="2.5" />
                        <rect x="13.5" y="3" width="7.5" height="7.5" rx="2.5" />
                        <rect x="3" y="13.5" width="7.5" height="7.5" rx="2.5" />
                        <rect x="13.5" y="13.5" width="7.5" height="7.5" rx="2.5" />
                    </svg>
                </div>
                <span class="text-[10px] leading-tight">Dashboard</span>
            </Link>

            <Link
                :href="route('mitra.kelola-menu')"
                class="flex-1 flex flex-col items-center gap-1 transition-colors"
                :class="route().current('mitra.kelola-menu') ? 'text-brand-maroon font-black' : 'text-slate-500 hover:text-brand-maroon font-medium'"
            >
                <div class="p-1 rounded-xl" :class="route().current('mitra.kelola-menu') ? 'bg-rose-50' : ''">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <span class="text-[10px] leading-tight">Menu</span>
            </Link>

            <!-- Center Action: Scan QR Button -->
            <button
                type="button"
                @click="showScanModal = true"
                class="flex flex-col items-center gap-0.5 px-2 -mt-3.5 group cursor-pointer"
            >
                <div class="h-11 w-11 rounded-2xl bg-amber-400 text-stone-900 flex items-center justify-center shadow-md shadow-amber-400/30 group-active:scale-95 transition-transform border-2 border-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                </div>
                <span class="text-[9px] font-black text-stone-800">Scan QR</span>
            </button>

            <Link
                :href="route('mitra.pesanan')"
                class="flex-1 flex flex-col items-center gap-1 transition-colors relative"
                :class="route().current('mitra.pesanan') ? 'text-brand-maroon font-black' : 'text-slate-500 hover:text-brand-maroon font-medium'"
            >
                <div class="p-1 rounded-xl relative" :class="route().current('mitra.pesanan') ? 'bg-rose-50' : ''">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span class="absolute -top-1 -right-1.5 h-4 w-4 rounded-full bg-rose-500 text-white text-[9px] font-black flex items-center justify-center">
                        3
                    </span>
                </div>
                <span class="text-[10px] leading-tight">Pesanan</span>
            </Link>

            <Link
                :href="route('mitra.profil')"
                class="flex-1 flex flex-col items-center gap-1 transition-colors"
                :class="route().current('mitra.profil') ? 'text-brand-maroon font-black' : 'text-slate-500 hover:text-brand-maroon font-medium'"
            >
                <div class="p-1 rounded-xl" :class="route().current('mitra.profil') ? 'bg-rose-50' : ''">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <span class="text-[10px] leading-tight">Profil</span>
            </Link>
        </nav>

        <!-- Global Modal Scanner QR Kasir -->
        <ScanQrModal
            v-if="showScanModal"
            @close="showScanModal = false"
            @validated="handleValidated"
        />

    </div>
</template>
