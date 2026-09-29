<script setup>
import { Listbox, ListboxButton, ListboxOption, ListboxOptions } from '@headlessui/vue';
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid';
import { computed } from 'vue';

const props = defineProps({
    modelValue: { type: [String, Number, null], default: null },
    options: { type: Array, required: true }, // [{ value, label }]
    placeholder: { type: String, default: 'Выбрать' },
});

const emit = defineEmits(['update:modelValue']);

const selected = computed(() =>
    props.options.find((o) => o.value === props.modelValue) ?? null
);
</script>

<template>
    <Listbox :model-value="modelValue" @update:model-value="(v) => emit('update:modelValue', v)">
        <div class="relative">
            <ListboxButton
                class="relative w-full cursor-pointer rounded-xl border border-ink-200 bg-white py-2.5 pl-4 pr-10 text-left text-sm text-ink-900 shadow-sm focus:border-ink-400 focus:outline-none focus:ring-1 focus:ring-ink-400"
            >
                <span :class="!selected && 'text-ink-400'">{{ selected ? selected.label : placeholder }}</span>
                <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                    <ChevronUpDownIcon class="h-4 w-4 text-ink-400" />
                </span>
            </ListboxButton>

            <Transition
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <ListboxOptions
                    class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-xl bg-white py-1 text-sm shadow-card ring-1 ring-ink-100 focus:outline-none"
                >
                    <ListboxOption
                        v-slot="{ active, selected: isSelected }"
                        :value="null"
                    >
                        <li
                            class="relative cursor-pointer select-none py-2 pl-4 pr-9 text-ink-500"
                            :class="active && 'bg-ink-50'"
                        >
                            {{ placeholder }}
                            <CheckIcon v-if="isSelected" class="absolute inset-y-0 right-3 my-auto h-4 w-4 text-ink-900" />
                        </li>
                    </ListboxOption>
                    <ListboxOption
                        v-for="option in options"
                        v-slot="{ active, selected: isSelected }"
                        :key="option.value"
                        :value="option.value"
                    >
                        <li
                            class="relative cursor-pointer select-none py-2 pl-4 pr-9 text-ink-900"
                            :class="active && 'bg-ink-50'"
                        >
                            {{ option.label }}
                            <CheckIcon v-if="isSelected" class="absolute inset-y-0 right-3 my-auto h-4 w-4 text-ink-900" />
                        </li>
                    </ListboxOption>
                </ListboxOptions>
            </Transition>
        </div>
    </Listbox>
</template>
