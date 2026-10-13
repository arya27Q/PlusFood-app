<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="relative flex min-h-screen w-full items-center justify-center bg-[#FAF7F2] p-4 text-[#2D1B1E] selection:bg-brand-maroon selection:text-white sm:p-8 animate-page-enter">
        
        <!-- Main Card Container dengan transisi bayangan lembut -->
        <div class="w-full max-w-[1020px] overflow-hidden rounded-[28px] bg-white shadow-2xl shadow-black/5 ring-1 ring-black/[0.04] transition-all duration-500 hover:shadow-brand-maroon/5">
            <slot />
        </div>

    </div>
</template>
