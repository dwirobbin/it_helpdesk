<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PasswordInput from '@/Components/PasswordInput.vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: null,
    password: null,
    remember: null,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout title="Masuk ke Aplikasi">

        <Head title="Log in" />

        <form @submit.prevent="submit" class="mt-8 space-y-6">
            <div>
                <InputLabel for="email" value="Email" required />
                <TextInput id="email" type="email" v-model="form.email" placeholder="name@company.com" autofocus autocomplete="email" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Kata sandi" required />
                <PasswordInput id="password" v-model="form.password" placeholder="••••••••" autocomplete="current-password" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <label class="flex items-center">
                            <Checkbox name="remember" v-model:checked="form.remember" />
                            <span class="ms-2 text-sm text-gray-600">Ingat saya</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-start mt-4">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Masuk ke akun anda
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
