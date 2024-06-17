<script setup>
import { ref } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import InputError from "@/Components/InputError.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Delete from './DeleteProfilePicture.vue';
import { route } from 'ziggy';

const user = usePage().props.auth.user.data;

const src = ref(user.photo);
const isShowConfirmDelete = ref(false);

const form = useForm({
    photo: null
});

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    form.photo = file;

    onSubmit(file);
};

const onSubmit = (file) => form.post(route('profile.avatar.update'), {
    onSuccess: () => handleSuccess(file),
    onError: (errors) => form.errors.photo = errors.photo,
});

const handleSuccess = (file) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = (e) => updateAvatarSrc(e.target.result);
    form.errors.photo = null;
}

const updateAvatarSrc = (value) => (src.value = value);

const toggleConfirmDelete = () => (isShowConfirmDelete.value = !isShowConfirmDelete.value);
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg text-left font-medium text-gray-900 dark:text-white">
                Foto Profil
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Memperbarui Foto Profil Anda.
            </p>
        </header>

        <div class="flex flex-col items-center sm:flex-row sm:justify-between mt-6">
            <div>
                <img class="mb-4 rounded-lg w-28 h-28 sm:mb-0 object-cover" :src="src" alt="Profile Picture">
                <input type="file" accept="image/*" class="hidden" ref="input" @change="handleFileChange">
                <InputError class="mt-1.5" :message="form.errors.photo" />
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
                    Upload Picture
                </PrimaryButton>

                <button type="button" v-if="!src.includes('default-img')" @click="toggleConfirmDelete"
                    class="text-gray-900 bg-white hover:bg-gray-100 focus:ring-4 focus:ring-primary-300 border border-gray-200 font-medium inline-flex items-center rounded-lg text-base px-3 py-2.5 text-center dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                    Hapus
                </button>
            </div>
        </div>

        <!-- Delete -->
        <Delete v-if="isShowConfirmDelete" :show="isShowConfirmDelete" @close-confirm-modal="toggleConfirmDelete"
            @update-avatar-src="updateAvatarSrc(user.photo)" />
    </section>
</template>
