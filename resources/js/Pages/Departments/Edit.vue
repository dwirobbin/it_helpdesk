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
    department: {
        type: Object,
        required: true,
    },
});

const isShow = computed(() => !!props.department);

const form = useForm({
    current_page: usePage().props.departments.meta.current_page,
    last_page: usePage().props.departments.meta.last_page,
    section: props.department?.data.section,
});

const closeModal = () => router.visit(route('departments.index'), {
    method: 'get',
    replace: true,
    preserveState: true,
    onSuccess: () => {
        form.reset();
        form.clearErrors();
    },
});

const onUpdate = () => form.put(route('departments.update', { department: props.department.data.slug }), {
    replace: true,
    onFinish: () => usePage().props.flash = null,
});
</script>

<template>

    <Head title="Edit Department" />

    <Modal :show="isShow" @close="closeModal" maxWidth="lg">
        <template #header>
            <h3 class="text-xl font-semibold">Edit Divisi</h3>
        </template>

        <template #body>
            <form @submit.prevent="onUpdate" id="update" class="p-6">
                <InputLabel for="section" value="Divisi" required />
                <TextInput type="text" id="section" v-model="form.section" placeholder="e.g. HRD" autofocus />
                <InputError class="mt-1.5" :message="form.errors.section" />
            </form>
        </template>

        <template #footer>
            <PrimaryButton type="submit" form="update" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Simpan Perubahan
            </PrimaryButton>
        </template>
    </Modal>
</template>
