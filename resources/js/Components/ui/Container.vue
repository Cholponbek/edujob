<script setup>
defineProps({
    as: { type: String, default: 'div' },
});
</script>

<template>
    <component :is="as" class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        <slot />
    </component>
</template>
