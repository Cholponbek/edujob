<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Container from '@/Components/ui/Container.vue';
import Button from '@/Components/ui/Button.vue';
import Card from '@/Components/ui/Card.vue';
import Badge from '@/Components/ui/Badge.vue';
import { Head } from '@inertiajs/vue3';
import {
    SparklesIcon,
    BuildingLibraryIcon,
    UserGroupIcon,
    LanguageIcon,
    MapIcon,
    ClockIcon,
} from '@heroicons/vue/24/outline';

defineProps({
    stats: { type: Object, default: () => ({ vacancies: 0, institutions: 0 }) },
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
        <section class="relative overflow-hidden bg-ink-950">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_rgba(240,89,42,0.18),_transparent_55%)]" />
            <Container class="relative grid gap-12 py-20 sm:py-28 lg:grid-cols-2 lg:items-center lg:py-32">
                <div>
                    <Badge tone="warning" class="!bg-coral-500/15 !text-coral-300">
                        <SparklesIcon class="h-3.5 w-3.5" /> ИИ-подбор для сферы образования
                    </Badge>

                    <h1 class="mt-6 text-4xl font-extrabold leading-tight tracking-tight text-white sm:text-5xl">
                        Кадры для школ и садов —
                        <span class="text-coral-400">без недель ожидания</span>
                    </h1>

                    <p class="mt-6 max-w-xl text-lg leading-relaxed text-ink-300">
                        EduJob подбирает педагогов для учреждений Кыргызстана с помощью ИИ-скрининга,
                        а кандидатам помогает собрать профессиональное резюме за считаные минуты — бесплатно.
                    </p>

                    <div class="mt-10 flex flex-col gap-3 sm:flex-row">
                        <Button as="a" href="/login" size="lg">Создать резюме бесплатно</Button>
                        <Button as="a" href="/vacancies" variant="outline" size="lg" class="!border-ink-700 !text-white hover:!bg-white/5">
                            Смотреть вакансии
                        </Button>
                    </div>

                    <dl class="mt-14 grid max-w-md grid-cols-2 gap-6 border-t border-white/10 pt-8">
                        <div>
                            <dt class="text-sm text-ink-400">Открытых вакансий</dt>
                            <dd class="mt-1 text-3xl font-bold text-white">{{ stats.vacancies }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-ink-400">Учреждений на платформе</dt>
                            <dd class="mt-1 text-3xl font-bold text-white">{{ stats.institutions }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="relative hidden lg:block">
                    <div class="absolute -inset-8 rounded-[2rem] bg-gradient-to-br from-coral-500/20 to-transparent blur-2xl" />
                    <Card class="relative !bg-white/95 backdrop-blur">
                        <div class="flex items-center justify-between">
                            <Badge tone="success">ИИ-скрининг завершён</Badge>
                            <span class="text-sm font-semibold text-ink-900">92%</span>
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-ink-900">Учитель математики</h3>
                        <p class="text-sm text-ink-500">Школа №5, г. Бишкек · полная ставка</p>
                        <div class="mt-4 rounded-xl bg-ink-50 p-4 text-sm text-ink-600">
                            «Кандидат уверенно ответил на вопросы по методике преподавания
                            алгебры для 8–9 классов, есть опыт подготовки к ОРТ.»
                        </div>
                        <div class="mt-4 flex gap-2">
                            <Badge>Высшая категория</Badge>
                            <Badge>Готов к переезду</Badge>
                        </div>
                    </Card>
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
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-coral-50 text-coral-500">
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
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-ink-900 text-white">
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
