import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';

export function useMitraLogin() {
    // Form State
    const form = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const showPassword = ref(false);

    const togglePasswordVisibility = () => {
        showPassword.value = !showPassword.value;
    };

    const goToRegister = () => {
        try {
            if (typeof route === 'function' && route().has('mitra.register')) {
                router.visit(route('mitra.register'));
            } else {
                router.visit('/mitra/register');
            }
        } catch (e) {
            router.visit('/mitra/register');
        }
    };

    const submitLogin = () => {
        form.post(route('mitra.login.store'), {
            onFinish: () => form.reset('password'),
        });
    };

    return {
        form,
        showPassword,
        togglePasswordVisibility,
        goToRegister,
        submitLogin,
    };
}
