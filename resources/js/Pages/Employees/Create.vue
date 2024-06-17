<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import InputLabel from "@/Components/InputLabel.vue";
import NumberInput from '@/Components/NumberInput.vue';
import TextInput from "@/Components/TextInput.vue";
import PasswordInput from '@/Components/PasswordInput.vue';
import ToggleInput from '@/Components/ToggleInput.vue';
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import VueMultiselect from 'vue-multiselect';
import { route } from 'ziggy';

const props = defineProps({
    positions: {
        type: Object,
        required: true,
    },
    departments: {
        type: Object,
        required: true,
    },
    roles: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    nik: null,
    name: null,
    position: null,
    department: null,
    role: null,
    email: null,
    password: null,
    password_confirmation: null,
    is_active_account: false,
});

const onSubmit = () => form.post(route('employees.store'), {
    replace: true,
    onSuccess: () => {
        usePage().props.flash = null;
    },
});
</script>

<template>

    <Head title="Tambah Karyawan" />

    <AuthenticatedLayout>
        <div class="p-4 bg-white block sm:flex items-center justify-between shadow-sm border-b border-gray-200 lg:mt-1">
            <div class="w-full flex flex-col justify-left sm:flex-row sm:justify-between gap-y-3">
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 order-2 sm:order-1">Tambah Karyawan</h1>
                <nav class="flex order-1 sm:order-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2">
                        <li class="inline-flex items-center">
                            <Link :href="route('dashboard')" class="text-gray-700 hover:text-gray-900 inline-flex items-center">
                            <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                                </path>
                            </svg>
                            Home
                            </Link>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <Link :href="route('employees.index')"
                                    class="text-gray-700 hover:text-gray-900 ml-1 md:ml-2 text-sm font-medium">
                                Karyawan
                                </Link>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-400 ml-1 md:ml-2 text-sm font-medium" aria-current="page">Tambah</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="p-4">
            <div class="p-4 bg-white block border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="p-4 space-y-4">
                    <form @submit.prevent="onSubmit">
                        <h2 class="text-xl font-semibold text-gray-800 mb-6">Data Karyawan</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-1 md:col-span-2">
                                <InputLabel for="nik" value="NIK" required />
                                <NumberInput id="nik" v-model="form.nik" placeholder="e.g. 2934205823034508" autofocus tabindex="1" />
                                <InputError class="mt-1.5" :message="form.errors.nik" />
                            </div>
                            <div>
                                <InputLabel for="name" value="Nama" required />
                                <TextInput type="text" id="name" v-model="form.name" placeholder="e.g. Xonny" tabindex="2" />
                                <InputError class="mt-1.5" :message="form.errors.name" />
                            </div>
                            <div class="order-3 md:order-4">
                                <InputLabel for="position" value="Jabatan" />
                                <VueMultiselect id="position" v-model="form.position" track-by="slug" label="title" :options="positions.data"
                                    placeholder="Pilih" tabindex="3" />
                                <InputError class="mt-1.5" :message="form.errors.position" />
                            </div>
                            <div class="order-4 md:order-3">
                                <InputLabel for="department" value="Divisi" />
                                <VueMultiselect id="department" v-model="form.department" track-by="slug" label="section"
                                    :options="departments.data" placeholder="Pilih" tabindex="4" />
                                <InputError class="mt-1.5" :message="form.errors.department" />
                            </div>
                            <div class="order-5">
                                <InputLabel for="role" value="Level User" required />
                                <VueMultiselect id="role" v-model="form.role" track-by="id" label="name" :options="roles.data"
                                    placeholder="Pilih" tabindex="5" />
                                <InputError class="mt-1.5" :message="form.errors.role" />
                            </div>
                        </div>

                        <hr class="mt-9 mb-7 divide-y-4 divide-solid" />

                        <h2 class="text-xl font-semibold text-gray-800 mb-6">Data Akun</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-1 md:col-span-2">
                                <InputLabel for="email" value="Email" required />
                                <TextInput type="email" id="email" v-model="form.email" placeholder="e.g. xonny@domain.com" tabindex="6" />
                                <InputError class="mt-1.5" :message="form.errors.email" />
                            </div>
                            <div>
                                <InputLabel for="password" value="Kata Sandi" required />
                                <PasswordInput id="password" v-model="form.password" placeholder="••••••••" autocomplete="new-password"
                                    tabindex="7" />
                                <InputError class="mt-2" :message="form.errors.password" />
                            </div>
                            <div>
                                <InputLabel for="password_confirmation" value="Konfirmasi Kata Sandi" required />
                                <PasswordInput id="password_confirmation" v-model="form.password_confirmation" placeholder="••••••••"
                                    autocomplete="new-password" tabindex="8" />
                                <InputError class="mt-2" :message="form.errors.password_confirmation" />
                            </div>
                            <div>
                                <InputLabel for="is-active-account" value="Aktifkan Akun?" />
                                <ToggleInput id="is-active-account" v-model:checked="form.is_active_account" tabindex="9" />
                                <InputError class="mt-2" :message="form.errors.is_active_account" />
                            </div>
                        </div>

                        <div class="col-span-2 flex items-center justify-center sm:justify-start space-x-4 mt-5 mb-1">
                            <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" tabindex="10">
                                Submit
                            </PrimaryButton>
                            <Link :href="route('employees.index')" method="get" as="button" type="button"
                                class="text-gray-900 bg-gray-50 hover:bg-gray-100 focus:ring-4 focus:ring-primary-300 border border-gray-300 font-medium inline-flex items-center rounded-lg text-base px-4 py-2.5 text-center"
                                tabindex="11">
                            Batal
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
