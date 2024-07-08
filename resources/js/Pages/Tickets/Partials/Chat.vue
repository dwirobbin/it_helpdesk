<script setup>
import { computed, nextTick, ref, watch } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import TextareaInput from "@/Components/TextareaInput.vue";
import InputError from "@/Components/InputError.vue";
import { usePermission } from "@/Composables/permissions";

const { hasAnyRole } = usePermission();

const props = defineProps({
    ticket_chats: {
        type: Object,
        default: () => { },
    },
    show: {
        type: Boolean,
        required: true,
    },
});

const emit = defineEmits(["closeChatModal"]);

const isShow = computed(() => !!props.show);

const closeModal = () => emit("closeChatModal");

const form = useForm({
    ticket_number: new URLSearchParams(window.location.search).get('ticket_number'),
    user: usePage().props.auth.user?.data.slug,
    text: null,
});

const modalRef = ref(null);

watch(
    () => props.ticket_chats,
    () => {
        nextTick(() => {
            modalRef.value.scrollTo({
                top: modalRef.value.scrollHeight,
                behavior: "smooth",
            });
        });
    },
    { deep: true, immediate: true }
);

const onSubmit = () => {
    form.post(route('ticket-chats.store'), {
        replace: true,
        onSuccess: () => {
            form.reset();
            form.clearErrors();
            usePage().props.flash = null;
        },
    });
};
</script>

<template>

    <Head title="Ruang Obrolan" />

    <Modal id="content" :show="isShow" @close="closeModal" maxWidth="3xl">
        <template v-slot:header>
            <h3 class="text-xl font-semibold">Ruang Obrolan</h3>
        </template>

        <template v-slot:body>
            <div class="p-6 space-y-6 h-[29rem] overflow-y-auto" ref="modalRef">
                <template v-if="ticket_chats?.data.length">
                    <template v-for="(ticket_chat, i) in ticket_chats.data" :key="i">
                        <div :class="{ 'justify-end': ticket_chat.user.slug === $page.props.auth.user.data.slug }"
                            class="flex items-start gap-2.5 modal-content">
                            <img v-if="ticket_chat.user.slug !== $page.props.auth.user.data.slug" class="w-8 h-8 rounded-full"
                                :src="ticket_chat.user.photo" :alt="`User-${i} image`">
                            <div class="flex flex-col gap-1 w-full max-w-[320px]">
                                <div :class="{ 'flex-row-reverse': ticket_chat.user.slug === $page.props.auth.user.data.slug }"
                                    class="flex items-center gap-2">
                                    <span class="text-sm font-semibold text-gray-900">{{ ticket_chat.user.name }}</span>
                                    <span class="text-sm font-normal text-gray-500">{{ ticket_chat.created_at.locale_hours_minutes }}</span>
                                </div>
                                <div :class="[ticket_chat.user.slug === $page.props.auth.user.data.slug ? 'rounded-b-xl rounded-s-xl' : 'rounded-e-xl rounded-es-xl']"
                                    class="flex flex-col leading-1.5 p-4 border-gray-200 bg-gray-100">
                                    <p class="text-sm font-normal text-gray-900">
                                        {{ ticket_chat.text }}
                                    </p>
                                </div>
                            </div>
                            <img v-if="ticket_chat.user.slug === $page.props.auth.user.data.slug" class="w-8 h-8 rounded-full"
                                :src="ticket_chat.user.photo" :alt="`User-${i} image`">
                        </div>
                    </template>
                </template>
                <template v-else>
                    <div class="flex flex-col items-center justify-center h-full">
                        <span class="bg-gray-100 text-gray-800 text-sm font-medium me-2 px-4 py-2 rounded dark:bg-gray-700 dark:text-gray-300">
                            Mulai Obrolan
                        </span>
                    </div>
                </template>
            </div>
        </template>

        <template v-slot:footer>
            <form @submit.prevent="onSubmit" class="p-0">
                <div class="flex items-center">
                    <TextareaInput id="chat" v-model="form.text" rows="1" class="me-4" placeholder="Pesan anda..." autofocus />
                    <button type="submit" class="inline-flex justify-center p-2 text-cyan-600 rounded-full cursor-pointer hover:bg-cyan-100">
                        <svg class="w-6 h-6 rotate-90 rtl:-rotate-90" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 18 20">
                            <path
                                d="m17.914 18.594-8-18a1 1 0 0 0-1.828 0l-8 18a1 1 0 0 0 1.157 1.376L8 18.281V9a1 1 0 0 1 2 0v9.281l6.758 1.689a1 1 0 0 0 1.156-1.376Z" />
                        </svg>
                    </button>
                </div>
                <InputError class="mt-1.5" :message="form.errors.text" />
            </form>
        </template>
    </Modal>
</template>
