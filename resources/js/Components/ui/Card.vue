<script setup>
defineProps({
    as: { type: [String, Object], default: 'div' },
    hoverable: { type: Boolean, default: false },
});
</script>

<template>
    <component
        :is="as"
        class="rounded-2xl border border-ink-100 bg-white p-6 shadow-soft"
        :class="hoverable && 'transition duration-150 hover:-translate-y-0.5 hover:shadow-card'"
    >
        <slot />
    </component>
</template>
