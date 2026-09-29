<script setup>
defineProps({
    size: { type: String, default: 'md' },
    inverted: { type: Boolean, default: false },
});

const sizeClasses = {
    sm: 'text-lg',
    md: 'text-xl',
    lg: 'text-2xl',
};
</script>

<template>
    <span class="inline-flex items-center font-extrabold tracking-tight" :class="sizeClasses[size]">
        <span :class="inverted ? 'text-white' : 'text-ink-950'">Edu</span><span class="text-violet-600">Job</span>
    </span>
</template>
