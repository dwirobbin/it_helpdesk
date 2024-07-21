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
    room: {
        type: Object,
        required: true,
    },
});

const isShow = computed(() => !!props.room);

const form = useForm({
    current_page: usePage().props.rooms.meta.current_page,
    last_page: usePage().props.rooms.meta.last_page,
    name: props.room?.data.name,
});

const closeModal = () => router.visit(route('rooms.index', { page: usePage().props.rooms.meta.current_page }), {
    method: 'get',
    replace: true,
    preserveState: true,
    onSuccess: () => {
        form.reset();
        form.clearErrors();
    },
});

const onUpdate = () => form.put(route('rooms.update', { room: props.room.data.slug }), {
    replace: true,
    onFinish: () => usePage().props.flash = null,
});
</script>

<template>

    <Head title="Edit Ruangan" />

    <Modal :show="isShow" @close="closeModal" maxWidth="lg">
        <template v-slot:header>
            <h3 class="text-xl font-semibold">Edit Ruangan</h3>
        </template>

        <template v-slot:body>
            <form @submit.prevent="onUpdate" id="update" class="p-6">
                <InputLabel for="name" value="Nama" required />
                <TextInput type="text" id="name" v-model="form.name" placeholder="cth: R001" autofocus />
                <InputError class="mt-1.5" :message="form.errors.name" />
            </form>
        </template>

        <template v-slot:footer>
            <PrimaryButton type="submit" form="update" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Simpan Perubahan
            </PrimaryButton>
        </template>
    </Modal>
</template>
