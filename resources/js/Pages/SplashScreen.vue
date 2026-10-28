<script setup>
import { Head, router, usePage } from '@inertiajs/vue3';
import LoadingScreen from '@/Components/UI/LoadingScreen.vue';
import { onMounted } from 'vue';

const page = usePage();

const getDestination = () => {
    // Jika belum login / belum punya akun, arahkan ke login. Jika sudah, ke beranda
    return page.props.auth?.user ? route('beranda') : route('login');
};

onMounted(() => {
    // Khusus tampilan responsive mobile (< 768px), tidak menampilkan splashscreen:
    // Langsung arahkan seketika (ke login jika belum login, ke beranda jika sudah login)
    if (window.innerWidth < 768) {
        router.visit(getDestination(), { replace: true });
    }
});

const handleFinished = () => {
    router.visit(getDestination(), { replace: true });
};
</script>

<template>
    <Head title="Memuat PlusFood..." />
    <!-- Tampilkan LoadingScreen animasi hanya di desktop / layar non-mobile -->
    <div class="hidden md:block min-h-screen">
        <LoadingScreen :duration="2200" @finish="handleFinished" />
    </div>
</template>
