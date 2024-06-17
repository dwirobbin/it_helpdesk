<script setup>
import { ref, reactive, watch } from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PaginationLinks from "@/Components/PaginationLinks.vue";
import ToggleInput from '@/Components/ToggleInput.vue';
import Delete from "./Delete.vue";
import { router, usePage } from "@inertiajs/vue3";
import { debounce, pickBy } from "lodash";
import { usePermission } from "@/Composables/permissions";

const { hasAnyRole } = usePermission();

const user = usePage().props.auth.user.data;

const props = defineProps({
    employees: {
        type: Object,
        default: () => { },
    },
    filters: {
        type: Object,
        default: () => { },
    },
});

// Begin of filters
const queries = reactive({
    search: props.filters.search,
    per_page: props.filters.per_page ?? 5,
    column: props.filters.column,
    direction: props.filters.direction,
});

function resetFilters() {
    Object.assign(queries, {
        search: null,
        per_page: 5,
        column: null,
        direction: null,
    })
};

const handleSearch = debounce(() => router.visit(route('employees.index', pickBy(queries)), {
    method: 'get',
    replace: true,
    preserveState: true,
}), 300);

const handlePerPage = () => router.visit(route('employees.index', pickBy(queries)), {
    method: 'get',
    replace: true,
    preserveState: true,
});

