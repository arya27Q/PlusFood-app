<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/UI/ApplicationLogo.vue';
import { onMounted, onUnmounted } from 'vue';

const handleKeydown = (e) => {
    if (e.key === 'Enter') {
        router.visit(route('login'));
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
    <Head title="PlusFood - Makan Enak, Dompet Aman, Perut Lega" />

    <!-- Style Keyframes Asli untuk Entrance & Floating Animation -->
    <component :is="'style'">
        @keyframes st-up {
            from {
                transform: translateY(24px);
                opacity: 0;
            }
            to {
                transform: none;
                opacity: 1;
            }
        }
        @keyframes st-float {
            0%, 100% {
                translate: 0 0;
            }
            50% {
                translate: 0 -10px;
            }
        }
    </component>

    <!-- Main Outer Container: 100% Fullscreen, Seamless Warm Cream Background -->
    <div class="relative min-h-screen w-full overflow-hidden bg-[#FAF7F2] text-[#2D1B1E] selection:bg-brand-maroon selection:text-white flex flex-col justify-between p-4 sm:p-8 lg:px-16 lg:py-10">
        
        <!-- Header (Logo + Pintu Masuk Mitra UMKM) -->
        <header class="relative z-30 flex items-center justify-between" style="animation: st-up .6s ease-out both">
            <div class="flex items-center gap-4">
                <ApplicationLogo :show-text="true" subtext="Food Rescue Kampus" />
            </div>

            <!-- Tombol Masuk Mitra UMKM (Pintu Masuk POV 2) -->
            
        </header>

        <!-- Main Hero Section (2 Kolom: Kiri Copywriting, Kanan Orbit Piring Makanan) -->
        <main class="relative z-10 my-auto grid grid-cols-1 lg:grid-cols-12 items-center gap-8 lg:gap-12 py-4">
            
            <!-- KOLOM KIRI: Copywriting, Highlights & Tombol Lanjutkan -->
            <div class="flex flex-col justify-center gap-5 sm:gap-7 lg:col-span-5 max-w-[560px] z-20">
                
                <!-- Badge Kampus -->
                <span
                    class="w-fit inline-flex items-center gap-2 py-1.5 px-3 sm:px-4 rounded-lg bg-white border border-[#EAE0D5] text-[11px] sm:text-xs font-bold text-brand-maroon shadow-sm leading-snug"
                    style="animation: st-up .6s .15s ease-out both"
                >
                    <svg class="h-3.5 w-3.5 fill-current text-brand-maroon shrink-0" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/>
                    </svg>
                    <span>Jaringan Kampus Surabaya (UNESA, ITS, UNAIR, UPN & Lainnya)</span>
                </span>

                <!-- Headline Utama -->
                <h1
                    class="text-3xl sm:text-5xl lg:text-[54px] xl:text-[62px] font-extrabold leading-[1.1] sm:leading-[1.05] tracking-tight text-[#2D1B1E]"
                    style="animation: st-up .6s .3s ease-out both"
                >
                    Makan enak,<br />
                    dompet aman,<br />
                    perut <span class="text-brand-maroon">lega.</span>
                </h1>

                <!-- Subtitle Deskripsi -->
                <p
                    class="text-sm sm:text-base lg:text-lg text-[#6E5A54] max-w-[480px] leading-relaxed"
                    style="animation: st-up .6s .45s ease-out both"
                >
                    Selamatkan makanan surplus dari warung sekitar kampus dengan harga mahasiswa.
                </p>

                <!-- Value Highlights (Diskon & Jam Ambil) -->
                <div
                    class="flex flex-wrap gap-4 sm:gap-6 text-xs sm:text-sm font-semibold text-[#6E5A54]"
                    style="animation: st-up .6s .6s ease-out both"
                >
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-brand-maroon shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Diskon hingga 60%
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-brand-maroon shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <circle cx="12" cy="12" r="10" stroke-width="2" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2" />
                        </svg>
                        Ambil 17.00–23.00
                    </span>
                </div>

                <!-- CTA Action Button & Enter Hint -->
                <div class="flex flex-wrap items-center gap-4 sm:gap-6 pt-2" style="animation: st-up .6s .75s ease-out both">
                    <Link
                        :href="route('login')"
                        class="group h-12 sm:h-14 pl-6 sm:pl-8 pr-2 rounded-xl bg-brand-maroon text-white font-bold inline-flex items-center justify-between sm:justify-start gap-4 sm:gap-6 shadow-xl shadow-brand-maroon/20 hover:bg-brand-maroon-hover transition-colors active:scale-95 w-full sm:w-auto"
                    >
                        <span>Lanjutkan</span>
                        <span class="h-9 w-9 sm:h-10 sm:w-10 rounded-lg bg-white/15 grid place-items-center transition-transform group-hover:translate-x-1">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </span>
                    </Link>

                    <p class="hidden sm:inline-block text-xs font-semibold text-[#8A7570]">
                        atau tekan
                        <kbd class="mx-1 px-2 py-0.5 rounded-md bg-white border border-[#EAE0D5] text-[#6E5A54] shadow-sm">
                            Enter
                        </kbd>
                        untuk masuk
                    </p>
                </div>

                <!-- Auth Prompt (Register & Login Horizontal) -->
                <div
                    class="flex flex-wrap items-center gap-4 sm:gap-6 pt-1 text-xs text-[#8A7570]"
                    style="animation: st-up .6s .9s ease-out both"
                >
                    <div class="flex items-center gap-1">
                        <span>Belum punya akun?</span>
                        <Link
                            :href="route('register')"
                            class="font-extrabold text-brand-maroon underline hover:text-brand-maroon-hover transition-colors"
                        >
                            Daftar akun &rarr;
                        </Link>
                    </div>

                    <span class="hidden sm:inline text-[#D9CEBF]">•</span>

                    <div class="flex items-center gap-1">
                        <span>Sudah punya?</span>
                        <Link
                            :href="route('login')"
                            class="font-extrabold text-brand-maroon underline hover:text-brand-maroon-hover transition-colors"
                        >
                            Masuk &rarr;
                        </Link>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: Kanvas Terpadu 100% Persis Gambar Mockup Kedua (Responsive Scaled) -->
            <div class="relative lg:col-span-7 flex items-center justify-center min-h-[360px] xs:min-h-[420px] sm:min-h-[520px] lg:min-h-[640px] w-full overflow-hidden mt-4 lg:mt-0">
                
                <!-- Container Koordinat Bersama (Lingkaran + Garis Putus-Putus + Piringan Menyatu Bebas Geser) -->
                <div class="relative w-[740px] h-[700px] shrink-0 scale-[0.44] xs:scale-[0.52] sm:scale-[0.7] md:scale-[0.85] lg:scale-95 xl:scale-100 origin-center transition-transform duration-300">
                    
                    <!-- 1. Lingkaran Solid Muted Besar di Sisi Kanan -->
                    <div class="pointer-events-none absolute -right-[140px] -top-[90px] w-[880px] h-[880px] rounded-full bg-[#EFE7DC]/70 z-0" />
                    
                    <!-- 2. Garis Tipis Putus-Putus (Dashed Orbit) 620px -->
                    <div class="pointer-events-none absolute right-[20px] top-[40px] w-[620px] h-[620px] rounded-full border-2 border-dashed border-[#D9CEBF] z-0" />

                    <!-- 3. KARTU 1: Nasi Ayam Bakar (Kiri Atas, Menempel Tepat di Busur Kiri Garis Dashed) -->
                    <div
                        class="absolute left-[40px] top-[70px] w-[290px] z-10"
                        style="transform: rotate(-4deg); animation: st-up .7s .2s ease-out both, st-float 4s .2s ease-in-out infinite;"
                    >
                        <div class="bg-white rounded-2xl border border-[#EAE0D5] shadow-xl p-2.5 transition-transform hover:scale-105 duration-300">
                            <div class="relative aspect-[4/3] rounded-xl overflow-hidden bg-[#FAF7F2]">
                                <img
                                    src="https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=600&q=80"
                                    alt="Nasi Ayam Bakar"
                                    class="h-full w-full object-cover"
                                />
                                <span class="absolute top-2 left-2 px-2.5 py-0.5 rounded-md bg-brand-maroon text-white text-xs font-bold shadow-sm">
                                    -52%
                                </span>
                            </div>
                            <div class="px-2 pt-2.5 pb-1 flex justify-between items-baseline">
                                <b class="text-sm text-[#2D1B1E] font-bold">Nasi Ayam Bakar</b>
                                <b class="text-sm text-brand-maroon font-extrabold">Rp12.000</b>
                            </div>
                        </div>
                    </div>

                    <!-- 4. KARTU 2: Soto Lamongan (Kanan Atas, Garis Dashed Tepat Melintasi Sudut Kanan Atas Kartu) -->
                    <div
                        class="absolute left-[430px] top-[140px] w-[240px] z-10"
                        style="transform: rotate(5deg); animation: st-up .7s .4s ease-out both, st-float 4.5s .4s ease-in-out infinite;"
                    >
                        <div class="bg-white rounded-2xl border border-[#EAE0D5] shadow-xl p-2.5 transition-transform hover:scale-105 duration-300">
                            <div class="relative aspect-[4/3] rounded-xl overflow-hidden bg-[#FAF7F2]">
                                <img
                                    src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80"
                                    alt="Soto Lamongan"
                                    class="h-full w-full object-cover"
                                />
                                <span class="absolute top-2 left-2 px-2.5 py-0.5 rounded-md bg-brand-maroon text-white text-xs font-bold shadow-sm">
                                    -45%
                                </span>
                            </div>
                            <div class="px-2 pt-2.5 pb-1 flex justify-between items-baseline">
                                <b class="text-sm text-[#2D1B1E] font-bold">Soto Lamongan</b>
                                <b class="text-sm text-brand-maroon font-extrabold">Rp11.000</b>
                            </div>
                        </div>
                    </div>

                    <!-- 5. KARTU 3: Roti Bakar Keju (Bawah, Garis Dashed Tepat Melintasi Sisi Atas Kartu) -->
                    <div
                        class="absolute left-[165px] bottom-[30px] w-[260px] z-10"
                        style="transform: rotate(2deg); animation: st-up .7s .6s ease-out both, st-float 4.2s .6s ease-in-out infinite;"
                    >
                        <div class="bg-white rounded-2xl border border-[#EAE0D5] shadow-xl p-2.5 transition-transform hover:scale-105 duration-300">
                            <div class="relative aspect-[4/3] rounded-xl overflow-hidden bg-[#FAF7F2]">
                                <img
                                    src="https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=600&q=80"
                                    alt="Roti Bakar Keju"
                                    class="h-full w-full object-cover"
                                />
                                <span class="absolute top-2 left-2 px-2.5 py-0.5 rounded-md bg-brand-maroon text-white text-xs font-bold shadow-sm">
                                    -40%
                                </span>
                            </div>
                            <div class="px-2 pt-2.5 pb-1 flex justify-between items-baseline">
                                <b class="text-sm text-[#2D1B1E] font-bold">Roti Bakar Keju</b>
                                <b class="text-sm text-brand-maroon font-extrabold">Rp10.500</b>
                            </div>
                        </div>
                    </div>

                    <!-- 6. BADGE DAUN: 1.240 porsi diselamatkan (Menempel Rapi di Kanan Kartu Roti Bakar) -->
                    <div
                        class="absolute left-[400px] bottom-[115px] px-4 py-2 rounded-xl bg-brand-maroon text-white shadow-xl text-xs sm:text-sm font-bold flex items-center gap-2 z-20 transition-transform hover:scale-105 whitespace-nowrap"
                        style="animation: st-up .6s .8s ease-out both"
                    >
                        <svg class="h-4 w-4 fill-current shrink-0" viewBox="0 0 24 24">
                            <path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.75C6.38 7.55 5.09 9.8 4.41 12.3 6.44 10.3 10.3 8.7 17 8z"/>
                        </svg>
                        <span>1.240 porsi diselamatkan pekan ini</span>
                    </div>

                </div>

            </div>

        </main>

        <!-- Footer Bersih (Tanpa Teks Tambahan) -->
        <div class="hidden"></div>

    </div>
</template>
