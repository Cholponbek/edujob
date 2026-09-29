<script setup>
import Container from '@/Components/ui/Container.vue';
import Logo from '@/Components/ui/Logo.vue';
import Button from '@/Components/ui/Button.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue';
import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline';

const page = usePage();

const navLinks = [
    { label: 'Вакансии', href: '/vacancies' },
];
</script>

<template>
    <div class="flex min-h-screen flex-col bg-white">
        <Disclosure as="header" v-slot="{ open }" class="sticky top-0 z-20 border-b border-ink-100 bg-white/90 backdrop-blur">
            <Container class="flex h-16 items-center justify-between">
                <Link href="/" class="shrink-0">
                    <Logo />
                </Link>

                <nav class="hidden items-center gap-8 md:flex">
                    <Link
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        class="text-sm font-medium text-ink-600 transition hover:text-ink-900"
                    >
                        {{ link.label }}
                    </Link>
                </nav>

                <div class="hidden items-center gap-3 md:flex">
                    <template v-if="page.props.auth.user">
                        <Button as="a" href="/dashboard" variant="outline" size="sm">Личный кабинет</Button>
                    </template>
                    <template v-else>
                        <Button as="a" href="/login" variant="outline" size="sm">Войти</Button>
                        <Button as="a" href="/login" variant="primary" size="sm">Создать резюме</Button>
                    </template>
                </div>

                <DisclosureButton class="md:hidden">
                    <Bars3Icon v-if="!open" class="h-6 w-6 text-ink-700" />
                    <XMarkIcon v-else class="h-6 w-6 text-ink-700" />
                </DisclosureButton>
            </Container>

            <DisclosurePanel class="border-t border-ink-100 md:hidden">
                <Container class="flex flex-col gap-1 py-3">
                    <Link
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        class="rounded-lg px-3 py-2 text-sm font-medium text-ink-700 hover:bg-ink-50"
                    >
                        {{ link.label }}
                    </Link>
                    <Link
                        v-if="page.props.auth.user"
                        href="/dashboard"
                        class="rounded-lg px-3 py-2 text-sm font-medium text-ink-700 hover:bg-ink-50"
                    >
                        Личный кабинет
                    </Link>
                    <Link
                        v-else
                        href="/login"
                        class="rounded-lg px-3 py-2 text-sm font-medium text-ink-700 hover:bg-ink-50"
                    >
                        Войти
                    </Link>
                </Container>
            </DisclosurePanel>
        </Disclosure>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="border-t border-ink-100 bg-ink-950 text-ink-200">
            <Container class="flex flex-col gap-6 py-12 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <Logo size="sm" inverted />
                    <p class="mt-2 max-w-sm text-sm text-ink-400">
                        Платформа подбора кадров для сферы образования в Кыргызстане.
                    </p>
                </div>
                <p class="text-sm text-ink-500">© {{ new Date().getFullYear() }} EduJob</p>
            </Container>
        </footer>
    </div>
</template>
