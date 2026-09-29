<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Container from '@/Components/ui/Container.vue';
import Button from '@/Components/ui/Button.vue';
import Card from '@/Components/ui/Card.vue';
import Badge from '@/Components/ui/Badge.vue';
import InstitutionAvatar from '@/Components/ui/InstitutionAvatar.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    MagnifyingGlassIcon,
    SparklesIcon,
    BuildingLibraryIcon,
    UserGroupIcon,
    LanguageIcon,
    MapIcon,
    ClockIcon,
} from '@heroicons/vue/24/outline';
import { EMPLOYMENT_TYPE_LABELS } from '@/lib/labels';

const props = defineProps({
    stats: { type: Object, default: () => ({ vacancies: 0, institutions: 0, regions: 0 }) },
    featuredVacancies: { type: Array, default: () => [] },
});

const query = ref('');

function search() {
    router.get('/vacancies', query.value ? { q: query.value } : {});
}

const activeSubject = ref(null);

const topSubjects = computed(() => {
    const counts = {};
    props.featuredVacancies.forEach((v) => {
        if (v.subject) counts[v.subject] = (counts[v.subject] || 0) + 1;
    });
    return Object.keys(counts)
        .sort((a, b) => counts[b] - counts[a])
        .slice(0, 6);
});

const visibleVacancies = computed(() => {
    const list = activeSubject.value
        ? props.featuredVacancies.filter((v) => v.subject === activeSubject.value)
        : props.featuredVacancies;
    return list.slice(0, 8);
});

const steps = [
    {
        title: 'Создайте резюме за 5 минут',
        body: 'Заполните профиль вручную, опишите себя своими словами или загрузите готовый файл — ИИ сам разберёт документ и соберёт структурированный профиль.',
    },
    {
        title: 'Откликайтесь на вакансии',
        body: 'Фильтруйте по предмету, региону и уровню образования. После отклика ИИ задаст несколько профессиональных вопросов по вакансии.',
    },
    {
        title: 'Получите обратную связь',
        body: 'Учреждение видит ваш профиль и результат ИИ-скрининга — решение принимается быстрее, чем при ручном разборе анкет.',
    },
];

const features = [
    {
        icon: LanguageIcon,
        title: 'Резюме на русском и кыргызском',
        body: 'ИИ формирует резюме сразу на двух языках — учреждениям в регионах и в Бишкеке удобно читать на своём.',
    },
    {
        icon: MapIcon,
        title: 'Готовность к переезду',
        body: 'Отдельно отмечаете готовность работать в другом регионе и условия — школам вне Бишкека проще закрыть дефицит кадров.',
    },
    {
        icon: ClockIcon,
        title: 'Совместительство и частичная занятость',
        body: 'Ищете 0.5 ставки или доп. нагрузку — фильтр по формату занятости работает в обе стороны.',
    },
];
</script>

