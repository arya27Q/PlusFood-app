<script setup>
import { ref, onMounted } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
        default: true,
    },
    status: {
        type: String,
    },
});

import { useAuth } from '@/Composables/useAuth';

const isVisible = ref(false);
const showPassword = ref(false);
const auth = useAuth();

const form = ref({
    email: 'raka@mhs.unesa.ac.id',
    password: 'password123',
    remember: true,
    errors: {},
});

onMounted(() => {
    setTimeout(() => {
        isVisible.value = true;
    }, 40);
});

const submit = () => {
    auth.login(form.value.email, form.value.password);
};
</script>

<template>
    <GuestLayout>
        <Head title="Masuk Akun - PlusFood" />

        <div class="grid grid-cols-1 md:grid-cols-12 min-h-[580px] overflow-hidden">
            
            <!-- LEFT PANEL: Maroon Hero Card (Animasi slide-in dari kiri) -->
            <div
                class="relative flex flex-col items-center justify-center overflow-hidden bg-brand-maroon p-8 text-center text-white md:col-span-5 md:p-12 transition-all duration-700 ease-out"
                :class="isVisible ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-12'"
            >
                <!-- Background Food Texture Overlay dengan pulse halus -->
                <div class="absolute inset-0 opacity-15 mix-blend-overlay transition-transform duration-1000 hover:scale-105">
                    <img
                        src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=1200&q=80"
                        alt="Texture"
                        class="h-full w-full object-cover"
                    />
                </div>

                <!-- Inner Content -->
                <div class="relative z-10 flex flex-col items-center max-w-[280px]">
                    <!-- Bowl Emblem Icon Circle dengan glow animation -->
                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-white/10 ring-4 ring-white/15 animate-pulse-glow">
                        <svg class="h-7 w-7 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 10C3 14.9706 7.02944 19 12 19C16.9706 19 21 14.9706 21 10H3Z" fill="currentColor" />
                            <path d="M12 4V8M10 6H14" stroke="#F5EFE6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M8.5 20H15.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>

                    <h2 class="text-2xl font-extrabold tracking-tight sm:text-3xl">
                        Halo, Mahasiswa!
                    </h2>
                    
                    <p class="mt-3 text-xs leading-relaxed text-white/80 sm:text-sm">
                        Belum punya akun? Daftar untuk mulai berburu makanan surplus mulai Rp5.000.
                    </p>

                    <Link
                        :href="route('register')"
                        class="group mt-8 inline-flex items-center gap-2 justify-center rounded-xl border border-white/60 bg-transparent px-6 py-2.5 text-xs font-bold text-white transition-all duration-300 hover:bg-white hover:text-brand-maroon hover:shadow-lg active:scale-95 sm:text-sm"
                    >
                        <span>Daftar Sekarang</span>
                        <svg class="h-3.5 w-3.5 transition-transform group-hover:translate-x-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </Link>
                </div>
            </div>

            <!-- RIGHT PANEL: Form Login (Animasi slide-in dari kanan) -->
            <div
                class="flex flex-col justify-between p-8 sm:p-12 md:col-span-7 transition-all duration-700 ease-out delay-100"
                :class="isVisible ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-12'"
            >
                
                <div>
                    <!-- Badge Inisiatif -->
                    <div class="inline-flex items-center gap-2 rounded-full bg-[#F5EFE6] px-3 py-1 text-[11px] font-bold text-brand-maroon">
                        <span class="h-2 w-2 rounded-full bg-brand-maroon animate-ping"></span>
                        <span>Inisiatif Kampus Berkelanjutan</span>
                    </div>

                    <!-- Title & Subtitle -->
                    <h1 class="mt-4 text-2xl font-black tracking-tight text-[#2D1B1E] sm:text-3xl">
                        Masuk Akun
                    </h1>
                    <p class="mt-1 text-xs font-medium text-[#7A6762] sm:text-sm">
                        Selamatkan makanan lezat & hemat kantong malam ini di sekitar Kampus.
                    </p>

                    <!-- Flash / Session Status -->
                    <div v-if="status" class="mt-3 text-xs font-bold text-emerald-600">
                        {{ status }}
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submit" class="mt-6 space-y-4">
                        
                        <!-- Email Field -->
                        <div>
                            <label class="block text-xs font-bold text-[#2D1B1E] mb-1.5">
                                Email
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#998781]">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="nama@mhs.unesa.ac.id"
                                    class="w-full rounded-xl bg-[#F5EFE6]/70 pl-10 pr-4 py-3 text-sm font-medium text-[#2D1B1E] placeholder-[#B5A59D] border border-transparent transition-all duration-200 focus:border-brand-maroon focus:bg-[#F5EFE6] focus:outline-none focus:ring-2 focus:ring-brand-maroon/20"
                                    :class="{ '!border-red-400 !bg-red-50/40': form.errors.email }"
                                />
                            </div>
                            <InputError class="mt-1.5" :message="form.errors.email" />
                        </div>

                        <!-- Password Field -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-xs font-bold text-[#2D1B1E]">
                                    Password
                                </label>
                                <Link
                                    v-if="canResetPassword"
                                    :href="route('password.request')"
                                    class="text-xs font-semibold text-brand-maroon hover:underline"
                                >
                                    Lupa password?
                                </Link>
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#998781]">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </span>
                                <input
                                    id="password"
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    required
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                    class="w-full rounded-xl bg-[#F5EFE6]/70 pl-10 pr-10 py-3 text-sm font-medium text-[#2D1B1E] placeholder-[#B5A59D] border border-transparent transition-all duration-200 focus:border-brand-maroon focus:bg-[#F5EFE6] focus:outline-none focus:ring-2 focus:ring-brand-maroon/20"
                                    :class="{ '!border-red-400 !bg-red-50/40': form.errors.password }"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-[#998781] hover:text-[#2D1B1E] transition-colors"
                                >
                                    <svg v-if="!showPassword" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                            <InputError class="mt-1.5" :message="form.errors.password" />
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <PrimaryButton :disabled="form.processing">
                                <span>Masuk</span>
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </PrimaryButton>
                        </div>

                        <!-- Footer switch link -->
                        <div class="text-center text-xs font-medium text-[#7A6762] pt-2">
                            Belum punya akun?
                            <Link :href="route('register')" class="font-bold text-brand-maroon hover:underline transition-colors">
                                Daftar dengan email
                            </Link>
                        </div>

                    </form>
                </div>

                <!-- Footnote Security -->
                <div class="mt-6 flex items-center justify-center gap-1.5 text-[11px] font-medium text-[#998781]">
                    <svg class="h-3.5 w-3.5 text-brand-maroon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Data terproteksi dengan enkripsi • Peduli Zero Waste 2026</span>
                </div>

            </div>

        </div>
    </GuestLayout>
</template>
