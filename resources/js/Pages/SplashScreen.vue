<script setup>
import { Head, router } from '@inertiajs/vue3';
import LoadingScreen from '@/Components/UI/LoadingScreen.vue';

const handleFinished = () => {
    router.visit(route('beranda'));
};
</script>

<template>
    <Head title="Memuat PlusFood..." />
    <LoadingScreen :duration="2200" @finish="handleFinished" />
</template>
