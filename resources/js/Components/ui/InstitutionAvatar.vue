<script setup>
import { computed } from 'vue';

const props = defineProps({
    name: { type: String, required: true },
    size: { type: String, default: 'md' },
});

const palette = [
    'bg-violet-100 text-violet-700',
    'bg-emerald-100 text-emerald-700',
    'bg-amber-100 text-amber-700',
    'bg-sky-100 text-sky-700',
    'bg-rose-100 text-rose-700',
];

function hashCode(str) {
    let hash = 0;
    for (let i = 0; i < str.length; i++) {
        hash = (hash << 5) - hash + str.charCodeAt(i);
        hash |= 0;
    }
    return Math.abs(hash);
}

const colorClass = computed(() => palette[hashCode(props.name) % palette.length]);
const initial = computed(() => props.name.trim().charAt(0).toUpperCase());

const sizeClasses = {
    sm: 'h-9 w-9 rounded-lg text-sm',
    md: 'h-11 w-11 rounded-xl text-base',
    lg: 'h-14 w-14 rounded-2xl text-lg',
};
</script>

<template>
    <div class="flex shrink-0 items-center justify-center font-bold" :class="[colorClass, sizeClasses[size]]">
        {{ initial }}
    </div>
</template>
