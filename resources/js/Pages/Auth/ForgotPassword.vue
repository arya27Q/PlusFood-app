<script setup>
import { ref, onMounted } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/UI/InputError.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const isVisible = ref(false);
const showPassword = ref(false);

const form = useForm({
    email: '',
    name: '',
    password: '',
    password_confirmation: '',
});

onMounted(() => {
    setTimeout(() => {
        isVisible.value = true;
    }, 40);
});

const submit = () => {
    form.post(route('password.email'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Lupa Password Mahasiswa - PlusFood" />

        <div class="grid grid-cols-1 md:grid-cols-12 min-h-[520px] overflow-hidden">
            
            <!-- LEFT PANEL: Maroon Branding -->
            <div
                class="relative flex flex-col items-center justify-center overflow-hidden bg-brand-maroon p-8 text-center text-white md:col-span-5 md:p-12 transition-all duration-700 ease-out"
                :class="isVisible ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-12'"
            >
                <div class="relative z-10 flex flex-col items-center max-w-[280px]">
                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-white/10 ring-4 ring-white/15 animate-pulse-glow">
                        <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>

                    <h2 class="text-2xl font-extrabold tracking-tight sm:text-3xl">
                        Pemulihan Akun
                    </h2>
                    
                    <p class="mt-3 text-xs leading-relaxed text-white/80 sm:text-sm">
                        Lupa kata sandi? Verifikasi identitas Nama akun Anda untuk membuat kata sandi baru.
                    </p>

                    <Link
                        :href="route('login')"
                        class="group mt-8 inline-flex items-center gap-2 justify-center rounded-xl border border-white/60 bg-transparent px-6 py-2.5 text-xs font-bold text-white transition-all duration-300 hover:bg-white hover:text-brand-maroon hover:shadow-lg active:scale-95 sm:text-sm"
                    >
                        <svg class="h-3.5 w-3.5 transition-transform group-hover:-translate-x-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        <span>Kembali ke Login</span>
                    </Link>
                </div>
            </div>

            <!-- RIGHT PANEL: Form Lupa Password -->
            <div
                class="flex flex-col justify-between p-8 sm:p-12 md:col-span-7 transition-all duration-700 ease-out delay-100"
                :class="isVisible ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-12'"
            >
                <div>
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 rounded-full bg-[#F5EFE6] px-3 py-1 text-[11px] font-bold text-brand-maroon">
                        <span class="h-2 w-2 rounded-full bg-brand-maroon"></span>
                        <span>Portal Mahasiswa · Reset Password</span>
                    </div>

                    <h1 class="mt-4 text-2xl font-black tracking-tight text-[#2D1B1E] sm:text-3xl">
                        Reset Kata Sandi
                    </h1>
                    <p class="mt-1 text-xs font-medium text-[#7A6762] sm:text-sm">
                        Verifikasi nama lengkap akun Anda untuk memperbarui kata sandi secara langsung tanpa perlu email atau kode OTP.
                    </p>

                    <!-- Alert Sukses -->
                    <div
                        v-if="status"
                        class="mt-4 rounded-xl bg-emerald-50 border border-emerald-200 p-3.5 text-xs font-bold text-emerald-800 flex items-center gap-2.5"
                    >
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ status }}</span>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submit" class="mt-6 space-y-3.5">
                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-bold text-[#2D1B1E] mb-1">
                                Email Mahasiswa Terdaftar *
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
                                    placeholder="nama@mhs.unesa.ac.id"
                                    class="w-full rounded-xl bg-[#F5EFE6]/70 pl-10 pr-4 py-2.5 text-xs font-medium text-[#2D1B1E] placeholder-[#B5A59D] border border-[#EAE0D5] transition-all duration-200 focus:border-brand-maroon focus:bg-white focus:outline-none"
                                    :class="{ '!border-red-400 !bg-red-50/40': form.errors.email }"
                                />
                            </div>
                            <InputError class="mt-1" :message="form.errors.email" />
                        </div>

                        <!-- Verifikasi Identitas (Nama Lengkap) -->
                        <div>
                            <label class="block text-xs font-bold text-[#2D1B1E] mb-1">
                                Nama Lengkap Terdaftar *
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#998781]">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </span>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    placeholder="Contoh: Raka Pratama"
                                    class="w-full rounded-xl bg-[#F5EFE6]/70 pl-10 pr-4 py-2.5 text-xs font-medium text-[#2D1B1E] placeholder-[#B5A59D] border border-[#EAE0D5] transition-all duration-200 focus:border-brand-maroon focus:bg-white focus:outline-none"
                                    :class="{ '!border-red-400 !bg-red-50/40': form.errors.name }"
                                />
                            </div>
                            <p class="text-[10px] text-[#998781] mt-0.5">Digunakan untuk memastikan kepemilikan akun Anda tanpa perlu kode OTP email.</p>
                            <InputError class="mt-1" :message="form.errors.name" />
                        </div>

                        <!-- Kata Sandi Baru -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-[#2D1B1E] mb-1">
                                    Kata Sandi Baru *
                                </label>
                                <div class="relative">
                                    <input
                                        id="password"
                                        v-model="form.password"
                                        :type="showPassword ? 'text' : 'password'"
                                        required
                                        placeholder="Min. 8 karakter"
                                        class="w-full rounded-xl bg-[#F5EFE6]/70 px-3.5 py-2.5 text-xs font-medium text-[#2D1B1E] placeholder-[#B5A59D] border border-[#EAE0D5] transition-all duration-200 focus:border-brand-maroon focus:bg-white focus:outline-none"
                                        :class="{ '!border-red-400 !bg-red-50/40': form.errors.password }"
                                    />
                                </div>
                                <InputError class="mt-1" :message="form.errors.password" />
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-[#2D1B1E] mb-1">
                                    Ulangi Kata Sandi *
                                </label>
                                <div class="relative">
                                    <input
                                        id="password_confirmation"
                                        v-model="form.password_confirmation"
                                        :type="showPassword ? 'text' : 'password'"
                                        required
                                        placeholder="Ulangi kata sandi"
                                        class="w-full rounded-xl bg-[#F5EFE6]/70 px-3.5 py-2.5 text-xs font-medium text-[#2D1B1E] placeholder-[#B5A59D] border border-[#EAE0D5] transition-all duration-200 focus:border-brand-maroon focus:bg-white focus:outline-none"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Toggle Show Password -->
                        <div class="flex items-center justify-between pt-0.5">
                            <label class="flex items-center gap-2 cursor-pointer text-xs text-[#7A6762] select-none">
                                <input
                                    type="checkbox"
                                    v-model="showPassword"
                                    class="rounded border-[#D5C7BC] text-brand-maroon focus:ring-brand-maroon/20"
                                />
                                <span>Tampilkan kata sandi</span>
                            </label>
                        </div>

                        <div class="pt-2">
                            <PrimaryButton :disabled="form.processing">
                                <span>Simpan Kata Sandi Baru</span>
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </PrimaryButton>
                        </div>

                        <div class="text-center text-xs font-medium text-[#7A6762] pt-1">
                            Ingat password Anda?
                            <Link :href="route('login')" class="font-bold text-brand-maroon hover:underline transition-colors">
                                Masuk ke akun
                            </Link>
                        </div>
                    </form>
                </div>

                <div class="mt-6 flex items-center justify-center gap-1.5 text-[11px] font-medium text-[#998781]">
                    <svg class="h-3.5 w-3.5 text-brand-maroon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Pemulihan akun instan & aman terverifikasi database PlusFood</span>
                </div>
            </div>

        </div>
    </GuestLayout>
</template>
