<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Container from '@/Components/ui/Container.vue';
import Card from '@/Components/ui/Card.vue';
import Badge from '@/Components/ui/Badge.vue';
import Button from '@/Components/ui/Button.vue';
import InstitutionAvatar from '@/Components/ui/InstitutionAvatar.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { MapPinIcon, AcademicCapIcon, BriefcaseIcon } from '@heroicons/vue/24/outline';
import { EDUCATION_LEVEL_LABELS, EMPLOYMENT_TYPE_LABELS, INSTITUTION_TYPE_LABELS } from '@/lib/labels';

const props = defineProps({
    staffRequest: { type: Object, required: true },
    hasApplied: { type: Boolean, default: false },
});

const page = usePage();

function apply() {
    if (!page.props.auth.user) {
        router.visit('/login');
        return;
    }

    router.post('/applications', { staff_request_id: props.staffRequest.id });
}
</script>

<template>
    <Head :title="`${staffRequest.title} — EduJob`" />

    <PublicLayout>
        <Container class="py-12">
            <Link href="/vacancies" class="text-sm font-medium text-ink-500 hover:text-ink-900">← Все вакансии</Link>

            <div class="mt-6 grid gap-8 lg:grid-cols-[1fr_320px]">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <Badge v-if="staffRequest.is_next_school_year" tone="warning">на следующий учебный год</Badge>
                        <Badge tone="neutral">{{ INSTITUTION_TYPE_LABELS[staffRequest.institution.type] }}</Badge>
                    </div>

                    <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-ink-950">{{ staffRequest.title }}</h1>
                    <div class="mt-3 flex items-center gap-3">
                        <InstitutionAvatar :name="staffRequest.institution.name" size="sm" />
                        <span class="font-medium text-ink-700">{{ staffRequest.institution.name }}</span>
                    </div>

                    <dl class="mt-8 grid grid-cols-2 gap-6 border-y border-ink-100 py-6 sm:grid-cols-4">
                        <div>
                            <dt class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-ink-400">
                                <MapPinIcon class="h-4 w-4" /> Регион
                            </dt>
                            <dd class="mt-1 font-medium text-ink-900">{{ staffRequest.institution.region }}</dd>
                        </div>
                        <div>
                            <dt class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-ink-400">
                                <AcademicCapIcon class="h-4 w-4" /> Уровень
                            </dt>
                            <dd class="mt-1 font-medium text-ink-900">{{ EDUCATION_LEVEL_LABELS[staffRequest.education_level] }}</dd>
                        </div>
                        <div>
                            <dt class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-ink-400">
                                <BriefcaseIcon class="h-4 w-4" /> Занятость
                            </dt>
                            <dd class="mt-1 font-medium text-ink-900">{{ EMPLOYMENT_TYPE_LABELS[staffRequest.employment_type] }}</dd>
                        </div>
                        <div v-if="staffRequest.required_category">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-ink-400">Категория</dt>
                            <dd class="mt-1 font-medium text-ink-900">{{ staffRequest.required_category }}</dd>
                        </div>
                    </dl>

                    <div v-if="staffRequest.description" class="prose prose-ink mt-8 max-w-none whitespace-pre-line text-ink-700">
                        {{ staffRequest.description }}
                    </div>
                </div>

                <div>
                    <Card class="sticky top-24">
                        <div v-if="staffRequest.salary_from || staffRequest.salary_to" class="text-2xl font-extrabold text-emerald-600">
                            {{ staffRequest.salary_from ?? '—' }}–{{ staffRequest.salary_to ?? '—' }} сом
                        </div>
                        <p class="mt-1 text-sm text-ink-500">в месяц, до вычета налогов</p>

                        <Button v-if="hasApplied" variant="outline" size="lg" class="mt-6 w-full !cursor-default" disabled>
                            Отклик уже отправлен
                        </Button>
                        <Button v-else size="lg" class="mt-6 w-full" @click="apply">
                            Откликнуться
                        </Button>

                        <p class="mt-4 text-xs leading-relaxed text-ink-400">
                            После отклика ИИ задаст несколько вопросов по вакансии — это займёт пару минут.
                        </p>
                    </Card>
                </div>
            </div>
        </Container>
    </PublicLayout>
</template>
