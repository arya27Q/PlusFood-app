<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <button
        :type="type"
        :disabled="disabled"
        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-brand-maroon px-5 py-3.5 text-sm font-bold text-white shadow-md shadow-brand-maroon/20 transition-all duration-150 hover:bg-brand-maroon-hover active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-60"
    >
        <slot />
    </button>
</template>
