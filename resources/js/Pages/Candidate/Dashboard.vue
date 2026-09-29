<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Container from '@/Components/ui/Container.vue';
import Card from '@/Components/ui/Card.vue';
import Badge from '@/Components/ui/Badge.vue';
import Button from '@/Components/ui/Button.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    DocumentArrowUpIcon,
    SparklesIcon,
    ArrowPathIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    candidate: { type: Object, default: null },
    profileCompletion: { type: Number, default: 0 },
    applications: { type: Array, default: () => [] },
    recommendations: { type: Array, default: () => [] },
});

const page = usePage();
const resumeTab = ref('ru');

const uploadForm = useForm({ resume: null });
const fileInput = ref(null);

function onFileChange(e) {
    uploadForm.resume = e.target.files[0];
    uploadForm.post('/candidate/resume/upload', { preserveScroll: true, onSuccess: () => uploadForm.reset() });
}

function generateResume() {
    useForm({}).post('/candidate/resume/generate', { preserveScroll: true });
}

function refreshRecommendations() {
    useForm({}).post('/candidate/recommendations/refresh', { preserveScroll: true });
}

const STATUS_LABELS = {
    applied: 'Отклик отправлен',
    screening: 'ИИ-скрининг',
    screened: 'Проверено',
    hired: 'Приняты',
    rejected: 'Отклонено',
};

const STATUS_TONES = {
    applied: 'neutral',
    screening: 'warning',
    screened: 'brand',
    hired: 'success',
    rejected: 'danger',
};

const VERDICT_TONES = { green: 'success', yellow: 'warning', red: 'danger' };
</script>

