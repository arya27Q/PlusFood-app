<script setup>
defineProps({
    title: {
        type: String,
        default: 'Halaman Sedang Disiapkan',
    },
});
</script>

<template>
    <div class="min-h-screen bg-[#FAF7F2] p-8 flex items-center justify-center text-[#2D1B1E]">
        <div class="bg-white rounded-2xl p-8 shadow-sm border border-[#E8DFD8] text-center max-w-md">
            <h1 class="text-xl font-bold mb-2">{{ title }}</h1>
            <p class="text-sm text-gray-500">Modul ini dalam tahap pengembangan sesuai desain sistem PlusFood.</p>
        </div>
    </div>
</template>
