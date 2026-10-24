import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';

export function useMitraRegister() {
    // Form State via Inertia useForm
    const form = useForm({
        storeName: '',
        email: '',
        password: '',
    });

    const showPassword = ref(false);

    const togglePasswordVisibility = () => {
        showPassword.value = !showPassword.value;
    };

    // Navigasi ke Halaman Login Mitra
    const goToLogin = () => {
        try {
            if (typeof route === 'function' && route().has('mitra.login')) {
                router.visit(route('mitra.login'));
            } else {
                router.visit('/mitra/login');
            }
        } catch (e) {
            router.visit('/mitra/login');
        }
    };

    // Submit Pendaftaran ke Backend MySQL
    const submitRegister = () => {
        form.post(route('mitra.register.store'), {
            onFinish: () => form.reset('password'),
        });
    };

    return {
        form,
        showPassword,
        togglePasswordVisibility,
        goToLogin,
        submitRegister,
    };
}
