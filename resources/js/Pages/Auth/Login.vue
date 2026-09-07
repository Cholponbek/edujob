<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
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
        <Head title="Вход" />

        <form v-if="step === 'phone'" @submit.prevent="requestCode">
            <div>
                <InputLabel for="phone" value="Номер телефона" />

                <TextInput
                    id="phone"
                    type="tel"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                    placeholder="+996 5XX XXXXXX"
                    required
                    autofocus
                    autocomplete="tel"
                />

                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Получить код
                </PrimaryButton>
            </div>
        </form>

        <form v-else @submit.prevent="verifyCode">
            <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                Код отправлен на {{ form.phone }}.
                <button
                    type="button"
                    class="underline"
                    @click="changePhone"
                >
                    Изменить номер
                </button>
            </p>

            <div>
                <InputLabel for="code" value="Код из SMS" />

                <TextInput
                    id="code"
                    type="text"
                    inputmode="numeric"
                    class="mt-1 block w-full"
                    v-model="form.code"
                    required
                    autofocus
                />

                <InputError class="mt-2" :message="form.errors.code" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Войти
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
