<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { onMounted, onUnmounted } from 'vue';

const page = usePage();

const handleEnterKey = (e) => {
    if (e.key === 'Enter') {
        const isMitra = !!(page.props?.auth?.mitra || page.props?.auth?.isMitra);
        router.visit(isMitra ? route('mitra.dashboard') : route('mitra.login'));
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleEnterKey);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleEnterKey);
});
</script>

<template>
    <Head title="Mitra UMKM - PlusFood" />

    <!-- Outer Fullscreen Container with Dark Maroon Gradient -->
    <div class="relative min-h-screen w-full overflow-hidden bg-[#5A071E] text-white selection:bg-amber-400 selection:text-stone-900 flex flex-col justify-between p-6 sm:p-10 lg:p-14">
        
        <!-- ================= BACKGROUND FOTO KASIR DENGAN VIGNETTE MAROON ELEGAN ================= -->
        <div class="absolute inset-0 pointer-events-none z-0">
            <!-- Background Image di Sisi Kanan -->
            <img 
                src="https://images.unsplash.com/photo-1556740738-b6a63e27c4df?auto=format&fit=crop&w=1600&q=80" 
                alt="Kasir Mitra UMKM PlusFood" 
                class="absolute right-0 top-0 h-full w-full lg:w-[65%] object-cover object-[center_right] opacity-40 lg:opacity-55 mix-blend-luminosity filter contrast-110"
            />
            
            <!-- Tint Maroon Overlay dari Kiri ke Kanan Sesuai Mockup -->
            <div class="absolute inset-0 bg-gradient-to-r from-[#5A071E] via-[#5A071E]/95 lg:via-[#5A071E]/80 to-[#5A071E]/30"></div>
            
            <!-- Vignette Tambahan Vertikal untuk Kontras Teks -->
            <div class="absolute inset-0 bg-gradient-to-t from-[#420314]/90 via-transparent to-[#420314]/40"></div>

            <!-- Concentric Circular Accent Arcs di Kiri Bawah Sesuai Gambar -->
            <div class="absolute -bottom-48 -left-48 h-[600px] w-[600px] rounded-full border border-white/[0.07]"></div>
            <div class="absolute -bottom-32 -left-32 h-[440px] w-[440px] rounded-full border border-white/[0.08]"></div>
            <div class="absolute -bottom-16 -left-16 h-[280px] w-[280px] rounded-full border border-white/[0.09]"></div>
        </div>

        <!-- ================= TOP HEADER (BRAND LOGO) ================= -->
        <header class="relative z-10 flex items-center justify-between">
            <!-- Logo PlusFood Rounded Square Badge -->
            <div class="flex items-center gap-3.5">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-[#8A1435] to-[#4D061A] border border-white/20 shadow-md">
                    <!-- Icon Bowl PlusFood dengan Senyuman -->
                    <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3v3" />
                        <path d="M8 4v2" />
                        <path d="M16 4v2" />
                        <path d="M4 9h16a8 8 0 0 1-16 0Z" />
                        <path d="M9 13a3 3 0 0 0 6 0" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-base sm:text-lg font-black tracking-tight text-white leading-none">
                        PlusFood
                    </h1>
                </div>
            </div>
        </header>

        <!-- ================= MAIN CONTENT (HEADLINE & METRICS) ================= -->
        <main class="relative z-10 my-auto py-10 max-w-2xl space-y-7">
            
            <!-- Status Sesi Malam Badge dengan Glowing Red Dot -->
            <div>
                <div class="inline-flex items-center gap-2.5 rounded-full bg-white/[0.08] border border-white/15 px-3.5 py-1.5 text-xs font-medium text-white/90 backdrop-blur-md shadow-xs">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                    </span>
                    <span>Sesi surplus malam ini dibuka 17.00</span>
                </div>
            </div>

            <!-- Headline Utama Sesuai Mockup -->
            <div class="space-y-3">
                <h2 class="text-4xl sm:text-5xl lg:text-[56px] font-black tracking-tight text-white leading-[1.12]">
                    Sisa dapur hari ini,<br />
                    rezeki malam ini.
                </h2>
                <p class="text-sm sm:text-base text-white/80 max-w-lg leading-relaxed font-normal">
                    Jual makanan surplus ke mahasiswa Unesa, kurangi limbah, dan tutup modal sebelum gerai tutup.
                </p>
            </div>

            <!-- 3 Glassmorphism Metric Cards Sesuai Gambar -->
            <div class="grid grid-cols-3 gap-3 sm:gap-4 max-w-lg pt-1">
                
                <!-- Card 1: 86 UMKM mitra -->
                <div class="rounded-2xl bg-white/[0.08] backdrop-blur-md border border-white/15 p-3.5 sm:p-4 text-white shadow-lg space-y-2">
                    <div class="flex items-center text-white/80">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl sm:text-3xl font-black tracking-tight text-white">86</p>
                        <p class="text-[11px] sm:text-xs text-white/75 font-medium leading-tight mt-0.5">UMKM mitra</p>
                    </div>
                </div>

                <!-- Card 2: 3,2 ton makanan diselamatkan -->
                <div class="rounded-2xl bg-white/[0.08] backdrop-blur-md border border-white/15 p-3.5 sm:p-4 text-white shadow-lg space-y-2">
                    <div class="flex items-center text-white/80">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl sm:text-3xl font-black tracking-tight text-white">3,2 ton</p>
                        <p class="text-[11px] sm:text-xs text-white/75 font-medium leading-tight mt-0.5">makanan diselamatkan</p>
                    </div>
                </div>

                <!-- Card 3: 4.100+ mahasiswa aktif -->
                <div class="rounded-2xl bg-white/[0.08] backdrop-blur-md border border-white/15 p-3.5 sm:p-4 text-white shadow-lg space-y-2">
                    <div class="flex items-center text-white/80">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl sm:text-3xl font-black tracking-tight text-white">4.100+</p>
                        <p class="text-[11px] sm:text-xs text-white/75 font-medium leading-tight mt-0.5">mahasiswa aktif</p>
                    </div>
                </div>

            </div>

            <!-- ================= ACTION CTA (BUTTON MASUK & SHORTCUT ENTER) ================= -->
            <div class="pt-4 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <!-- Primary Button Putih Masuk ke Dashboard Sesuai Gambar -->
                    <Link
                        :href="($page.props.auth?.mitra || $page.props.auth?.isMitra) ? route('mitra.dashboard') : route('mitra.login')"
                        class="inline-flex items-center justify-between gap-4 rounded-2xl bg-white px-6 py-3.5 text-sm sm:text-base font-extrabold text-[#5A071E] shadow-2xl hover:bg-stone-50 hover:scale-[1.02] active:scale-[0.98] transition group"
                    >
                        <span>Masuk ke Dashboard</span>
                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-[#5A071E] text-white group-hover:translate-x-0.5 transition">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </div>
                    </Link>

                    <!-- Hint Tekan Enter -->
                    <div class="flex items-center gap-1.5 text-xs text-white/75 font-medium">
                        <span>atau tekan</span>
                        <kbd class="rounded-md bg-white/15 px-2 py-0.5 font-mono text-[10px] text-white border border-white/20 shadow-xs">
                            Enter
                        </kbd>
                        <span>untuk lanjut</span>
                    </div>
                </div>

                <!-- Navigasi Alternatif Login / Register Akun Baru -->
                <div class="flex items-center gap-3 text-xs text-white/70 font-medium pt-1">
                    <span>Sudah terdaftar?</span>
                    <Link :href="route('mitra.login')" class="text-white font-bold underline hover:text-amber-300 transition">
                        Masuk Kasir
                    </Link>
                    <span>•</span>
                    <Link :href="route('mitra.register')" class="text-white font-bold underline hover:text-amber-300 transition">
                        Daftar Mitra Baru
                    </Link>
                </div>
            </div>

        </main>

        <!-- ================= FOOTER / COPYRIGHT ================= -->
        <footer class="relative z-10 pt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between text-xs text-white/50 border-t border-white/[0.08] gap-2">
            <div>
                &copy; {{ new Date().getFullYear() }} PlusFood Indonesia · Platform Penyelamatan Makanan Kampus
            </div>
            <div class="flex items-center gap-4">
                <span>Surabaya, Jawa Timur</span>
                <span>•</span>
                <span>Inisiatif Nol Limbah UNESA</span>
            </div>
        </footer>

    </div>
</template>
