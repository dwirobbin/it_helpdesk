<script setup>
import { computed } from "vue";
import { useForm, usePage } from '@inertiajs/vue3';
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { route } from 'ziggy';

const props = defineProps({
    show: {
        type: Boolean,
        required: true,
    },
});

const emit = defineEmits(['closeCreateModal']);

const isShow = computed(() => props.show);

const form = useForm({
    section: null,
});

const closeModal = () => emit('closeCreateModal');

const onSubmit = () => form.post(route('departments.store'), {
    replace: true,
    onSuccess: () => {
        closeModal();
        usePage().props.flash = null;
    },
});
</script>

<template>

    <Head title="Create Department" />

    <Modal :show="isShow" @close="closeModal" maxWidth="lg">
        <template v-slot:header>
            <h3 class="text-xl font-semibold">Tambah Divisi Baru</h3>
        </template>

        <template v-slot:body>
            <form @submit.prevent="onSubmit" id="create" class="p-6">
                <InputLabel for="section" value="Divisi" required />
                <TextInput type="text" id="section" v-model="form.section" placeholder="cth: HRD" autofocus />
                <InputError class="mt-1.5" :message="form.errors.section" />
            </form>
        </template>

        <template v-slot:footer>
            <PrimaryButton type="submit" form="create" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Submit
            </PrimaryButton>
        </template>
    </Modal>
</template>
