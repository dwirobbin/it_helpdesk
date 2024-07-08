<script setup>
import { computed, ref } from "vue";
import { useForm, router, usePage } from '@inertiajs/vue3';
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import TextareaInput from "@/Components/TextareaInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { route } from 'ziggy';

const props = defineProps({
    ticket: {
        type: Object,
        required: true,
    },
});

const src = ref(null);

const isShow = computed(() => !!props.ticket);

const closeModal = () => router.visit(route('tickets.index'), {
    method: 'get',
    replace: true,
    preserveState: true,
    onSuccess: () => {
        form.reset();
        form.clearErrors();
    },
});

const generateUrl = (filePath) => window.location.origin + filePath;

const form = useForm({
    page: usePage().props.tickets.meta.current_page,
    ticket_id: props.ticket.data.id,
    text: null,
    image: null,
});

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    form.image = file;

    previewImage(file);
};

const previewImage = (file) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = (e) => (src.value = e.target.result);
}

const onSubmit = () => form.post(route('responds.store'), {
    replace: true,
    onSuccess: () => form.reset(),
    onFinish: () => usePage().props.flash = null,
});
</script>

<template>

    <Head title="Tanggapan" />

    <Modal :show="isShow" @close="closeModal" maxWidth="5xl" height="116" overflow="y-auto">
        <template #header>
            <h3 class="text-xl font-semibold">Berikan Tanggapan</h3>
        </template>

        <template #body>
            <div class="p-6 space-y-6 h-[29rem] overflow-y-auto">
                <form @submit.prevent="onSubmit" id="create" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-6">
                        <h2 class="2xl font-medium border-b pb-5">Pengaduan</h2>
                        <div>
                            <InputLabel for="title" value="Keluhan" />
                            <TextInput type="text" id="title" v-model="ticket.data.title" placeholder="Keluhan..." disabled />
                        </div>
                        <div>
                            <InputLabel for="picture-complaint" value="Gambar Pengaduan" />
                            <img class="mb-4 rounded-lg w-28 h-28 sm:mb-0 object-cover"
                                :src="[ticket.data.image ?? generateUrl('/assets/images/default-img.png')]" alt="Foto Keluhan">
                        </div>
                        <div>
                            <InputLabel for="description" value="Deskripsi" />
                            <TextareaInput id="description" rows="9" v-model="ticket.data.description" placeholder="Deskripsi..." disabled />
                        </div>
                    </div>
                    <div class="space-y-6">
                        <h2 class="2xl font-medium border-b pb-5">Tanggapan</h2>
                        <div>
                            <InputLabel for="text" value="Teks" required />
                            <TextareaInput id="text" v-model="form.text" rows="9" placeholder="Teks..." autofocus />
                            <InputError class="mt-1.5" :message="form.errors.text" />
                        </div>
                        <div class="pt-1">
                            <InputLabel for="picture-respond" value="Gambar Tanggapan" />
                            <div class="flex flex-col items-center sm:flex-row sm:justify-between">
                                <div>
                                    <img class="mb-4 rounded-lg w-28 h-28 sm:mb-0 object-cover"
                                        :src="[src ?? generateUrl('/assets/images/default-img.png')]" alt="Gambar Tanggapan">
                                    <input id="picture-respond" type="file" accept="image/*" class="hidden" ref="input"
                                        @change="handleFileChange">
                                    <InputError class="mt-1.5" :message="form.errors.image" />
                                </div>

                                <PrimaryButton type="button" @click="(event) => $refs.input.click()" :disabled="form.processing"
                                    class="flex items-center justify-center text-center space-x-4 mb-1 sm:mb-0">
                                    <svg class="w-4 h-4 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z">
                                        </path>
                                        <path d="M9 13h2v5a1 1 0 11-2 0v-5z"></path>
                                    </svg>
                                    Upload Gambar
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </template>

        <template #footer>
            <PrimaryButton type="submit" form="create" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Submit
            </PrimaryButton>
        </template>
    </Modal>
</template>
