<script setup>
import { ref, watch } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import NumberInput from '@/Components/NumberInput.vue';
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { route } from 'ziggy';

const props = defineProps({
    setting: {
        type: Object,
        required: true,
    }
});

const companyLogoSrc = ref(props.setting.company_logo);

const generateUrl = (filePath) => window.location.origin + filePath;

const form = useForm({
    company_name: props.setting.company_name,
    company_telp: props.setting.company_telp,
    company_address: props.setting.company_address,
    company_logo: props.setting.company_logo,
});

const setNullLogo = () => {
    companyLogoSrc.value = null, form.company_logo = null;
}

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    form.company_logo = file;
};

const previewLogo = (file) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = (e) => (companyLogoSrc.value = e.target.result);
}

watch(() => form.company_logo, (newFile) => {
    if (newFile instanceof File) {
        const isImage = newFile.type.startsWith('image/');
        if (isImage) {
            previewLogo(newFile);
        } else {
            companyLogoSrc.value = null;
        }
    }
});

const onUpdate = () => form.post(route('settings.company-information.update'), {
    preserveScroll: true,
    onFinish: () => usePage().props.flash = null,
});
</script>

<template>
    <form @submit.prevent="onUpdate">
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
                <TextInput type="text" id="company-address" v-model="form.company_address" placeholder="Alamat Instansi..." tabindex="6" />
                <InputError class="mt-2" :message="form.errors.company_address" />
            </div>
            <div class="order-3 md:order-4">
                <InputLabel for="company-logo" value="Logo Instansi" />
                <div class="flex flex-col items-center sm:flex-row sm:justify-between">
                    <div>
                        <img class="mb-4 rounded-lg w-28 h-28 sm:mb-0 object-cover"
                            :src="[companyLogoSrc ?? generateUrl('/assets/images/default-img.png')]" alt="Foto Keluhan">
                        <input id="company-logo" type="file" accept="image/*" class="hidden" ref="input" @change="handleFileChange">
                        <InputError class="mt-2" :message="form.errors.company_logo" />
                    </div>

                    <div class="flex items-center justify-end space-x-4 mb-1 sm:mb-0">
                        <PrimaryButton type="button" @click="(event) => $refs.input.click()" :disabled="form.processing"
                            class="inline-flex items-center">
                            <svg class="w-4 h-4 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z">
                                </path>
                                <path d="M9 13h2v5a1 1 0 11-2 0v-5z"></path>
                            </svg>
                            Unggah Gambar
                        </PrimaryButton>

                        <button type="button" v-if="companyLogoSrc !== null" @click="setNullLogo"
                            class="text-gray-900 bg-white hover:bg-gray-100 focus:ring-4 focus:ring-primary-300 border border-gray-200 font-medium inline-flex items-center rounded-lg text-base px-3 py-2.5 text-center dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-2 flex justify-start mt-5 mb-1">
            <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" tabindex="10">
                Simpan
            </PrimaryButton>
        </div>
    </form>
</template>
