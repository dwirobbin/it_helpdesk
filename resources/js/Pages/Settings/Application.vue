<script setup>
import { computed, ref, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import NumberInput from '@/Components/NumberInput.vue';
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { usePermission } from "@/Composables/permissions";
import { route } from 'ziggy';

const { hasAnyRole } = usePermission();

const page = usePage();

const setting = page.props.setting.data;

const src = ref(setting.logo);

const generateUrl = (filePath) => window.location.origin + filePath;

const form = useForm({
    name: setting.name,
    footer: setting.footer,
    company_name: setting.company_name,
    company_telp: setting.company_telp,
    company_address: setting.company_address,
    logo: setting.logo,
});

const setNullLogo = () => {
    src.value = null, form.logo = null;
}

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    form.logo = file;
};

const previewLogo = (file) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = (e) => (src.value = e.target.result);
}

watch(() => form.logo, (newFile) => {
    if (newFile instanceof File) {
        const isImage = newFile.type.startsWith('image/');
        if (isImage) {
            previewLogo(newFile);
        } else {
            src.value = null;
        }
    }
});

const onUpdate = () => form.post(route('settings.update'), {
    preserveScroll: true,
    onFinish: () => page.props.flash = null,
});
</script>

<template>

    <Head title="Pengaturan Aplikasi" />

    <AuthenticatedLayout>
        <div class="p-4 bg-white block sm:flex items-center justify-between shadow-sm border-b border-gray-200 lg:mt-1">
            <div class="w-full flex flex-col justify-left sm:flex-row sm:justify-between gap-y-3">
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 order-2 sm:order-1">Pengaturan Aplikasi</h1>
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
                                <span class="text-gray-400 ml-1 md:ml-2 text-sm font-medium" aria-current="page">
                                    Edit Pengaturan Aplikasi
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div v-if="hasAnyRole(['Super Admin'])" class="p-4">
            <div class="p-4 bg-white block border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="p-4 space-y-4">
                    <form @submit.prevent="onUpdate">
                        <h2 class="text-xl font-semibold text-gray-800 mb-6">Data Aplikasi</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="name" value="Nama Aplikasi" required />
                                <TextInput id="name" v-model="form.name" placeholder="My App" autofocus tabindex="1" />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>
                            <div>
                                <InputLabel for="footer" value="Footer Aplikasi" required />
                                <TextInput type="text" id="footer" v-model="form.footer" placeholder="All Rights Reserved" tabindex="2" />
                                <InputError class="mt-2" :message="form.errors.footer" />
                            </div>
                            <div class="order-3 md:order-4">
                                <InputLabel for="logo" value="Logo Aplikasi" />
                                <div class="flex flex-col items-center sm:flex-row sm:justify-between">
                                    <div>
                                        <img class="mb-4 rounded-lg w-28 h-28 sm:mb-0 object-cover"
                                            :src="[src ?? generateUrl('/assets/images/default-img.png')]" alt="Foto Keluhan">
                                        <input id="logo" type="file" accept="image/*" class="hidden" ref="input" @change="handleFileChange">
                                        <InputError class="mt-2" :message="form.errors.logo" />
                                    </div>

                                    <div class="flex items-center justify-end space-x-4 mb-1 sm:mb-0">
                                        <PrimaryButton type="button" @click="(event) => $refs.input.click()" :disabled="form.processing"
                                            class="inline-flex items-center">
                                            <svg class="w-4 h-4 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z">
                                                </path>
                                                <path d="M9 13h2v5a1 1 0 11-2 0v-5z"></path>
                                            </svg>
                                            Upload Picture
                                        </PrimaryButton>

                                        <button type="button" v-if="src !== null" @click="setNullLogo"
                                            class="text-gray-900 bg-white hover:bg-gray-100 focus:ring-4 focus:ring-primary-300 border border-gray-200 font-medium inline-flex items-center rounded-lg text-base px-3 py-2.5 text-center dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="mt-9 mb-7 divide-y-4 divide-solid" />

                        <h2 class="text-xl font-semibold text-gray-800 mb-6">Data Instansi</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-1 md:col-span-2">
                                <InputLabel for="company-name" value="Nama Instansi" required />
                                <TextInput type="text" id="company-name" v-model="form.company_name" placeholder="Instance" tabindex="4" />
                                <InputError class="mt-2" :message="form.errors.company_name" />
                            </div>
                            <div>
                                <InputLabel for="company-telp" value="No. Telp Instansi" />
                                <NumberInput id="company-telp" v-model="form.company_telp" placeholder="No. Telp Instansi..." tabindex="5" />
                                <InputError class="mt-2" :message="form.errors.company_telp" />
                            </div>
                            <div>
                                <InputLabel for="company-address" value="Alamat Instansi" />
                                <TextInput type="text" id="company-address" v-model="form.company_address" placeholder="Alamat Instansi..."
                                    tabindex="6" />
                                <InputError class="mt-2" :message="form.errors.company_address" />
                            </div>
                        </div>

                        <div class="col-span-2 flex items-center justify-center sm:justify-start space-x-4 mt-5 mb-1">
                            <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" tabindex="10">
                                Simpan Perubahan
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
