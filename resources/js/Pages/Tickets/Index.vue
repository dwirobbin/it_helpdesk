<script setup>
import { ref, reactive, watch, toRef, onMounted } from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PaginationLinks from "@/Components/PaginationLinks.vue";
import Create from "./Create.vue";
import Chat from "./Partials/Chat.vue";
import Edit from "./Edit.vue";
import Delete from "./Delete.vue";
import { router, usePage } from "@inertiajs/vue3";
import { debounce, pickBy } from "lodash";
import ConfirmComplaint from "./Partials/ConfirmComplaint.vue";
import CreateRespond from "./Responds/Create.vue";
import EditRespond from "./Responds/Edit.vue";
import SolvedComplaint from "./Partials/SolvedComplaint.vue";
import { usePermission } from "@/Composables/permissions";

const { hasAnyRole } = usePermission();

const user = usePage().props.auth.user.data;

const props = defineProps({
    tickets: {
        type: Object,
        default: () => { },
    },
    ticket: {
        type: Object,
        default: () => { },
    },
    respond: {
        type: Object,
        default: () => { },
    },
    is_respond: {
        type: Boolean,
        default: false,
    },
    filters: {
        type: Object,
        default: () => { },
    },
    is_open_chat: {
        type: Boolean,
        default: false,
    },
    ticket_chats: {
        type: Object,
        default: () => { },
    },
});

// Begin of filters
const queries = reactive({
    search: props.filters?.search,
    per_page: props.filters?.per_page ?? 5,
    column: props.filters?.column,
    direction: props.filters?.direction,
});

function resetFilters() {
    Object.assign(queries, {
        search: null,
        per_page: 5,
        column: null,
        direction: null,
    })
};

const handleSearch = debounce(() => router.visit(route('tickets.index', pickBy(queries)), {
    method: 'get',
    replace: true,
    preserveState: true,
}), 300);

const handlePerPage = () => router.visit(route('tickets.index', pickBy(queries)), {
    method: 'get',
    replace: true,
    preserveState: true,
});

const handleSort = (column) => router.visit(route('tickets.index', pickBy(queries)), {
    method: 'get',
    replace: true,
    preserveState: true,
    onSuccess: () => {
        queries.column = column;
        queries.direction = queries.direction === 'asc' ? 'desc' : 'asc';
    }
});

watch(
    () => [queries.search, queries.per_page, queries.column, queries.direction],
    ([search, perPage, column, direction]) => {
        if (search) return handleSearch();
        if (perPage) return handlePerPage();
        if (column && direction) return handleSort(column);
    }
);
// End of filters

// Begin of modal create
const showCreateModal = ref(false);

const toggleShowCreatModal = () => showCreateModal.value = !showCreateModal.value;
// End of modal create

const showModalChat = toRef(props.is_open_chat);

const toggleShowChatModal = () => {
    router.visit(route('tickets.index'), {
        method: 'get',
        replace: true,
        preserveState: true,
        onSuccess: () => {
            showModalChat.value = false;
        },
    });
};

// Begin of modal confirmation
const isShowConfirmModal = ref(false);
const dataConfirm = reactive({
    component_id: null,
    ticket_number: null,
    slug: null,
});

const toggleConfirmModal = (component_id = null, ticketNumber = null, slug = null) => {
    if (component_id !== null, ticketNumber !== null && slug !== null) {
        dataConfirm.component_id = component_id;
        dataConfirm.ticket_number = ticketNumber;
        dataConfirm.slug = slug;
    }

    isShowConfirmModal.value = !isShowConfirmModal.value;
};
// End of modal confirmation

// Begin set status color
const statusColor = (status) => {
    return {
        'Dalam Antrian': 'bg-yellow-100 text-yellow-800 border-yellow-300',
        'Menunggu Diproses': 'bg-blue-100 text-blue-800 border-blue-400',
        'Sedang Diproses': 'bg-purple-100 text-purple-800 border-purple-400',
        'Terselesaikan': 'bg-green-100 text-green-800 border-green-400',
    }[status];
};
// End set status color
</script>

