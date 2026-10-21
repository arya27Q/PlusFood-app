<script setup>
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/UI/ApplicationLogo.vue';
import Dropdown from '@/Components/UI/Dropdown.vue';
import DropdownLink from '@/Components/UI/DropdownLink.vue';
import { useAuth } from '@/Composables/useAuth';

defineProps({
    title: {
        type: String,
        default: 'PlusFood',
    },
});

const auth = useAuth();
</script>

<template>
    <div class="min-h-screen bg-[#FAF7F2] text-[#2D1B1E] selection:bg-brand-maroon selection:text-white">
        
        <!-- ================= NAVBAR ATAS MAHASISWA ================= -->
        <header class="sticky top-0 z-40 border-b border-[#EAE0D5] bg-white/95 backdrop-blur-md">
            <div class="w-full flex items-center justify-between px-4 sm:px-6 md:px-8 lg:px-12 py-3.5">
                
                <!-- Logo & Brand -->
                <div class="flex items-center gap-8">
                    <Link :href="route('beranda')">
                        <ApplicationLogo :show-text="true" subtext="Food Rescue Kampus" />
                    </Link>

                    <!-- Navigasi Utama: Beranda, Katalog, Riwayat -->
                    <nav class="hidden md:flex items-center gap-1">
                        <Link
                            :href="route('beranda')"
                            class="rounded-xl px-4 py-2 text-sm font-bold transition-all"
                            :class="route().current('beranda') ? 'bg-[#F5EFE6] text-brand-maroon font-extrabold' : 'text-[#6E5A54] hover:text-brand-maroon hover:bg-[#FAF7F2]'"
                        >
                            Beranda
                        </Link>
                        <Link
                            :href="route('katalog')"
                            class="rounded-xl px-4 py-2 text-sm font-bold transition-all"
                            :class="route().current('katalog') || route().current('makanan.detail') ? 'bg-[#F5EFE6] text-brand-maroon font-extrabold' : 'text-[#6E5A54] hover:text-brand-maroon hover:bg-[#FAF7F2]'"
                        >
                            Katalog
                        </Link>
                        <Link
                            :href="route('riwayat')"
                            class="rounded-xl px-4 py-2 text-sm font-bold transition-all"
                            :class="route().current('riwayat') ? 'bg-[#F5EFE6] text-brand-maroon font-extrabold' : 'text-[#6E5A54] hover:text-brand-maroon hover:bg-[#FAF7F2]'"
                        >
                            Riwayat
                        </Link>
                        <Link
                            :href="route('profil')"
                            class="rounded-xl px-4 py-2 text-sm font-bold transition-all"
                            :class="route().current('profil') ? 'bg-[#F5EFE6] text-brand-maroon font-extrabold' : 'text-[#6E5A54] hover:text-brand-maroon hover:bg-[#FAF7F2]'"
                        >
                            Profil
                        </Link>
                    </nav>
                </div>

                <!-- Sisi Kanan: Search Bar, Notifikasi, Avatar Mahasiswa -->
                <div class="flex items-center gap-2 sm:gap-3.5">
                    
                    <!-- Search Input Pill -->
                    <div class="relative hidden sm:block w-64 lg:w-72">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#9E8B83] pointer-events-none">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input
                            type="text"
                            placeholder="Cari makanan surplus..."
                            class="w-full rounded-xl border border-[#EAE0D5] bg-[#F5EFE6]/70 pl-9 pr-3.5 py-2 text-xs font-medium text-[#2D1B1E] placeholder-[#A8988F] transition focus:border-brand-maroon focus:bg-white focus:outline-none"
                        />
                    </div>


                    <!-- Notifikasi Icon Pill -->
                    <button class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#F5EFE6] text-[#7A6762] transition hover:bg-[#EAE0D5] hover:text-brand-maroon">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </button>

                    <!-- User Profile / Avatar Menu -->
                    <div v-if="auth.user.value" class="relative">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-maroon text-xs font-extrabold text-white shadow-sm ring-2 ring-brand-maroon/20 hover:scale-105 transition cursor-pointer">
                                    {{ auth.user.value.name.charAt(0).toUpperCase() }}
                                </button>
                            </template>
                            <template #content>
                                <div class="px-4 py-2.5 border-b border-[#F5EFE6] text-xs">
                                    <p class="font-bold text-[#2D1B1E] truncate">{{ auth.user.value.name }}</p>
                                    <p class="text-[#8C7A72] text-[11px] truncate">{{ auth.user.value.email }}</p>
                                </div>
                                <DropdownLink :href="route('profil')">
                                    Profil Saya
                                </DropdownLink>
                                <DropdownLink :href="route('riwayat')">
                                    Riwayat Pesanan
                                </DropdownLink>
                                <button
                                    type="button"
                                    @click="auth.logout()"
                                    class="block w-full px-4 py-2 text-start text-xs font-semibold leading-5 text-rose-600 transition duration-150 ease-in-out hover:bg-rose-50 focus:outline-none"
                                >
                                    Keluar (Logout)
                                </button>
                            </template>
                        </Dropdown>
                    </div>

                    <!-- Guest Fallback -->
                    <div v-else class="flex items-center gap-2">
                        <Link :href="route('login')" class="rounded-xl px-3 py-1.5 text-xs font-bold text-brand-maroon hover:bg-[#F5EFE6]">
                            Masuk
                        </Link>
                        <Link :href="route('register')" class="rounded-xl bg-brand-maroon px-3 py-1.5 text-xs font-bold text-white hover:bg-brand-maroon-hover shadow-sm">
                            Daftar
                        </Link>
                    </div>

                </div>

            </div>
        </header>

        <!-- ================= MAIN CONTENT SLOT ================= -->
        <main class="w-full px-4 sm:px-6 md:px-8 lg:px-12 py-6 pb-24 md:pb-12 animate-page-enter">
            <slot />
        </main>

        <!-- ================= BOTTOM NAVIGATION BAR (MOBILE-FIRST) ================= -->
        <nav class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-[#EAE0D5] py-2 px-2 flex items-center justify-around shadow-[0_-4px_20px_rgba(0,0,0,0.05)]">
            <Link
                :href="route('beranda')"
                class="flex-1 flex flex-col items-center gap-0.5 transition-colors"
                :class="route().current('beranda') ? 'text-brand-maroon font-black' : 'text-[#8C7A72] hover:text-brand-maroon font-medium'"
            >
                <div class="p-1 rounded-xl" :class="route().current('beranda') ? 'bg-[#F5EFE6]' : ''">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div>
                <span class="text-[10px] leading-tight">Beranda</span>
            </Link>

            <Link
                :href="route('katalog')"
                class="flex-1 flex flex-col items-center gap-0.5 transition-colors"
                :class="route().current('katalog') || route().current('makanan.detail') ? 'text-brand-maroon font-black' : 'text-[#8C7A72] hover:text-brand-maroon font-medium'"
            >
                <div class="p-1 rounded-xl" :class="route().current('katalog') || route().current('makanan.detail') ? 'bg-[#F5EFE6]' : ''">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </div>
                <span class="text-[10px] leading-tight">Katalog</span>
            </Link>

            <Link
                :href="route('riwayat')"
                class="flex-1 flex flex-col items-center gap-0.5 relative transition-colors"
                :class="route().current('riwayat') ? 'text-brand-maroon font-black' : 'text-[#8C7A72] hover:text-brand-maroon font-medium'"
            >
                <div class="p-1 rounded-xl relative" :class="route().current('riwayat') ? 'bg-[#F5EFE6]' : ''">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                    <!-- Ping dot for active ticket -->
                    <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-amber-500 ring-2 ring-white"></span>
                </div>
                <span class="text-[10px] leading-tight">Riwayat</span>
            </Link>

            <Link
                :href="route('profil')"
                class="flex-1 flex flex-col items-center gap-0.5 transition-colors"
                :class="route().current('profil') ? 'text-brand-maroon font-black' : 'text-[#8C7A72] hover:text-brand-maroon font-medium'"
            >
                <div class="p-1 rounded-xl" :class="route().current('profil') ? 'bg-[#F5EFE6]' : ''">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <span class="text-[10px] leading-tight">Profil</span>
            </Link>

            <Link
                :href="route('mitra.dashboard')"
                class="flex-1 flex flex-col items-center gap-0.5 text-amber-800 font-bold hover:text-amber-900 transition-colors"
            >
                <div class="p-1 rounded-xl bg-amber-100 border border-amber-300">
                    <svg class="h-5 w-5 text-amber-800" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <span class="text-[10px] leading-tight">Mitra</span>
            </Link>
        </nav>

    </div>
</template>
