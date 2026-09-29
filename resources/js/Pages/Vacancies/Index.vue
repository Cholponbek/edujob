<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Container from '@/Components/ui/Container.vue';
import Card from '@/Components/ui/Card.vue';
import Badge from '@/Components/ui/Badge.vue';
import SelectMenu from '@/Components/ui/SelectMenu.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';
import { MapPinIcon, AcademicCapIcon, BriefcaseIcon } from '@heroicons/vue/24/outline';
import { EDUCATION_LEVEL_LABELS, EMPLOYMENT_TYPE_LABELS, educationLevelOptions, employmentTypeOptions } from '@/lib/labels';

const props = defineProps({
    staffRequests: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    filterOptions: { type: Object, default: () => ({ subjects: [], regions: [] }) },
});

const form = reactive({
    subject: props.filters.subject || null,
    region: props.filters.region || null,
    education_level: props.filters.education_level || null,
    employment_type: props.filters.employment_type || null,
});

let firstRun = true;
watch(form, () => {
    if (firstRun) {
        firstRun = false;
        return;
    }
    router.get('/vacancies', form, { preserveState: true, replace: true, preserveScroll: true });
});

function resetFilters() {
    form.subject = null;
    form.region = null;
    form.education_level = null;
    form.employment_type = null;
}
</script>

<template>
    <Head title="Вакансии — EduJob" />

    <PublicLayout>
        <Container class="py-12">
            <div class="mb-10">
                <h1 class="text-3xl font-extrabold tracking-tight text-ink-950">Вакансии в сфере образования</h1>
                <p class="mt-2 text-ink-500">{{ staffRequests.total }} открытых вакансий от учреждений Кыргызстана</p>
            </div>

            <div class="grid gap-8 lg:grid-cols-[260px_1fr]">
                <aside class="space-y-4">
                    <Card class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-ink-400">Предмет</label>
                            <SelectMenu
                                v-model="form.subject"
                                :options="filterOptions.subjects.map((s) => ({ value: s, label: s }))"
                                placeholder="Любой предмет"
                            />
                        </div>
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
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="font-bold text-ink-950">{{ sr.title }}</h3>
                                    <Badge v-if="sr.is_next_school_year" tone="warning">на след. год</Badge>
                                </div>
                                <p class="mt-1 text-sm text-ink-500">{{ sr.institution.name }}</p>

                                <dl class="mt-4 space-y-1.5 text-sm text-ink-600">
                                    <div class="flex items-center gap-2">
                                        <MapPinIcon class="h-4 w-4 shrink-0 text-ink-400" />
                                        {{ sr.institution.region }}
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <AcademicCapIcon class="h-4 w-4 shrink-0 text-ink-400" />
                                        {{ EDUCATION_LEVEL_LABELS[sr.education_level] }}
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <BriefcaseIcon class="h-4 w-4 shrink-0 text-ink-400" />
                                        {{ EMPLOYMENT_TYPE_LABELS[sr.employment_type] }}
                                    </div>
                                </dl>

                                <div v-if="sr.salary_from || sr.salary_to" class="mt-4 font-semibold text-ink-950">
                                    {{ sr.salary_from ?? '—' }}–{{ sr.salary_to ?? '—' }} сом
                                </div>
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
