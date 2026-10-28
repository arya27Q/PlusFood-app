<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
        default: null,
    },
});

const form = useForm({
    email: '',
    storeName: '',
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const submit = () => {
    form.post(route('mitra.forgot-password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Lupa Password Mitra UMKM - PlusFood" />

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

    <div class="min-h-screen w-full bg-[#FAF7F2] flex flex-col justify-between items-center p-4 sm:p-6 lg:p-10 text-[#2D1B1E] selection:bg-[#700926] selection:text-white">
        
        <div class="hidden sm:block sm:h-2"></div>

        <div 
            class="w-full max-w-[980px] bg-white rounded-[28px] sm:rounded-[32px] overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.07)] ring-1 ring-black/[0.04] grid grid-cols-1 md:grid-cols-12 min-h-[580px] my-auto"
            style="animation: fade-in-up 0.5s ease-out both"
        >
            <!-- ================= KOLOM KIRI: HERO MITRA UMKM ================= -->
            <div class="relative md:col-span-6 bg-[#700926] flex flex-col items-center justify-center p-6 sm:p-12 text-center text-white overflow-hidden min-h-[200px] md:min-h-full">
                
                <div class="absolute inset-0 z-0">
                    <img 
                        src="https://images.unsplash.com/photo-1556740738-b6a63e27c4df?auto=format&fit=crop&w=1200&q=80" 
                        alt="Mitra Kasir UMKM PlusFood" 
                        class="h-full w-full object-cover mix-blend-multiply opacity-35"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-[#58071E]/95 via-[#700926]/85 to-[#700926]/90"></div>
                </div>

                <div class="relative z-10 flex flex-col items-center max-w-[340px]">
                    <div class="h-14 w-14 sm:h-20 sm:w-20 rounded-full bg-white/10 ring-1 ring-white/20 backdrop-blur-xs flex items-center justify-center mb-3 sm:mb-6 shadow-inner">
                        <svg class="h-7 w-7 sm:h-9 sm:w-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-tight">
                        Pemulihan Akun Toko
                    </h1>

                    <p class="mt-3 text-xs sm:text-sm text-white/90 leading-relaxed font-normal">
                        Lupa kata sandi toko Anda?<br />
                        Verifikasi nama toko atau nomor telepon terdaftar untuk membuat kata sandi baru.
                    </p>

                    <Link
                        :href="route('mitra.login')"
                        class="mt-8 inline-flex items-center justify-center rounded-xl border border-white/40 bg-white/5 px-7 py-2.5 text-xs sm:text-sm font-bold text-white transition-all duration-200 hover:bg-white hover:text-[#700926] hover:border-white active:scale-95 shadow-sm"
                    >
                        ← Kembali ke Masuk Mitra
                    </Link>
                </div>
            </div>

            <!-- ================= KOLOM KANAN: FORM RESET PASSWORD MITRA ================= -->
            <div class="md:col-span-6 bg-white p-8 sm:p-12 lg:p-14 flex flex-col justify-between">
                
                <div>
                    <!-- Back Link -->
                    <div class="flex items-center justify-between">
                        <Link :href="route('mitra.login')" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-maroon hover:underline">
                            <span>← Masuk Mitra</span>
                        </Link>
                    </div>

                    <!-- Flash Message Status -->
                    <div v-if="status" class="mt-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-xs font-semibold text-emerald-800 flex items-center gap-2">
                        <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ status }}</span>
                    </div>

                    <!-- Title & Subtitle Form -->
                    <h2 class="mt-3.5 text-2xl sm:text-3xl font-extrabold text-[#5B081E] tracking-tight">
                        Reset Kata Sandi Toko
                    </h2>
                    <p class="mt-2 text-xs sm:text-sm text-[#736863] leading-relaxed max-w-[360px]">
                        Verifikasi kepemilikan akun toko Anda untuk memperbarui kata sandi secara instan.
                    </p>

                    <!-- Form -->
                    <form @submit.prevent="submit" class="mt-6 space-y-4">
                        
                        <!-- Input Email Toko -->
                        <div>
                            <label class="block text-xs sm:text-sm font-bold text-[#2D1B1E] mb-1.5">
                                Email Bisnis / Toko Terdaftar *
                            </label>
                            <div 
                                class="relative flex items-center rounded-xl px-3.5 py-2.5 sm:py-3 transition-all"
                                :class="form.errors.email 
                                    ? 'bg-[#FFFDFD] border border-[#F87171] focus-within:ring-2 focus-within:ring-red-200' 
                                    : 'bg-[#F7F2EB] border border-transparent focus-within:bg-white focus-within:ring-2 focus-within:ring-[#700926]/20'"
                            >
                                <svg class="h-5 w-5 text-[#8C7A72] shrink-0 mr-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                                
                                <input
                                    v-model="form.email"
                                    type="email"
                                    required
                                    placeholder="nama@warung.com"
                                    class="w-full bg-transparent text-xs sm:text-sm font-medium text-[#2D1B1E] placeholder-[#A69992] focus:outline-none"
                                />
                            </div>
                            <p v-if="form.errors.email" class="text-xs text-[#DC2626] font-medium mt-1.5">
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <!-- Verifikasi Nama Toko / Nomor HP -->
                        <div>
                            <label class="block text-xs sm:text-sm font-bold text-[#2D1B1E] mb-1.5">
                                Nama Toko atau No. HP Terdaftar *
                            </label>
                            <div 
                                class="relative flex items-center rounded-xl px-3.5 py-2.5 sm:py-3 transition-all"
                                :class="form.errors.storeName 
                                    ? 'bg-[#FFFDFD] border border-[#F87171] focus-within:ring-2 focus-within:ring-red-200' 
                                    : 'bg-[#F7F2EB] border border-transparent focus-within:bg-white focus-within:ring-2 focus-within:ring-[#700926]/20'"
                            >
                                <svg class="h-5 w-5 text-[#8C7A72] shrink-0 mr-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M3 9l1-5h16l1 5" />
                                    <path d="M3 9a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0" />
                                    <path d="M4 14v6a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-6" />
                                    <path d="M9 21v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4" />
                                </svg>
                                
                                <input
                                    v-model="form.storeName"
                                    type="text"
                                    required
                                    placeholder="Contoh: Warung Bu Sari"
                                    class="w-full bg-transparent text-xs sm:text-sm font-medium text-[#2D1B1E] placeholder-[#A69992] focus:outline-none"
                                />
                            </div>
                            <p class="text-[10px] text-[#8C7A72] mt-1">Digunakan untuk verifikasi keamanan akun toko Anda.</p>
                            <p v-if="form.errors.storeName" class="text-xs text-[#DC2626] font-medium mt-1">
                                {{ form.errors.storeName }}
                            </p>
                        </div>

                        <!-- Password Baru -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs sm:text-sm font-bold text-[#2D1B1E] mb-1.5">
                                    Kata Sandi Baru *
                                </label>
                                <div 
                                    class="relative flex items-center rounded-xl px-3.5 py-2.5 sm:py-3 transition-all"
                                    :class="form.errors.password 
                                        ? 'bg-[#FFFDFD] border border-[#F87171] focus-within:ring-2 focus-within:ring-red-200' 
                                        : 'bg-[#F7F2EB] border border-transparent focus-within:bg-white focus-within:ring-2 focus-within:ring-[#700926]/20'"
                                >
                                    <input
                                        v-model="form.password"
                                        :type="showPassword ? 'text' : 'password'"
                                        required
                                        placeholder="Min. 6 karakter"
                                        class="w-full bg-transparent text-xs sm:text-sm font-medium text-[#2D1B1E] placeholder-[#A69992] focus:outline-none"
                                    />
                                </div>
                                <p v-if="form.errors.password" class="text-xs text-[#DC2626] font-medium mt-1">
                                    {{ form.errors.password }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-xs sm:text-sm font-bold text-[#2D1B1E] mb-1.5">
                                    Ulangi Kata Sandi *
                                </label>
                                <div 
                                    class="relative flex items-center rounded-xl px-3.5 py-2.5 sm:py-3 transition-all bg-[#F7F2EB] border border-transparent focus-within:bg-white focus-within:ring-2 focus-within:ring-[#700926]/20"
                                >
                                    <input
                                        v-model="form.password_confirmation"
                                        :type="showPassword ? 'text' : 'password'"
                                        required
                                        placeholder="Ulangi sandi"
                                        class="w-full bg-transparent text-xs sm:text-sm font-medium text-[#2D1B1E] placeholder-[#A69992] focus:outline-none"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Toggle Show Password -->
                        <div class="flex items-center justify-between pt-0.5">
                            <button
                                type="button"
                                @click="togglePasswordVisibility"
                                class="text-xs font-semibold text-[#8C7A72] hover:text-[#700926] transition-colors"
                            >
                                {{ showPassword ? 'Sembunyikan Kata Sandi' : 'Tampilkan Kata Sandi' }}
                            </button>
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full mt-2 rounded-xl bg-[#700926] hover:bg-[#58071E] py-3 sm:py-3.5 text-xs sm:text-sm font-bold text-white shadow-md transition-all duration-200 active:scale-[0.99] disabled:opacity-50 cursor-pointer"
                        >
                            <span v-if="form.processing">Menyimpan...</span>
                            <span v-else>Simpan Kata Sandi Toko</span>
                        </button>

                        <div class="text-center text-xs text-[#736863] pt-2">
                            Ingat password toko Anda? 
                            <Link :href="route('mitra.login')" class="font-bold text-[#700926] hover:underline ml-1">
                                Masuk Mitra
                            </Link>
                        </div>
                    </form>
                </div>

                <!-- Footer Keamanan -->
                <div class="pt-6 border-t border-stone-100 flex items-center justify-center gap-1.5 text-[11px] text-[#A69992]">
                    <svg class="h-3.5 w-3.5 text-brand-maroon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Pemulihan akun Mitra terverifikasi database PlusFood</span>
                </div>
            </div>

        </div>

        <div class="hidden sm:block sm:h-2"></div>
    </div>
</template>

