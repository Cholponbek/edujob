<script setup>
defineProps({
    modelValue: { type: [String, Number], default: '' },
});

defineEmits(['update:modelValue']);

defineOptions({ inheritAttrs: false });
</script>

<template>
    <input
        :value="modelValue"
        v-bind="$attrs"
        class="block w-full rounded-xl border-ink-200 text-ink-900 shadow-sm placeholder:text-ink-400 focus:border-ink-400 focus:ring-ink-400 sm:text-sm"
        @input="$emit('update:modelValue', $event.target.value)"
    />
</template>
