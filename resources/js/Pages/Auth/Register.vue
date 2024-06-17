<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import PasswordInput from '@/Components/PasswordInput.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: null,
    email: null,
    password: null,
    password_confirmation: null,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout title="Buat Akun Baru Anda" max-width="2xl">

        <Head title="Register" />

        <form @submit.prevent="submit" class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <InputLabel for="name" value="Nama" required />
                <TextInput id="name" type="text" v-model="form.name" placeholder="name" autofocus autocomplete="name" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" required />
                <TextInput id="email" type="email" v-model="form.email" placeholder="name@company.com" autocomplete="email" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Kata Sandi" required />
                <PasswordInput id="password" v-model="form.password" placeholder="••••••••" autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel for="password_confirmation" value="Konfirmasi Kata Sandi" required />
                <PasswordInput id="password_confirmation" v-model="form.password_confirmation" placeholder="••••••••"
                    autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="flex items-center justify-start">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Buat Akun
                </PrimaryButton>
            </div>

            <div class="col-start-1 text-sm font-medium text-gray-500 dark:text-gray-400">
                Sudah punya akun?
                <Link :href="route('login')" class="text-teal-500 hover:underline">Masuk ke sini</Link>
            </div>
        </form>
    </GuestLayout>
</template>
