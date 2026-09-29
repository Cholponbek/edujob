<script setup>
import { computed } from 'vue';

const props = defineProps({
    as: { type: [String, Object], default: 'button' },
    variant: {
        type: String,
        default: 'primary',
        validator: (v) => ['primary', 'secondary', 'outline', 'ghost'].includes(v),
    },
    size: {
        type: String,
        default: 'md',
        validator: (v) => ['sm', 'md', 'lg'].includes(v),
    },
});

const variantClasses = {
    primary: 'bg-coral-500 text-white shadow-soft hover:bg-coral-600 focus-visible:outline-coral-500',
    secondary: 'bg-ink-900 text-white shadow-soft hover:bg-ink-800 focus-visible:outline-ink-900',
    outline: 'border border-ink-200 text-ink-900 hover:bg-ink-50 focus-visible:outline-ink-400',
    ghost: 'text-ink-700 hover:bg-ink-50 focus-visible:outline-ink-400',
};

const sizeClasses = {
    sm: 'px-3 py-1.5 text-sm',
    md: 'px-5 py-2.5 text-sm',
    lg: 'px-6 py-3.5 text-base',
};

const classes = computed(() => [
    'inline-flex items-center justify-center gap-2 rounded-full font-semibold transition duration-150 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
    variantClasses[props.variant],
    sizeClasses[props.size],
]);
</script>

<template>
    <component :is="as" :class="classes">
        <slot />
    </component>
</template>
