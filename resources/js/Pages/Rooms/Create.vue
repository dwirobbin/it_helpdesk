<script setup>
import { computed } from "vue";
import { useForm } from '@inertiajs/vue3';
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
    name: null,
});

const closeModal = () => emit('closeCreateModal');

const onSubmit = () => form.post(route('rooms.store'), {
    replace: true,
    onSuccess: () => {
        closeModal();
        usePage().props.flash = null;
    },
});
</script>

<template>

    <Head title="Tambah Ruangan" />

    <Modal :show="isShow" @close="closeModal" maxWidth="lg">
        <template v-slot:header>
            <h3 class="text-xl font-semibold">Tambah Ruangan Baru</h3>
        </template>

        <template v-slot:body>
            <form @submit.prevent="onSubmit" id="create" class="p-6">
                <InputLabel for="name" value="Nama" required />
                <TextInput type="text" id="name" v-model="form.name" placeholder="cth: R001" autofocus />
                <InputError class="mt-1.5" :message="form.errors.name" />
            </form>
        </template>

        <template v-slot:footer>
            <PrimaryButton type="submit" form="create" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Submit
            </PrimaryButton>
        </template>
    </Modal>
</template>
