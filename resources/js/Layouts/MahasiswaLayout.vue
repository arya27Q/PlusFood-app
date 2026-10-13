<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { useAuth } from '@/Composables/useAuth';

defineProps({
    title: {
        type: String,
        default: 'PlusFood',
    },
});

const page = usePage();
const auth = useAuth();
</script>

<template>
    <div class="min-h-screen bg-[#FAF7F2] text-[#2D1B1E] selection:bg-brand-maroon selection:text-white">
        
        <!-- ================= NAVBAR ATAS MAHASISWA ================= -->
        <header class="sticky top-0 z-40 border-b border-[#EAE0D5] bg-white/95 backdrop-blur-md">
            <div class="mx-auto flex max-w-[1380px] items-center justify-between px-6 py-3.5 sm:px-8">
                
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
                    </nav>
                </div>

                <!-- Sisi Kanan: Search Bar, Notifikasi, Avatar Mahasiswa -->
                <div class="flex items-center gap-3.5">
                    
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
                            class="w-full rounded-xl border border-transparent bg-[#F5EFE6]/70 pl-9 pr-3.5 py-2 text-xs font-medium text-[#2D1B1E] placeholder-[#A8988F] transition focus:border-brand-maroon focus:bg-white focus:outline-none focus:ring-1 focus:ring-brand-maroon"
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
        <main class="mx-auto max-w-[1380px] p-6 sm:p-8 animate-page-enter">
            <slot />
        </main>

    </div>
</template>
