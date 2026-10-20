<script setup>
import { ref, onMounted } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/UI/InputError.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const isVisible = ref(false);
const showPassword = ref(false);

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

onMounted(() => {
    setTimeout(() => {
        isVisible.value = true;
    }, 40);
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Buat Password Baru - PlusFood" />

        <div class="grid grid-cols-1 md:grid-cols-12 min-h-[520px] overflow-hidden">
            
            <!-- LEFT PANEL: Maroon Branding -->
            <div
                class="relative flex flex-col items-center justify-center overflow-hidden bg-brand-maroon p-8 text-center text-white md:col-span-5 md:p-12 transition-all duration-700 ease-out"
                :class="isVisible ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-12'"
            >
                <div class="relative z-10 flex flex-col items-center max-w-[280px]">
                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-white/10 ring-4 ring-white/15 animate-pulse-glow">
                        <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>

                    <h2 class="text-2xl font-extrabold tracking-tight sm:text-3xl">
                        Password Baru
                    </h2>
                    
                    <p class="mt-3 text-xs leading-relaxed text-white/80 sm:text-sm">
                        Amankan kembali akun PlusFood Anda dengan membuat kata sandi baru minimal 8 karakter.
                    </p>
                </div>
            </div>

            <!-- RIGHT PANEL: Form Reset Password -->
            <div
                class="flex flex-col justify-between p-8 sm:p-12 md:col-span-7 transition-all duration-700 ease-out delay-100"
                :class="isVisible ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-12'"
            >
                <div>
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 rounded-full bg-[#F5EFE6] px-3 py-1 text-[11px] font-bold text-brand-maroon">
                        <span class="h-2 w-2 rounded-full bg-brand-maroon"></span>
                        <span>Pemulihan Keamanan</span>
                    </div>

                    <h1 class="mt-4 text-2xl font-black tracking-tight text-[#2D1B1E] sm:text-3xl">
                        Atur Password Baru
                    </h1>
                    <p class="mt-1 text-xs font-medium text-[#7A6762] sm:text-sm">
                        Masukkan kata sandi baru untuk akun <strong class="text-brand-maroon">{{ form.email }}</strong>.
                    </p>

                    <form @submit.prevent="submit" class="mt-6 space-y-4">
                        <!-- Email (Readonly info) -->
                        <input type="hidden" v-model="form.email" />
                        <input type="hidden" v-model="form.token" />

                        <!-- Password Baru -->
                        <div>
                            <label class="block text-xs font-bold text-[#2D1B1E] mb-1.5">
                                Password Baru
                            </label>
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
                                    autofocus
                                    autocomplete="new-password"
                                    placeholder="Minimal 8 karakter"
                                    class="w-full rounded-xl bg-[#F5EFE6]/70 pl-10 pr-10 py-3 text-sm font-medium text-[#2D1B1E] placeholder-[#B5A59D] border border-[#EAE0D5] transition-all duration-200 focus:border-brand-maroon focus:bg-white focus:outline-none"
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

                        <!-- Konfirmasi Password Baru -->
                        <div>
                            <label class="block text-xs font-bold text-[#2D1B1E] mb-1.5">
                                Ulangi Password Baru
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#998781]">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                                <input
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    :type="showPassword ? 'text' : 'password'"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Ulangi password baru"
                                    class="w-full rounded-xl bg-[#F5EFE6]/70 pl-10 pr-4 py-3 text-sm font-medium text-[#2D1B1E] placeholder-[#B5A59D] border border-[#EAE0D5] transition-all duration-200 focus:border-brand-maroon focus:bg-white focus:outline-none"
                                    :class="{ '!border-red-400 !bg-red-50/40': form.errors.password_confirmation }"
                                />
                            </div>
                            <InputError class="mt-1.5" :message="form.errors.password_confirmation" />
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <PrimaryButton :disabled="form.processing">
                                <span>Simpan Password Baru</span>
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <div class="mt-6 flex items-center justify-center gap-1.5 text-[11px] font-medium text-[#998781]">
                    <svg class="h-3.5 w-3.5 text-brand-maroon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Setelah tersimpan, Anda akan otomatis dialihkan ke halaman login</span>
                </div>
            </div>

        </div>
    </GuestLayout>
</template>
