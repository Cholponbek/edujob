<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Input from '@/Components/ui/Input.vue';
import Button from '@/Components/ui/Button.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, watchEffect } from 'vue';

const page = usePage();

// Два шага одной формы: сначала телефон (запрос кода), затем код
// (подтверждение и вход/неявная регистрация) — PhoneOtpController
// обслуживает оба действия одним флоу.
const step = ref('phone');

const form = useForm({
    phone: '',
    code: '',
});

watchEffect(() => {
    if (page.props.flash?.status === 'otp-sent') {
        step.value = 'code';
    }
});

const requestCode = () => {
    form.post(route('otp.request'), {
        preserveScroll: true,
    });
};

const verifyCode = () => {
    form.post(route('otp.verify'), {
        preserveScroll: true,
        onError: () => form.reset('code'),
    });
};

const changePhone = () => {
    step.value = 'phone';
    form.reset('code');
};
</script>

<template>
    <GuestLayout>
        <Head title="Вход — EduJob" />

        <h1 class="text-2xl font-extrabold tracking-tight text-ink-950">
            {{ step === 'phone' ? 'Вход или регистрация' : 'Введите код' }}
        </h1>
        <p class="mt-2 text-sm text-ink-500">
            {{ step === 'phone'
                ? 'По номеру телефона — без пароля.'
                : `Код отправлен на ${form.phone}.` }}
        </p>

        <form v-if="step === 'phone'" class="mt-8 space-y-5" @submit.prevent="requestCode">
            <div>
                <InputLabel for="phone" value="Номер телефона" />
                <Input
                    id="phone"
                    type="tel"
                    class="mt-1.5"
                    v-model="form.phone"
                    placeholder="+996 5XX XXXXXX"
                    required
                    autofocus
                    autocomplete="tel"
                />
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <Button type="submit" size="lg" class="w-full" :disabled="form.processing">
                Получить код
            </Button>
        </form>

        <form v-else class="mt-8 space-y-5" @submit.prevent="verifyCode">
            <div>
                <div class="flex items-center justify-between">
                    <InputLabel for="code" value="Код из SMS" />
                    <button type="button" class="text-sm font-medium text-ink-500 underline underline-offset-2 hover:text-ink-900" @click="changePhone">
                        Изменить номер
                    </button>
                </div>
                <Input
                    id="code"
                    type="text"
                    inputmode="numeric"
                    class="mt-1.5 tracking-[0.3em]"
                    v-model="form.code"
                    required
                    autofocus
                />
                <InputError class="mt-2" :message="form.errors.code" />
            </div>

            <Button type="submit" size="lg" class="w-full" :disabled="form.processing">
                Войти
            </Button>
        </form>
    </GuestLayout>
</template>
