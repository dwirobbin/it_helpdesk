<script setup>
import { computed } from "vue";
import { useForm, router, usePage } from '@inertiajs/vue3';
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { route } from 'ziggy';

const props = defineProps({
    position: {
        type: Object,
        required: true,
    },
});

const isShow = computed(() => !!props.position);

const form = useForm({
    current_page: usePage().props.positions.meta.current_page,
    last_page: usePage().props.positions.meta.last_page,
    title: props.position?.data.title,
});

const closeModal = () => router.visit(route('positions.index'), {
    method: 'get',
    replace: true,
    preserveState: true,
    onSuccess: () => {
        form.reset();
        form.clearErrors();
    },
});

const onUpdate = () => form.put(route('positions.update', { position: props.position.data.slug }), {
    replace: true,
    onFinish: () => usePage().props.flash = null,
});
</script>

<template>

    <Head title="Edit Position" />

    <Modal :show="isShow" @close="closeModal" maxWidth="lg">
        <template v-slot:header>
            <h3 class="text-xl font-semibold">Edit Posisi</h3>
        </template>

        <template v-slot:body>
            <form @submit.prevent="onUpdate" id="update" class="p-6">
                <InputLabel for="title" value="Title" required />
                <TextInput type="text" id="title" v-model="form.title" placeholder="e.g. Manager" autofocus />
                <InputError class="mt-1.5" :message="form.errors.title" />
            </form>
        </template>

        <template v-slot:footer>
            <PrimaryButton type="submit" form="update" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Simpan Perubahan
            </PrimaryButton>
        </template>
    </Modal>
</template>
