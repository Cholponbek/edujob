<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Container from '@/Components/ui/Container.vue';
import Card from '@/Components/ui/Card.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    isInstitutionUser: { type: Boolean, default: false },
});
</script>

<template>
    <Head title="Личный кабинет — EduJob" />

    <PublicLayout>
        <Container class="py-16">
            <Card class="mx-auto max-w-lg text-center">
                <h1 class="text-xl font-bold text-ink-950">
                    {{ isInstitutionUser ? 'Кабинет учреждения скоро' : 'Личный кабинет' }}
                </h1>
                <p class="mt-2 text-sm text-ink-500">
                    {{ isInstitutionUser
                        ? 'Публикация вакансий и просмотр откликов уже доступны через API — визуальный кабинет учреждения в разработке.'
                        : 'Вы вошли в систему.' }}
                </p>
            </Card>
        </Container>
    </PublicLayout>
</template>
