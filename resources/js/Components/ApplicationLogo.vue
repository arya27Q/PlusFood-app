<script setup>
defineProps({
    showText: {
        type: Boolean,
        default: false,
    },
    subtext: {
        type: String,
        default: 'Food Rescue Kampus',
    },
});
</script>

<template>
    <div class="inline-flex items-center gap-3">
        <!-- Logo Emblem -->
        <div class="relative flex h-11 w-11 items-center justify-center rounded-xl bg-brand-maroon text-white shadow-sm ring-4 ring-brand-maroon/10">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Bowl -->
                <path
                    d="M3 10C3 14.9706 7.02944 19 12 19C16.9706 19 21 14.9706 21 10H3Z"
                    fill="currentColor"
                />
                <!-- Plus Cross inside/above bowl -->
                <path
                    d="M12 4V8M10 6H14"
                    stroke="#F5EFE6"
                    stroke-width="2.2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
                <!-- Base stand -->
                <path
                    d="M8.5 20H15.5"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                />
            </svg>
        </div>

        <!-- Text Title -->
        <div v-if="showText" class="flex flex-col text-left">
            <span class="text-base font-extrabold leading-tight tracking-tight text-brand-maroon">
                Plus Food
            </span>
            <span class="text-[11px] font-medium tracking-normal text-[#8A7570]">
                {{ subtext }}
            </span>
        </div>
    </div>
</template>