<template>
    <Head title="Личный кабинет — EduJob" />

    <PublicLayout>
        <Container class="py-12">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-ink-950">
                        Здравствуйте, {{ page.props.auth.user.name }}
                    </h1>
                    <p class="mt-1 text-ink-500">Ваш профиль заполнен на {{ profileCompletion }}%</p>
                </div>
                <Link href="/vacancies"><Button variant="outline">Смотреть вакансии</Button></Link>
            </div>

            <div class="mt-4 h-2 w-full max-w-md overflow-hidden rounded-full bg-ink-100">
                <div class="h-full rounded-full bg-violet-600 transition-all" :style="{ width: `${profileCompletion}%` }" />
            </div>

            <div class="mt-10 grid gap-6 lg:grid-cols-3">
                <!-- Resume actions -->
                <Card class="lg:col-span-2">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-ink-950">Резюме</h2>
                        <SparklesIcon class="h-5 w-5 text-violet-600" />
                    </div>

                    <div v-if="candidate?.resume_text_ru || candidate?.resume_text_ky" class="mt-4">
                        <div class="flex gap-1 rounded-lg bg-ink-50 p-1 text-sm font-medium">
                            <button
                                class="flex-1 rounded-md py-1.5 transition"
                                :class="resumeTab === 'ru' ? 'bg-white text-ink-900 shadow-sm' : 'text-ink-500'"
                                @click="resumeTab = 'ru'"
                            >
                                Русский
                            </button>
                            <button
                                class="flex-1 rounded-md py-1.5 transition"
                                :class="resumeTab === 'ky' ? 'bg-white text-ink-900 shadow-sm' : 'text-ink-500'"
                                @click="resumeTab = 'ky'"
                            >
                                Кыргызча
                            </button>
                        </div>
                        <p class="mt-4 whitespace-pre-line text-sm leading-relaxed text-ink-700">
                            {{ resumeTab === 'ru' ? candidate.resume_text_ru : candidate.resume_text_ky }}
                        </p>
                    </div>
                    <p v-else class="mt-3 text-sm text-ink-500">
                        Резюме ещё не сформировано. Загрузите файл или сгенерируйте резюме из данных профиля.
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3 border-t border-ink-100 pt-6">
                        <input ref="fileInput" type="file" accept=".pdf,.jpg,.jpeg,.png" class="hidden" @change="onFileChange" />
                        <Button variant="outline" size="sm" :disabled="uploadForm.processing" @click="fileInput.click()">
                            <DocumentArrowUpIcon class="h-4 w-4" /> Загрузить файл (PDF/JPG/PNG)
                        </Button>
                        <Button variant="outline" size="sm" @click="generateResume">
                            <SparklesIcon class="h-4 w-4" /> Сгенерировать ИИ-резюме
                        </Button>
                    </div>
                    <p v-if="uploadForm.errors.resume" class="mt-2 text-sm text-rose-600">{{ uploadForm.errors.resume }}</p>
                </Card>

                <!-- Profile summary -->
                <Card>
                    <h2 class="text-lg font-bold text-ink-950">Профиль</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-ink-500">Предмет</dt>
                            <dd class="font-medium text-ink-900">{{ candidate?.subject ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-ink-500">Категория</dt>
                            <dd class="font-medium text-ink-900">{{ candidate?.teaching_category ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-ink-500">Готов к переезду</dt>
                            <dd class="font-medium text-ink-900">{{ candidate?.relocation_ready ? 'Да' : 'Нет' }}</dd>
                        </div>
                    </dl>
                </Card>
            </div>

            <div class="mt-10 grid gap-6 lg:grid-cols-2">
                <!-- Applications -->
                <Card>
                    <h2 class="text-lg font-bold text-ink-950">Мои отклики</h2>
                    <p v-if="applications.length === 0" class="mt-3 text-sm text-ink-500">
                        Вы пока никуда не откликались — самое время
                        <Link href="/vacancies" class="font-medium text-violet-600 underline">посмотреть вакансии</Link>.
                    </p>
                    <ul v-else class="mt-4 space-y-4">
                        <li v-for="app in applications" :key="app.id" class="flex items-start justify-between gap-3 border-b border-ink-100 pb-4 last:border-0 last:pb-0">
                            <div>
                                <Link :href="`/vacancies/${app.staff_request.id}`" class="font-medium text-ink-900 hover:text-violet-600">
                                    {{ app.staff_request.title }}
                                </Link>
                                <p class="text-sm text-ink-500">{{ app.staff_request.institution.name }}</p>
                                <p v-if="app.screening_verdict" class="mt-1 text-xs text-ink-400">
                                    Соответствие: {{ app.screening_match_percent }}%
                                </p>
                            </div>
                            <Badge :tone="app.screening_verdict ? VERDICT_TONES[app.screening_verdict] : STATUS_TONES[app.status]">
                                {{ STATUS_LABELS[app.status] }}
                            </Badge>
                        </li>
                    </ul>
                </Card>

                <!-- Recommendations -->
                <Card>
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-ink-950">Рекомендации</h2>
                        <button class="text-ink-400 hover:text-ink-900" title="Обновить" @click="refreshRecommendations">
                            <ArrowPathIcon class="h-4 w-4" />
                        </button>
                    </div>
                    <p v-if="recommendations.length === 0" class="mt-3 text-sm text-ink-500">
                        Заполните профиль, чтобы получить персональные рекомендации вакансий.
                    </p>
                    <ul v-else class="mt-4 space-y-4">
                        <li v-for="rec in recommendations" :key="rec.id" class="border-b border-ink-100 pb-4 last:border-0 last:pb-0">
                            <div class="flex items-start justify-between gap-3">
                                <Link :href="`/vacancies/${rec.staff_request.id}`" class="font-medium text-ink-900 hover:text-violet-600">
                                    {{ rec.staff_request.title }}
                                </Link>
                                <Badge tone="brand">{{ rec.score }}%</Badge>
                            </div>
                            <p class="mt-1 text-sm text-ink-500">{{ rec.staff_request.institution.name }}</p>
                        </li>
                    </ul>
                </Card>
            </div>
        </Container>
    </PublicLayout>
</template>