<template>

    <Head title="Tiket" />

    <AuthenticatedLayout>
        <div class="p-4 bg-white block sm:flex items-center justify-between shadow-sm border-b border-gray-200 lg:mt-1">
            <div class="w-full flex flex-col justify-left sm:flex-row sm:justify-between gap-y-3">
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 order-2 sm:order-1">Tiket</h1>
                <nav class="flex order-1 sm:order-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2">
                        <li class="inline-flex items-center">
                            <Link :href="route('dashboard')" class="text-gray-700 hover:text-gray-900 inline-flex items-center">
                            <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                                </path>
                            </svg>
                            Home
                            </Link>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <Link :href="route('tickets.index')" class="text-gray-700 hover:text-gray-900 ml-1 md:ml-2 text-sm font-medium">
                                Tiket
                                </Link>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-400 ml-1 md:ml-2 text-sm font-medium" aria-current="page">List</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="p-4">
            <div class="p-4 bg-white block border border-gray-200 rounded-lg shadow-sm">
                <!-- Card Header -->
                <div class="mb-1 w-full">
                    <div class="sm:flex">
                        <div class="items-center mb-3 flex flex-col sm:flex-row justify-center sm:divide-x sm:divide-gray-100 sm:mb-0">
                            <div class="lg:pr-3">
                                <label for="users-search" class="sr-only">Search</label>
                                <div class="mt-1 relative lg:w-64 xl:w-96">
                                    <input type="text" v-model="queries.search"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                        placeholder="Ketik kata yang ingin dicari...">
                                </div>
                            </div>
                            <div class="flex pl-0 mt-3 space-x-1 sm:pl-2 sm:mt-0">
                                <button type="button" @click="resetFilters"
                                    class="text-gray-500 hover:text-gray-900 cursor-pointer p-1 hover:bg-gray-100 rounded inline-flex justify-center">
                                    <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.651 7.65a7.131 7.131 0 0 0-12.68 3.15M18.001 4v4h-4m-7.652 8.35a7.13 7.13 0 0 0 12.68-3.15M6 20v-4h4" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="flex justify-between items-center ml-auto space-x-2 sm:space-x-3 divide-x divide-gray-100">
                            <button v-if="hasAnyRole(['Staff'])" type="button" @click="toggleShowCreatModal"
                                class="w-1/2 text-white bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 font-medium inline-flex items-center justify-center rounded-lg text-sm px-2 sm:px-3 py-2 text-center sm:w-auto">
                                <svg class="-ml-1 mr-1 h-6 w-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Tambah
                            </button>
                            <div class="ml-0 flex items-center">
                                <label class="px-2 text-gray-500 whitespace-nowrap">Per Page</label>
                                <select id="countries" v-model="queries.per_page"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="25">25</option>
                                    <option value="35">35</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="flex flex-col mt-5">
                    <div class="overflow-x-auto rounded-lg">
                        <div class="align-middle inline-block min-w-full">
                            <div class="shadow overflow-hidden">
                                <table class="table-fixed min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th scope="col" class="p-4 w-4 text-left text-xs font-medium text-gray-500 uppercase">
                                                #
                                            </th>
                                            <th @click="handleSort('ticket_number')" scope="col"
                                                class="p-4 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer">
                                                <div class="flex items-center">
                                                    No. Tiket
                                                    <span>
                                                        <svg v-if="queries.column === 'ticket_number' && queries.direction === 'asc'"
                                                            class="w-[15px] h-[15px] ms-1.5 text-gray-500" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="3" d="M12 19V5m0 14-4-4m4 4 4-4" />
                                                        </svg>
                                                        <svg v-else-if="queries.column === 'ticket_number' && queries.direction === 'desc'"
                                                            class="w-[15px] h-[15px] ms-1.5 text-gray-500" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="3" d="M12 6v13m0-13 4 4m-4-4-4 4" />
                                                        </svg>
                                                        <svg v-else class="w-[15px] h-[15px] ms-1.5 text-gray-500" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="3" d="M8 20V7m0 13-4-4m4 4 4-4m4-12v13m0-13 4 4m-4-4-4 4" />
                                                        </svg>
                                                    </span>
                                                </div>
                                            </th>
                                            <th @click="handleSort('title')" scope="col"
                                                class="p-4 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer">
                                                <div class="flex items-center">
                                                    Keluhan
                                                    <span>
                                                        <svg v-if="queries.column === 'title' && queries.direction === 'asc'"
                                                            class="w-[15px] h-[15px] ms-1.5 text-gray-500" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="3" d="M12 19V5m0 14-4-4m4 4 4-4" />
                                                        </svg>
                                                        <svg v-else-if="queries.column === 'title' && queries.direction === 'desc'"
                                                            class="w-[15px] h-[15px] ms-1.5 text-gray-500" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="3" d="M12 6v13m0-13 4 4m-4-4-4 4" />
                                                        </svg>
                                                        <svg v-else class="w-[15px] h-[15px] ms-1.5 text-gray-500" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="3" d="M8 20V7m0 13-4-4m4 4 4-4m4-12v13m0-13 4 4m-4-4-4 4" />
                                                        </svg>
                                                    </span>
                                                </div>
                                            </th>
                                            <th @click="handleSort('created_at')" scope="col"
                                                class="p-4 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer">
                                                <div class="flex items-center">
                                                    Tgl. Pengaduan
                                                    <span>
                                                        <svg v-if="queries.column === 'created_at' && queries.direction === 'asc'"
                                                            class="w-[15px] h-[15px] ms-1.5 text-gray-500" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="3" d="M12 19V5m0 14-4-4m4 4 4-4" />
                                                        </svg>
                                                        <svg v-else-if="queries.column === 'created_at' && queries.direction === 'desc'"
                                                            class="w-[15px] h-[15px] ms-1.5 text-gray-500" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="3" d="M12 6v13m0-13 4 4m-4-4-4 4" />
                                                        </svg>
                                                        <svg v-else class="w-[15px] h-[15px] ms-1.5 text-gray-500" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="3" d="M8 20V7m0 13-4-4m4 4 4-4m4-12v13m0-13 4 4m-4-4-4 4" />
                                                        </svg>
                                                    </span>
                                                </div>
                                            </th>
                                            <th @click="handleSort('status')" scope="col"
                                                class="p-4 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer">
                                                <div class="flex items-center">
                                                    Status
                                                    <span>
                                                        <svg v-if="queries.column === 'status' && queries.direction === 'asc'"
                                                            class="w-[15px] h-[15px] ms-1.5 text-gray-500" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="3" d="M12 19V5m0 14-4-4m4 4 4-4" />
                                                        </svg>
                                                        <svg v-else-if="queries.column === 'status' && queries.direction === 'desc'"
                                                            class="w-[15px] h-[15px] ms-1.5 text-gray-500" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="3" d="M12 6v13m0-13 4 4m-4-4-4 4" />
                                                        </svg>
                                                        <svg v-else class="w-[15px] h-[15px] ms-1.5 text-gray-500" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="3" d="M8 20V7m0 13-4-4m4 4 4-4m4-12v13m0-13 4 4m-4-4-4 4" />
                                                        </svg>
                                                    </span>
                                                </div>
                                            </th>
                                            <th scope=" col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase">
                                                Tanggapan
                                            </th>
                                            <th scope="col" class="w-1/12 p-4 text-left text-xs font-medium text-gray-500 uppercase">
                                                Aksi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <template v-if="tickets.data.length">
                                            <tr v-for="(ticket, index) in tickets?.data" :key="index" class="hover:bg-gray-100">
                                                <td class="p-4 w-4 whitespace-nowrap text-base font-medium text-gray-900">
                                                    {{ tickets.meta.from + index }}
                                                </td>
                                                <td class="p-4 whitespace-nowrap text-base font-medium text-gray-900">
                                                    {{ ticket.ticket_number }}
                                                </td>
                                                <td class="p-4 whitespace-nowrap text-base font-medium text-gray-900">
                                                    {{ ticket.title }}
                                                </td>
                                                <td class="p-4 whitespace-nowrap text-base font-medium text-gray-900">
                                                    {{ ticket.created_at.locale_day_date }}
                                                </td>
                                                <td class="p-4 whitespace-nowrap text-base font-medium">
                                                    <span :class="statusColor(ticket.status)"
                                                        class="text-sm font-medium me-2 px-2.5 py-0.5 rounded border">
                                                        {{ ticket.status }}
                                                    </span>
                                                </td>
                                                <td class="p-4 whitespace-nowrap space-x-2">
                                                    <Link
                                                        :href="route('ticket-chats.create', { _query: { ticket_number: ticket.ticket_number } })"
                                                        method="get" :only="['is_open_chat', 'ticket_chats']"
                                                        class="relative inline-flex items-center p-2 text-sm font-medium text-center text-white rounded-lg bg-indigo-500 hover:bg-indigo-600 focus:ring-4 focus:ring-indigo-300">
                                                    <svg class="mr-1.5 w-[20px] h-[20px] text-white" aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path fill-rule="evenodd"
                                                            d="M4 3a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h1v2a1 1 0 0 0 1.707.707L9.414 13H15a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H4Z"
                                                            clip-rule="evenodd" />
                                                        <path fill-rule="evenodd"
                                                            d="M8.023 17.215c.033-.03.066-.062.098-.094L10.243 15H15a3 3 0 0 0 3-3V8h2a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1h-1v2a1 1 0 0 1-1.707.707L14.586 18H9a1 1 0 0 1-.977-.785Z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <div v-if="hasAnyRole(['Super Admin', 'It Support'])"
                                                        class="absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 border border-white rounded-full -top-3 -end-2 dark:border-gray-900">
                                                        {{ ticket.ticket_chats.filter(chat => chat.is_readed === false && chat.seen_for_admin === true).length }}
                                                    </div>
                                                    <div v-else
                                                        class="absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 border border-white rounded-full -top-3 -end-2 dark:border-gray-900">
                                                        {{ ticket.ticket_chats.filter(chat => chat.is_readed === false && chat.seen_for_staff === true).length }}
                                                    </div>
                                                    Chat
                                                    </Link>

                                                    <template v-if="hasAnyRole(['Super Admin', 'It Support'])">
                                                        <button type="button" v-if="ticket.status === 'Dalam Antrian'"
                                                            @click="toggleConfirmModal('confirm-complaint', ticket.ticket_number, ticket.slug)"
                                                            class="inline-flex items-center p-2 text-sm font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300">
                                                            <svg class="mr-1.5 w-[20px] h-[20px] text-white" aria-hidden="true"
                                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                                viewBox="0 0 24 24">
                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                            </svg>
                                                            Konfirmasi
                                                        </button>
                                                        <Link v-if="ticket.status === 'Menunggu Diproses'"
                                                            :href="route('responds.create', { ticket: ticket.slug })" method="get" preserve-state
                                                            :only="['ticket', 'is_respond']"
                                                            class="inline-flex items-center p-2 text-sm font-medium text-center text-white rounded-lg bg-purple-500 hover:bg-purple-600 focus:ring-4 focus:ring-purple-300">
                                                        <svg class="mr-1.5 w-[20px] h-[20px] text-white" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M7.556 8.5h8m-8 3.5H12m7.111-7H4.89a.896.896 0 0 0-.629.256.868.868 0 0 0-.26.619v9.25c0 .232.094.455.26.619A.896.896 0 0 0 4.89 16H9l3 4 3-4h4.111a.896.896 0 0 0 .629-.256.868.868 0 0 0 .26-.619v-9.25a.868.868 0 0 0-.26-.619.896.896 0 0 0-.63-.256Z" />
                                                        </svg>
                                                        Tanggapi
                                                        </Link>
                                                        <div v-if="ticket.status === 'Sedang Diproses'" class="flex space-x-2">
                                                            <Link :href="route('responds.edit', { respond: ticket.slug })" method="get"
                                                                preserve-state :only="['ticket', 'respond']"
                                                                class="inline-flex items-center p-2 text-sm font-medium text-center text-white rounded-lg bg-amber-500 hover:bg-amber-600 focus:ring-4 focus:ring-amber-300">
                                                            <svg class="mr-1.5 h-5 w-5" fill="currentColor" viewBox="0 0 20 20"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z">
                                                                </path>
                                                                <path fill-rule="evenodd"
                                                                    d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                            Edit
                                                            </Link>
                                                            <button type="button"
                                                                @click="toggleConfirmModal('confirm-solved', ticket.ticket_number, ticket.slug)"
                                                                class="inline-flex items-center p-2 text-sm font-medium text-center text-white bg-green-500 rounded-lg hover:bg-green-600 focus:ring-4 focus:ring-green-300">
                                                                <svg class="mr-1.5 w-[20px] h-[20px] text-white" aria-hidden="true"
                                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="m15 9-6 6m0-6 6 6m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                                </svg>
                                                                Selesaikan
                                                            </button>
                                                        </div>
                                                        <button type="button" v-if="ticket.status === 'Terselesaikan'"
                                                            class="inline-flex items-center text-center text-white bg-gray-800 border border-gray-300 font-medium rounded-lg text-sm p-2 cursor-default">
                                                            <svg class="mr-1.5 w-[20px] h-[20px] text-white" aria-hidden="true"
                                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                                viewBox="0 0 24 24">
                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="m8.032 12 1.984 1.984 4.96-4.96m4.55 5.272.893-.893a1.984 1.984 0 0 0 0-2.806l-.893-.893a1.984 1.984 0 0 1-.581-1.403V7.04a1.984 1.984 0 0 0-1.984-1.984h-1.262a1.983 1.983 0 0 1-1.403-.581l-.893-.893a1.984 1.984 0 0 0-2.806 0l-.893.893a1.984 1.984 0 0 1-1.403.581H7.04A1.984 1.984 0 0 0 5.055 7.04v1.262c0 .527-.209 1.031-.581 1.403l-.893.893a1.984 1.984 0 0 0 0 2.806l.893.893c.372.372.581.876.581 1.403v1.262a1.984 1.984 0 0 0 1.984 1.984h1.262c.527 0 1.031.209 1.403.581l.893.893a1.984 1.984 0 0 0 2.806 0l.893-.893a1.985 1.985 0 0 1 1.403-.581h1.262a1.984 1.984 0 0 0 1.984-1.984V15.7c0-.527.209-1.031.581-1.403Z" />
                                                            </svg>
                                                            Selesai
                                                        </button>
                                                    </template>
                                                </td>
                                                <td class="p-4 w-1/12 whitespace-nowrap space-x-2">
                                                    <Link v-if="hasAnyRole(['Super Admin', 'It Support']) || ticket.user.slug === user.slug"
                                                        :href="route('tickets.show', { ticket: ticket.slug })" method="get" preserve-state
                                                        :only="['ticket']"
                                                        class="inline-flex items-center p-2 text-sm font-medium text-center rounded-lg text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100">
                                                    <svg class="mr-1.5 w-[20px] h-[20px] text-gray-800 dark:text-white" aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                        viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-width="2"
                                                            d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z" />
                                                        <path stroke="currentColor" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                    </svg>
                                                    Detail
                                                    </Link>
                                                    <Link v-if="ticket.status === 'Dalam Antrian' && ticket.user.slug === user.slug"
                                                        :href="route('tickets.edit', { ticket: ticket.slug })" method="get" preserve-state
                                                        :only="['ticket']"
                                                        class="inline-flex items-center p-2 text-sm font-medium text-center text-white rounded-lg bg-amber-500 hover:bg-amber-600 focus:ring-4 focus:ring-amber-300">
                                                    <svg class="mr-1.5 h-5 w-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z">
                                                        </path>
                                                        <path fill-rule="evenodd"
                                                            d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                            clip-rule="evenodd">
                                                        </path>
                                                    </svg>
                                                    Edit
                                                    </Link>
                                                    <button v-if="hasAnyRole(['Super Admin', 'It Support'])" type="button"
                                                        @click="toggleConfirmModal('confirm-delete', ticket.ticket_number, ticket.slug)"
                                                        class="inline-flex items-center p-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-300 ">
                                                        <svg class="mr-1.5 h-5 w-5" fill="currentColor" viewBox="0 0 20 20"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd"
                                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>

                                        <template v-else>
                                            <tr>
                                                <td colspan="7" class="p-4 text-center text-gray-900">
                                                    Data belum tersedia pada tabel ini.
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card footer -->
                <PaginationLinks v-if="tickets.data.length" :pagination="tickets.meta" />
            </div>
        </div>

        <!-- Chat -->
        <Chat v-if="showModalChat" :show="showModalChat" :ticket_chats="ticket_chats" @close-chat-modal="toggleShowChatModal" />

        <!-- Create -->
        <Create v-if="showCreateModal" :show="showCreateModal" @close-create-modal="toggleShowCreatModal" />

        <!-- Edit -->
        <Edit v-if="ticket && !respond && !showModalChat" :ticket="ticket" />

        <!-- Confirm Delete Modal -->
        <Delete v-if="isShowConfirmModal && dataConfirm.component_id === 'confirm-delete'" :complaint="dataConfirm"
            @close-confirm-modal="toggleConfirmModal" />

        <!-- Confirm Complaint Modal -->
        <ConfirmComplaint v-if="isShowConfirmModal && dataConfirm.component_id === 'confirm-complaint'" :complaint="dataConfirm"
            @close-confirm-modal="toggleConfirmModal" />

        <!-- Confirm Solved Modal -->
        <SolvedComplaint v-if="isShowConfirmModal && dataConfirm.component_id === 'confirm-solved'" :complaint="dataConfirm"
            @close-confirm-modal="toggleConfirmModal" />

        <!-- Respond -->
        <CreateRespond v-if="ticket && is_respond" :ticket="ticket" />

        <!-- Edit -->
        <EditRespond v-if="props.ticket && props.respond" :ticket="props.ticket" :respond="props.respond" />
    </AuthenticatedLayout>
</template>
