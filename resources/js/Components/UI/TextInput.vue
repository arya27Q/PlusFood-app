<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

defineProps({
    placeholder: {
        type: String,
        default: '',
    },
    hasError: {
        type: Boolean,
        default: false,
    },
});

const input = ref(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <input
        ref="input"
        v-model="model"
        :placeholder="placeholder"
        :class="[
            'w-full rounded-xl bg-[#F5EFE6]/70 px-4 py-3 text-sm font-medium text-[#2D1B1E] placeholder-[#B5A59D] transition-all duration-150',
            'border focus:outline-none focus:ring-2 focus:ring-brand-maroon/20',
            hasError
                ? 'border-red-400 bg-red-50/40 focus:border-red-500'
                : 'border-transparent focus:border-brand-maroon focus:bg-[#F5EFE6]'
        ]"
    />
</template>
