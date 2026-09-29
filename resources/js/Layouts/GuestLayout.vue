<script setup>
import Logo from '@/Components/ui/Logo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="grid min-h-screen lg:grid-cols-2">
        <div class="relative hidden flex-col justify-between overflow-hidden bg-ink-950 p-12 text-white lg:flex">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_left,_rgba(240,89,42,0.18),_transparent_55%)]" />
            <Link href="/" class="relative">
                <Logo inverted size="lg" />
            </Link>
            <div class="relative">
                <p class="text-2xl font-semibold leading-snug text-white">
                    «Подбор кадров для школ Кыргызстана — быстрее, чем раньше.»
                </p>
                <p class="mt-4 text-sm text-ink-400">
                    ИИ-скрининг откликов и бесплатное резюме за 5 минут для кандидатов.
                </p>
            </div>
        </div>

        <div class="flex flex-col items-center justify-center bg-white px-6 py-12">
            <Link href="/" class="mb-8 lg:hidden">
                <Logo size="lg" />
            </Link>

            <div class="w-full max-w-sm">
                <slot />
            </div>
        </div>
    </div>
</template>
