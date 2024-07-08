<script setup>
import { computed } from "vue";
import { router, usePage } from '@inertiajs/vue3';
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { route } from 'ziggy';

const props = defineProps({
    complaint: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['closeConfirmModal']);

const isShow = computed(() => !!props.complaint);

const closeModal = () => emit('closeConfirmModal');

const onSolve = () => router.visit(route('tickets.solved', { ticket: props.complaint.slug }), {
    method: 'patch',
    replace: true,
    onFinish: () => {
        closeModal();
        usePage().props.flash = null;
    },
});
</script>

<template>

    <Head title="Delete Role" />

    <Modal :show="isShow" @close="closeModal" maxWidth="md">
        <!-- header -->
        <template #header />

        <!-- body -->
        <template #body>
            <div class="text-center p-6">
                <svg class="w-20 h-20 text-red-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>
                <h3 class="text-xl font-normal text-gray-500 mt-5">
                    Apakah anda yakin ingin menyelesaikan pengaduan dengan No. Tiket: <b>{{ complaint.ticket_number }}</b>?
                </h3>
            </div>
        </template>

        <!-- footer -->
        <template #footer>
            <div class="flex justify-center">
                <PrimaryButton type="button" @click="onSolve" class="mr-2" autofocus>
                    Ya, Saya yakin
                </PrimaryButton>
                <button type="button" @click="closeModal"
                    class="text-gray-900 bg-white hover:bg-gray-100 focus:ring-4 focus:ring-primary-300 border border-gray-200 font-medium inline-flex items-center rounded-lg text-base px-3 py-2.5 text-center dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                    Batal
                </button>
            </div>
        </template>
    </Modal>
</template>