<template>
    <Head title="EduJob — работа в сфере образования" />

    <PublicLayout>
        <!-- Hero -->
        <section class="relative overflow-hidden bg-white">
            <!-- decorative shapes -->
            <div class="pointer-events-none absolute inset-0 hidden lg:block">
                <div class="absolute left-[8%] top-24 h-10 w-10 rounded-lg bg-violet-100" />
                <div class="absolute left-[14%] top-64 h-2 w-16 rounded-full bg-coral-300" />
                <div class="absolute right-[10%] top-16 h-2 w-2.5 rounded-full bg-amber-400" />
                <div class="absolute right-[16%] top-28 h-16 w-3 rounded-full bg-violet-200" />
                <div class="absolute right-[9%] top-72 h-20 w-3 rounded-full bg-sky-300" />
                <div class="absolute left-[6%] top-[26rem] h-16 w-16 rounded-full border-8 border-emerald-100" />
            </div>

            <Container class="relative py-20 text-center sm:py-28">
                <Badge tone="brand" class="!bg-violet-100 !text-violet-700">
                    <SparklesIcon class="h-3.5 w-3.5" /> ИИ-подбор для сферы образования
                </Badge>

                <h1 class="mx-auto mt-8 max-w-3xl text-5xl font-extrabold leading-[1.1] tracking-tight text-ink-950 sm:text-6xl">
                    Помогаем
                    <span class="text-violet-600">найти</span>
                    работу в образовании
                </h1>

                <p class="mx-auto mt-6 max-w-xl text-lg text-ink-500">
                    Бесплатное ИИ-резюме для кандидатов и ИИ-скрининг откликов для школ и садов Кыргызстана.
                </p>

                <form class="mx-auto mt-10 flex max-w-xl overflow-hidden rounded-2xl bg-white p-2 shadow-card ring-1 ring-ink-100" @submit.prevent="search">
                    <div class="flex flex-1 items-center gap-2 px-3">
                        <MagnifyingGlassIcon class="h-5 w-5 shrink-0 text-ink-400" />
                        <input
                            v-model="query"
                            type="text"
                            placeholder="Учитель математики, психолог, воспитатель…"
                            class="w-full border-0 bg-transparent py-2.5 text-sm text-ink-900 placeholder:text-ink-400 focus:outline-none focus:ring-0"
                        />
                    </div>
                    <Button type="submit">Найти</Button>
                </form>

                <dl class="mx-auto mt-16 grid max-w-lg grid-cols-3 gap-6">
                    <div>
                        <dd class="text-3xl font-extrabold text-ink-950">{{ stats.vacancies }}</dd>
                        <dt class="mt-1 text-sm text-ink-500">вакансий</dt>
                    </div>
                    <div>
                        <dd class="text-3xl font-extrabold text-ink-950">{{ stats.institutions }}</dd>
                        <dt class="mt-1 text-sm text-ink-500">учреждений</dt>
                    </div>
                    <div>
                        <dd class="text-3xl font-extrabold text-ink-950">{{ stats.regions }}</dd>
                        <dt class="mt-1 text-sm text-ink-500">регионов</dt>
                    </div>
                </dl>
            </Container>
        </section>

        <!-- Featured vacancies -->
        <section v-if="featuredVacancies.length > 0" class="border-t border-ink-100 bg-ink-50/50 py-20">
            <Container>
                <h2 class="text-center text-3xl font-extrabold tracking-tight text-ink-950">Свежие вакансии</h2>

                <div v-if="topSubjects.length > 0" class="mt-8 flex flex-wrap justify-center gap-2">
                    <button
                        class="rounded-full px-4 py-2 text-sm font-semibold transition"
                        :class="activeSubject === null ? 'bg-ink-950 text-white' : 'bg-white text-ink-600 ring-1 ring-ink-200 hover:bg-ink-100'"
                        @click="activeSubject = null"
                    >
                        Все
                    </button>
                    <button
                        v-for="subject in topSubjects"
                        :key="subject"
                        class="rounded-full px-4 py-2 text-sm font-semibold transition"
                        :class="activeSubject === subject ? 'bg-ink-950 text-white' : 'bg-white text-ink-600 ring-1 ring-ink-200 hover:bg-ink-100'"
                        @click="activeSubject = subject"
                    >
                        {{ subject }}
                    </button>
                </div>

                <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <Link
                        v-for="v in visibleVacancies"
                        :key="v.id"
                        :href="`/vacancies/${v.id}`"
                    >
                        <Card hoverable class="h-full">
                            <div class="flex items-start gap-3">
                                <InstitutionAvatar :name="v.institution.name" size="sm" />
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-ink-900">{{ v.institution.name }}</p>
                                    <p class="text-xs text-ink-400">{{ EMPLOYMENT_TYPE_LABELS[v.employment_type] }}</p>
                                </div>
                            </div>
                            <h3 class="mt-4 font-bold leading-snug text-ink-950">{{ v.title }}</h3>
                            <p v-if="v.salary_from || v.salary_to" class="mt-3 font-semibold text-emerald-600">
                                {{ v.salary_from ? `от ${v.salary_from}` : `до ${v.salary_to}` }} сом
                            </p>
                        </Card>
                    </Link>
                </div>

                <div class="mt-10 text-center">
                    <Button as="a" href="/vacancies" variant="secondary">Все вакансии →</Button>
                </div>
            </Container>
        </section>

        <!-- Value props -->
        <section class="py-20 sm:py-24">
            <Container>
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-extrabold tracking-tight text-ink-950">Одна платформа — два сценария</h2>
                    <p class="mt-4 text-ink-500">
                        Кандидатам — бесплатный инструмент найти работу. Учреждениям — быстрый и точный подбор.
                    </p>
                </div>

                <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Card hoverable>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                            <UserGroupIcon class="h-6 w-6" />
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-ink-950">Для кандидатов</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink-500">
                            Бесплатное ИИ-резюме на русском и кыргызском, подбор подходящих вакансий
                            и понятный статус по каждому отклику.
                        </p>
                    </Card>

                    <Card hoverable>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-ink-50 text-ink-700">
                            <BuildingLibraryIcon class="h-6 w-6" />
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-ink-950">Для учреждений</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink-500">
                            Публикация вакансий с указанием предмета, уровня образования и категории —
                            заявки приходят уже отфильтрованными по компетенциям.
                        </p>
                    </Card>

                    <Card hoverable>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <SparklesIcon class="h-6 w-6" />
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-ink-950">ИИ-скрининг</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink-500">
                            После отклика кандидат отвечает на несколько профессиональных вопросов —
                            ИИ выставляет вердикт и процент соответствия вакансии.
                        </p>
                    </Card>
                </div>
            </Container>
        </section>

        <!-- How it works -->
        <section class="bg-ink-50/60 py-20 sm:py-24">
            <Container>
                <h2 class="text-center text-3xl font-extrabold tracking-tight text-ink-950">Как это работает</h2>

                <div class="mt-14 grid gap-8 lg:grid-cols-3">
                    <div v-for="(step, i) in steps" :key="step.title" class="relative">
                        <span class="text-5xl font-extrabold text-ink-200">{{ String(i + 1).padStart(2, '0') }}</span>
                        <h3 class="mt-3 text-lg font-bold text-ink-950">{{ step.title }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink-500">{{ step.body }}</p>
                    </div>
                </div>
            </Container>
        </section>

        <!-- Features tailored to KR -->
        <section class="py-20 sm:py-24">
            <Container>
                <div class="grid gap-10 lg:grid-cols-3">
                    <div v-for="feature in features" :key="feature.title" class="flex gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-ink-950 text-white">
                            <component :is="feature.icon" class="h-5 w-5" />
                        </div>
                        <div>
                            <h3 class="font-bold text-ink-950">{{ feature.title }}</h3>
                            <p class="mt-1 text-sm leading-relaxed text-ink-500">{{ feature.body }}</p>
                        </div>
                    </div>
                </div>
            </Container>
        </section>

        <!-- CTA band -->
        <section class="pb-24">
            <Container>
                <div class="flex flex-col items-center gap-6 rounded-3xl bg-ink-950 px-8 py-14 text-center sm:px-16">
                    <h2 class="text-3xl font-extrabold text-white">Готовы начать?</h2>
                    <p class="max-w-lg text-ink-300">
                        Регистрация занимает меньше минуты — только номер телефона и код из SMS.
                    </p>
                    <Button as="a" href="/login" size="lg">Создать резюме бесплатно</Button>
                </div>
            </Container>
        </section>
    </PublicLayout>
</template>
