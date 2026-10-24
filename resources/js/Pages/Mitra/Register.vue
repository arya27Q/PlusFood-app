<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

// Form State riil terhubung ke MySQL
const form = useForm({
    storeName: '',
    email: '',
    password: '',
});

const showPassword = ref(false);

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

// Submit Pendaftaran riil ke backend database MySQL (tabel merchants)
const submitRegister = () => {
    form.post(route('mitra.register.store'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Daftar Toko Mitra - PlusFood" />

    <!-- Style Keyframes Asli untuk Animasi Masuk Halus -->
    <component :is="'style'">
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(18px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </component>

    <!-- Main Outer Fullscreen Container (Warna latar hangat #FAF7F2 khas PlusFood) -->
    <div class="min-h-screen w-full bg-[#FAF7F2] flex flex-col justify-between items-center p-4 sm:p-6 lg:p-10 text-[#2D1B1E] selection:bg-[#700926] selection:text-white">
        
        <!-- Spacer Atas agar Kartu Seimbang di Tengah Layar -->
        <div class="hidden sm:block sm:h-2"></div>

        <!-- MAIN REGISTER CARD (Container 2 Kolom dengan Rounded Halus & Bayangan Lembut) -->
        <div 
            class="w-full max-w-[980px] bg-white rounded-[28px] sm:rounded-[32px] overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.07)] ring-1 ring-black/[0.04] grid grid-cols-1 md:grid-cols-12 min-h-[580px] my-auto"
            style="animation: fade-in-up 0.5s ease-out both"
        >
            
            <!-- ================= KOLOM KIRI: FORM DAFTAR TOKO (PUTIH) ================= -->
            <div class="md:col-span-6 bg-white p-6 sm:p-12 lg:p-14 flex flex-col justify-between order-2 md:order-1">
                
                <div>
                    <!-- Back to Splash Screen -->
                    <div class="flex items-center justify-between">
                        <Link :href="route('mitra.splash')" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-maroon hover:underline">
                            <span>← Splash Screen</span>
                        </Link>
                    </div>

                    <!-- Title & Subtitle Form -->
                    <h1 class="mt-3.5 text-2xl sm:text-3xl font-extrabold text-[#5B081E] tracking-tight">
                        Daftar Toko
                    </h1>
                    <p class="mt-2 text-xs sm:text-sm text-[#736863] leading-relaxed max-w-[360px]">
                        Ubah bahan sisa jadi pendapatan. Jangkau ribuan mahasiswa Unesa tiap malam.
                    </p>

                    <!-- Form Pendaftaran -->
                    <form @submit.prevent="submitRegister" class="mt-7 space-y-4">
                        
                        <!-- 1. Input Nama Toko -->
                        <div>
                            <label class="block text-xs sm:text-sm font-bold text-[#2D1B1E] mb-1.5">
                                Nama Toko
                            </label>
                            <div class="relative flex items-center rounded-xl bg-[#F7F2EB] px-3.5 py-2.5 sm:py-3 transition-colors focus-within:bg-white focus-within:ring-2 focus-within:ring-[#700926]/20 focus-within:border-[#700926]/40 border border-transparent">
                                <!-- Icon Profil / User Toko -->
                                <svg class="h-5 w-5 text-[#8C7A72] shrink-0 mr-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                                <input
                                    v-model="form.storeName"
                                    type="text"
                                    required
                                    placeholder="Nama Usaha / Warung Anda"
                                    class="w-full bg-transparent border-0 p-0 text-xs sm:text-sm font-medium text-[#2D1B1E] placeholder-[#A89F9A] focus:ring-0 outline-none"
                                />
                            </div>
                            <div v-if="form.errors.storeName" class="text-xs text-red-600 mt-1 font-semibold">
                                {{ form.errors.storeName }}
                            </div>
                        </div>

                        <!-- 2. Input Email -->
                        <div>
                            <label class="block text-xs sm:text-sm font-bold text-[#2D1B1E] mb-1.5">
                                Email
                            </label>
                            <div class="relative flex items-center rounded-xl bg-[#F7F2EB] px-3.5 py-2.5 sm:py-3 transition-colors focus-within:bg-white focus-within:ring-2 focus-within:ring-[#700926]/20 focus-within:border-[#700926]/40 border border-transparent">
                                <!-- Icon Amplop Email -->
                                <svg class="h-5 w-5 text-[#8C7A72] shrink-0 mr-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="5" width="18" height="14" rx="2" />
                                    <path d="m3 7 9 6 9-6" />
                                </svg>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    required
                                    placeholder="nama@toko.com"
                                    class="w-full bg-transparent border-0 p-0 text-xs sm:text-sm font-medium text-[#2D1B1E] placeholder-[#A89F9A] focus:ring-0 outline-none"
                                />
                            </div>
                            <div v-if="form.errors.email" class="text-xs text-red-600 mt-1 font-semibold">
                                {{ form.errors.email }}
                            </div>
                        </div>

                        <!-- 3. Input Password -->
                        <div>
                            <label class="block text-xs sm:text-sm font-bold text-[#2D1B1E] mb-1.5">
                                Password
                            </label>

                            <div class="relative flex items-center rounded-xl bg-[#F7F2EB] px-3.5 py-2.5 sm:py-3 transition-colors focus-within:bg-white focus-within:ring-2 focus-within:ring-[#700926]/20 focus-within:border-[#700926]/40 border border-transparent">
                                <!-- Icon Gembok Password -->
                                <svg class="h-5 w-5 text-[#8C7A72] shrink-0 mr-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="5" y="11" width="14" height="10" rx="2" />
                                    <path d="M8 11V7a4 4 0 0 1 8 0v4" />
                                </svg>
                                
                                <input
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    required
                                    placeholder="Buat password aman"
                                    class="w-full bg-transparent border-0 p-0 text-xs sm:text-sm font-medium text-[#2D1B1E] placeholder-[#A89F9A] focus:ring-0 outline-none"
                                />

                                <!-- Tombol Toggle Lihat Password (Icon Mata) -->
                                <button
                                    type="button"
                                    @click="togglePasswordVisibility"
                                    class="ml-2 text-[#8C7A72] hover:text-[#2D1B1E] transition-colors focus:outline-none"
                                    aria-label="Lihat password"
                                >
                                    <!-- Mata Terbuka -->
                                    <svg v-if="!showPassword" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    <!-- Mata Tertutup (Eye Slash) -->
                                    <svg v-else class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                                        <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
                                        <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61" />
                                        <line x1="2" y1="2" x2="22" y2="22" />
                                    </svg>
                                </button>
                            </div>
                            <div v-if="form.errors.password" class="text-xs text-red-600 mt-1 font-semibold">
                                {{ form.errors.password }}
                            </div>
                        </div>

                        <!-- Tombol Submit Daftar -->
                        <div class="pt-2">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full flex items-center justify-center gap-2 rounded-xl bg-[#700926] py-3.5 px-6 text-sm font-bold text-white shadow-md shadow-[#700926]/20 transition-all duration-200 hover:bg-[#58071E] active:scale-[0.99] disabled:opacity-75"
                            >
                                <span v-if="!form.processing">Daftar</span>
                                <span v-else>Mendaftarkan...</span>
                                <svg v-if="!form.processing" class="h-4 w-4 transition-transform group-hover:translate-x-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </form>

                    <!-- Link Sudah Punya Akun -->
                    <p class="text-center text-xs sm:text-sm text-[#6E5A54] mt-5">
                        Sudah punya akun? 
                        <Link 
                            :href="route('mitra.login')" 
                            class="font-bold text-[#700926] hover:underline"
                        >
                            Masuk di sini
                        </Link>
                    </p>
                </div>

                <!-- Footer Proteksi & Enkripsi -->
                <div class="flex items-center justify-center gap-1.5 pt-6 text-[11px] sm:text-xs text-[#8C7A72]">
                    <svg class="h-4 w-4 text-[#8C7A72] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        <path d="m9 12 2 2 4-4" />
                    </svg>
                    <span>Data terproteksi dengan enkripsi · Peduli Zero Waste 2026</span>
                </div>

            </div>

            <!-- ================= KOLOM KANAN: HERO MITRA (MAROON & FOTO KASIR) ================= -->
            <div class="relative md:col-span-6 bg-[#700926] flex flex-col items-center justify-center p-6 sm:p-12 text-center text-white overflow-hidden min-h-[200px] md:min-h-full order-1 md:order-2">
                
                <!-- Background Foto Kasir Kafe / Resto dengan Maroon Tint -->
                <div class="absolute inset-0 z-0">
                    <img 
                        src="https://images.unsplash.com/photo-1556740738-b6a63e27c4df?auto=format&fit=crop&w=1200&q=80" 
                        alt="Mitra Kasir UMKM PlusFood" 
                        class="h-full w-full object-cover mix-blend-multiply opacity-35"
                    />
                    <!-- Overlay Gradasi Gelap Elegan -->
                    <div class="absolute inset-0 bg-gradient-to-t from-[#58071E]/95 via-[#700926]/85 to-[#700926]/90"></div>
                </div>

                <!-- Konten Teks & Icon Kanan -->
                <div class="relative z-10 flex flex-col items-center max-w-[340px]">
                    
                    <!-- Icon Toko / Storefront Lingkaran Semi-Transparan -->
                    <div class="h-14 w-14 sm:h-20 sm:w-20 rounded-full bg-white/10 ring-1 ring-white/20 backdrop-blur-xs flex items-center justify-center mb-3 sm:mb-6 shadow-inner">
                        <svg class="h-7 w-7 sm:h-9 sm:w-9 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l1-5h16l1 5" />
                            <path d="M3 9a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0" />
                            <path d="M4 14v6a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-6" />
                            <path d="M9 21v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4" />
                        </svg>
                    </div>

                    <!-- Judul Hero -->
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-tight">
                        Selamat Datang, Mitra!
                    </h2>

                    <!-- Deskripsi Hero -->
                    <p class="mt-3 text-xs sm:text-sm text-white/90 leading-relaxed font-normal">
                        Sudah terdaftar? Masuk untuk mengelola listing surplus tokomu.
                    </p>

                    <!-- Tombol Masuk Sekarang (Outline Putih Transparan) -->
                    <Link
                        :href="route('mitra.login')"
                        class="mt-8 inline-flex items-center justify-center rounded-xl border border-white/40 bg-white/5 px-7 py-2.5 text-xs sm:text-sm font-bold text-white transition-all duration-200 hover:bg-white hover:text-[#700926] hover:border-white active:scale-95 shadow-sm"
                    >
                        Masuk Sekarang
                    </Link>
                </div>
            </div>

        </div>

        <!-- ================= FOOTER HALAMAN LUAR ================= -->
        <footer class="text-center text-[11px] text-[#A89F9A] py-3 tracking-wide">
            Pusat Penyelamatan Pangan Berlebih & Kantin Mitra BEM Unesa
        </footer>

    </div>
</template>
