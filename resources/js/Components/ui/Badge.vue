<script setup>
import { computed } from 'vue';

const props = defineProps({
    tone: {
        type: String,
        default: 'neutral',
        validator: (v) => ['neutral', 'brand', 'success', 'warning', 'danger'].includes(v),
    },
});

const toneClasses = {
    neutral: 'bg-ink-100 text-ink-700',
    brand: 'bg-ink-900 text-white',
    success: 'bg-emerald-100 text-emerald-800',
    warning: 'bg-amber-100 text-amber-800',
    danger: 'bg-rose-100 text-rose-800',
};

const classes = computed(() => [
    'inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold',
    toneClasses[props.tone],
]);
</script>

<template>
    <span :class="classes"><slot /></span>
</template>
