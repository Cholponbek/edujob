<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Container from '@/Components/ui/Container.vue';
import Card from '@/Components/ui/Card.vue';
import Badge from '@/Components/ui/Badge.vue';
import SelectMenu from '@/Components/ui/SelectMenu.vue';
import InstitutionAvatar from '@/Components/ui/InstitutionAvatar.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';
import { MagnifyingGlassIcon, MapPinIcon, BriefcaseIcon } from '@heroicons/vue/24/outline';
import { EMPLOYMENT_TYPE_LABELS, educationLevelOptions, employmentTypeOptions } from '@/lib/labels';

const props = defineProps({
    staffRequests: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    filterOptions: { type: Object, default: () => ({ subjects: [], regions: [] }) },
});

const form = reactive({
    q: props.filters.q || '',
    subject: props.filters.subject || null,
    region: props.filters.region || null,
    education_level: props.filters.education_level || null,
    employment_type: props.filters.employment_type || null,
});

let firstRun = true;
watch(
    () => ({ ...form }),
    () => {
        if (firstRun) {
            firstRun = false;
            return;
        }
        router.get('/vacancies', form, { preserveState: true, replace: true, preserveScroll: true });
    },
    { deep: true }
);

function resetFilters() {
    form.q = '';
    form.subject = null;
    form.region = null;
    form.education_level = null;
    form.employment_type = null;
}

function toggleSubject(subject) {
    form.subject = form.subject === subject ? null : subject;
}
</script>

<template>
    <Head title="Вакансии — EduJob" />

    <PublicLayout>
        <Container class="py-12">
            <div class="mb-8">
                <h1 class="text-3xl font-extrabold tracking-tight text-ink-950">Вакансии в сфере образования</h1>
                <p class="mt-2 text-ink-500">{{ staffRequests.total }} открытых вакансий от учреждений Кыргызстана</p>
            </div>

            <div class="mb-6 flex items-center gap-2 rounded-2xl bg-white p-2 shadow-soft ring-1 ring-ink-100">
                <MagnifyingGlassIcon class="ml-2 h-5 w-5 shrink-0 text-ink-400" />
                <input
                    v-model="form.q"
                    type="text"
                    placeholder="Учитель математики, психолог…"
                    class="w-full border-0 bg-transparent py-2 text-sm text-ink-900 placeholder:text-ink-400 focus:outline-none focus:ring-0"
                />
            </div>

            <div v-if="filterOptions.subjects.length > 0" class="mb-8 flex flex-wrap gap-2">
                <button
                    v-for="subject in filterOptions.subjects.slice(0, 10)"
                    :key="subject"
                    type="button"
                    class="rounded-full px-4 py-2 text-sm font-semibold transition"
                    :class="form.subject === subject ? 'bg-ink-950 text-white' : 'bg-white text-ink-600 ring-1 ring-ink-200 hover:bg-ink-100'"
                    @click="toggleSubject(subject)"
                >
                    {{ subject }}
                </button>
            </div>

            <div class="grid gap-8 lg:grid-cols-[240px_1fr]">
                <aside class="space-y-4">
                    <Card class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-ink-400">Регион</label>
                            <SelectMenu
                                v-model="form.region"
                                :options="filterOptions.regions.map((r) => ({ value: r, label: r }))"
                                placeholder="Любой регион"
                            />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-ink-400">Уровень образования</label>
                            <SelectMenu v-model="form.education_level" :options="educationLevelOptions()" placeholder="Любой уровень" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-ink-400">Занятость</label>
                            <SelectMenu v-model="form.employment_type" :options="employmentTypeOptions()" placeholder="Любая занятость" />
                        </div>
                        <button
                            type="button"
                            class="text-sm font-medium text-ink-500 underline decoration-ink-300 underline-offset-2 hover:text-ink-900"
                            @click="resetFilters"
                        >
                            Сбросить фильтры
                        </button>
                    </Card>
                </aside>

                <div>
                    <div v-if="staffRequests.data.length === 0" class="rounded-2xl border border-dashed border-ink-200 py-20 text-center text-ink-400">
                        По этим фильтрам вакансий не найдено.
                    </div>

                    <div v-else class="grid gap-5 sm:grid-cols-2">
                        <Link
                            v-for="sr in staffRequests.data"
                            :key="sr.id"
                            :href="`/vacancies/${sr.id}`"
                            class="block"
                        >
                            <Card hoverable class="h-full">
                                <div class="flex items-start gap-3">
                                    <InstitutionAvatar :name="sr.institution.name" size="sm" />
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold text-ink-900">{{ sr.institution.name }}</p>
                                        <p class="flex items-center gap-1 text-xs text-ink-400">
                                            <MapPinIcon class="h-3.5 w-3.5" /> {{ sr.institution.region }}
                                        </p>
                                    </div>
                                    <Badge v-if="sr.is_next_school_year" tone="warning">на след. год</Badge>
                                </div>

                                <h3 class="mt-4 font-bold leading-snug text-ink-950">{{ sr.title }}</h3>

                                <p class="mt-2 flex items-center gap-1.5 text-sm text-ink-500">
                                    <BriefcaseIcon class="h-4 w-4 shrink-0 text-ink-400" />
                                    {{ EMPLOYMENT_TYPE_LABELS[sr.employment_type] }}
                                </p>

                                <p v-if="sr.salary_from || sr.salary_to" class="mt-3 font-semibold text-emerald-600">
                                    {{ sr.salary_from ?? '—' }}–{{ sr.salary_to ?? '—' }} сом
                                </p>
                            </Card>
                        </Link>
                    </div>

                    <nav v-if="staffRequests.last_page > 1" class="mt-10 flex flex-wrap justify-center gap-1">
                        <template v-for="link in staffRequests.links" :key="link.label">
                            <span
                                v-if="!link.url"
                                class="rounded-lg px-3 py-2 text-sm text-ink-300"
                                v-html="link.label"
                            />
                            <Link
                                v-else
                                :href="link.url"
                                class="rounded-lg px-3 py-2 text-sm font-medium"
                                :class="link.active ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-ink-50'"
                                preserve-scroll
                                v-html="link.label"
                            />
                        </template>
                    </nav>
                </div>
            </div>
        </Container>
    </PublicLayout>
</template>