const handleSort = (column) => router.visit(route('employees.index', pickBy(queries)), {
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

// Begin of modal confirmation
const isShowConfirmDelete = ref(false);
const dataDelete = reactive({
    name: null,
    slug: null,
});

const toggleConfirmDelete = (name = null, slug = null) => {
    if (name !== null && slug !== null) {
        dataDelete.name = name;
        dataDelete.slug = slug;
    }

    isShowConfirmDelete.value = !isShowConfirmDelete.value;
};
// End of modal confirmation

// Begin status account
const changeAccountStatus = (slugByUser, isActiveAccount) => {
    router.patch(
        route('employees.change-account-status', { employee: slugByUser }),
        { is_active_account: isActiveAccount },
        {
            replace: true,
            preserveScroll: true,
        }
    )
};
// End status account
</script>

<template>

    <Head title="Employees" />

    <AuthenticatedLayout>
        <div class="p-4 bg-white block sm:flex items-center justify-between shadow-sm border-b border-gray-200 lg:mt-1">
            <div class="w-screen flex flex-col justify-left sm:flex-row sm:justify-between gap-y-3">
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 order-2 sm:order-1">Karyawan</h1>
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
                                <Link :href="route('positions.index')"
                                    class="text-gray-700 hover:text-gray-900 ml-1 md:ml-2 text-sm font-medium">
                                Karyawan
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
                        <div v-if="hasAnyRole(['Super Admin'])"
                            class="flex justify-between items-center ml-auto space-x-2 sm:space-x-3 divide-x divide-gray-100">
                            <Link :href="route('employees.create')"
                                class="w-1/2 text-white bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 font-medium inline-flex items-center justify-center rounded-lg text-sm px-2 sm:px-3 py-2 text-center sm:w-auto">
                            <svg class="-ml-1 mr-1 h-6 w-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Tambah
                            </Link>
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

                <div class="flex flex-col mt-5">
                    <div class="overflow-x-auto rounded-lg">
                        <div class="align-middle inline-block min-w-full">
                            <div class="shadow overflow-hidden">
                                <table class="table-fixed min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th scope="col" class="p-4 w-6 text-left text-xs font-medium text-gray-500 uppercase">
                                                #
                                            </th>
                                            <th @click="handleSort('users.name')" scope="col"
                                                class="p-4 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer whitespace-nowrap">
                                                <div class="flex items-center">
                                                    Nama
                                                    <span>
                                                        <svg v-if="queries.column === 'users.name' && queries.direction === 'asc'"
                                                            class="w-[15px] h-[15px] ms-1.5 text-gray-500" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="3" d="M12 19V5m0 14-4-4m4 4 4-4" />
                                                        </svg>
                                                        <svg v-else-if="queries.column === 'users.name' && queries.direction === 'desc'"
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
                                            <th @click="handleSort('users.email')" scope="col"
                                                class="p-4 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer whitespace-nowrap">
                                                <div class="flex items-center">
                                                    Email
                                                    <span>
                                                        <svg v-if="queries.column === 'users.email' && queries.direction === 'asc'"
                                                            class="w-[15px] h-[15px] ms-1.5 text-gray-500" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="3" d="M12 19V5m0 14-4-4m4 4 4-4" />
                                                        </svg>
                                                        <svg v-else-if="queries.column === 'users.email' && queries.direction === 'desc'"
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
                                            <th @click="handleSort('departments.section')" scope="col"
                                                class="p-4 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer whitespace-nowrap">
                                                <div class="flex items-center">
                                                    Divisi
                                                    <span>
                                                        <svg v-if="queries.column === 'departments.section' && queries.direction === 'asc'"
                                                            class="w-[15px] h-[15px] ms-1.5 text-gray-500" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="3" d="M12 19V5m0 14-4-4m4 4 4-4" />
                                                        </svg>
                                                        <svg v-else-if="queries.column === 'departments.section' && queries.direction === 'desc'"
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
                                            <th @click="handleSort('positions.title')" scope="col"
                                                class="p-4 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer whitespace-nowrap">
                                                <div class="flex items-center">
                                                    Jabatan
                                                    <span>
                                                        <svg v-if="queries.column === 'positions.title' && queries.direction === 'asc'"
                                                            class="w-[15px] h-[15px] ms-1.5 text-gray-500" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="3" d="M12 19V5m0 14-4-4m4 4 4-4" />
                                                        </svg>
                                                        <svg v-else-if="queries.column === 'positions.title' && queries.direction === 'desc'"
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
                                            <th @click="handleSort('roles.name')" scope="col"
                                                class="p-4 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer whitespace-nowrap">
                                                <div class="flex items-center">
                                                    Level User
                                                    <span>
                                                        <svg v-if="queries.column === 'roles.name' && queries.direction === 'asc'"
                                                            class="w-[15px] h-[15px] ms-1.5 text-gray-500" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="3" d="M12 19V5m0 14-4-4m4 4 4-4" />
                                                        </svg>
                                                        <svg v-else-if="queries.column === 'roles.name' && queries.direction === 'desc'"
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
                                            <th v-if="hasAnyRole(['Super Admin'])" @click="handleSort('roles.name')" scope="col"
                                                class="p-4 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer whitespace-nowrap">
                                                <div class="flex items-center">
                                                    Akun Aktif?
                                                    <span>
                                                        <svg v-if="queries.column === 'roles.name' && queries.direction === 'asc'"
                                                            class="w-[15px] h-[15px] ms-1.5 text-gray-500" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="3" d="M12 19V5m0 14-4-4m4 4 4-4" />
                                                        </svg>
                                                        <svg v-else-if="queries.column === 'roles.name' && queries.direction === 'desc'"
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
                                            <th scope="col" class="w-1/7 p-4 text-left text-xs font-medium text-gray-500 uppercase">
                                                Aksi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <template v-if="employees.data.length">
                                            <tr v-for="(employee, index) in employees?.data" :key="index" class="hover:bg-gray-100">
                                                <td class="p-4 w-6 whitespace-nowrap text-base font-medium text-gray-900">
                                                    {{ employees.meta.from + index }}
                                                </td>
                                                <td class="p-4 whitespace-nowrap text-base font-medium text-gray-900">
                                                    {{ employee.user.name }}
                                                </td>
                                                <td class="p-4 whitespace-nowrap text-base font-medium text-gray-900">
                                                    {{ employee.user.email }}
                                                </td>
                                                <td class="p-4 whitespace-nowrap text-base font-medium text-gray-900">
                                                    {{ employee.department?.section }}
                                                </td>
                                                <td class="p-4 whitespace-nowrap text-base font-medium text-gray-900">
                                                    {{ employee.position?.title }}
                                                </td>
                                                <td class="p-4 whitespace-nowrap text-base font-medium text-gray-900">
                                                    {{ employee.user.role.name }}
                                                </td>
                                                <td v-if="hasAnyRole(['Super Admin'])"
                                                    class="pt-1.5 px-4 whitespace-nowrap text-base font-medium text-gray-900">
                                                    <ToggleInput id="is-active-account" v-model:checked="employee.user.is_active"
                                                        @update:checked="changeAccountStatus(employee.user.slug, employee.user.is_active)" />
                                                </td>
                                                <td v-if="hasAnyRole(['Super Admin']) || employee.user.slug === user.slug"
                                                    class="p-4 w-1/7 whitespace-nowrap space-x-2">
                                                    <Link :href="route('employees.edit', { employee: employee.user.slug })" method="get"
                                                        preserve-state
                                                        class="inline-flex items-center p-2 text-sm font-medium text-center text-white rounded-lg bg-amber-500 hover:bg-amber-600 focus:ring-4 focus:ring-amber-300">
                                                    <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z">
                                                        </path>
                                                        <path fill-rule="evenodd"
                                                            d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                    Edit
                                                    </Link>
                                                    <button v-if="hasAnyRole(['Super Admin'])" type="button"
                                                        @click="toggleConfirmDelete(employee.user.name, employee.user.slug)"
                                                        class="inline-flex items-center p-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900">
                                                        <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20"
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
                                                <td colspan="8" class="p-4 text-center text-gray-900">
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

                <PaginationLinks v-if="employees.data.length" :pagination="employees.meta" />
            </div>
        </div>

        <Delete v-if="hasAnyRole(['Super Admin']) && isShowConfirmDelete" :employee="dataDelete" @close-confirm-modal="toggleConfirmDelete" />
    </AuthenticatedLayout>
</template>
