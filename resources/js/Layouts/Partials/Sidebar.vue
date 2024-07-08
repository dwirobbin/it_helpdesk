<script setup>
import { onMounted, ref, watch, reactive } from 'vue';
import { Collapse } from 'flowbite';
import { usePermission } from "@/Composables/permissions";

const { hasAnyRole } = usePermission();

const dropdowns = reactive({});
const currentDropdownId = ref(null);

function toggleDropdownCollapse(id) {
    currentDropdownId.value = id;
    if (dropdowns[id]) {
        dropdowns[id].isShow = !dropdowns[id].isShow;
    }
}

watch([currentDropdownId, dropdowns], ([newId, newDropdown]) => {
    if (newId && newDropdown[newId]) {
        if (newDropdown[newId].isShow) {
            newDropdown[newId].instance.expand();
        } else {
            newDropdown[newId].instance.collapse();
        }
    }
});

onMounted(() => {
    const dropdownElements = document.querySelectorAll('ul[data-dropdown-id]');
    if (dropdownElements) {
        dropdownElements.forEach(el => {
            const id = el.getAttribute('data-dropdown-id');
            dropdowns[id] = {
                isShow: false,
                instance: new Collapse(el),
            };
        });
    }
});
</script>

<template>
    <aside id="sidebar"
        class="fixed top-0 left-0 h-full z-20 pt-16 w-64 flex lg:flex flex-shrink-0 flex-col transition-transform -translate-x-full bg-white border-r border-gray-200 lg:translate-x-0">
        <div class="relative flex-1 flex flex-col min-h-0 border-r border-gray-200 bg-white pt-0">
            <div class="flex-1 flex flex-col pt-2.5 pb-4 overflow-y-auto">
                <div class="flex-1 px-3 bg-white divide-y space-y-1">
                    <ul class="space-y-2 pb-1">
                        <li>
                            <h5 id="drawer-navigation-label" class="text-base font-semibold text-gray-500 uppercase">
                                Main
                            </h5>
                        </li>
                        <li>
                            <Link :href="route('dashboard')" method="get" preserve-scroll
                                :class="[route().current('dashboard') ? 'text-white bg-cyan-600 hover:text-gray-100 hover:bg-cyan-700 focus:ring-cyan-200' : 'text-gray-900 bg-white hover:bg-gray-100']"
                                class="text-base font-normal rounded-lg flex w-full items-center p-2 focus:ring-4 group">
                            <svg :class="[route().current('dashboard') ? 'text-white group-hover:text-gray-100' : 'text-gray-500 group-hover:text-gray-900']"
                                class="w-6 h-6 transition duration-75" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                                </path>
                            </svg>
                            <span class="ml-3">Dashboard</span>
                            </Link>
                        </li>
                    </ul>

                    <ul class="pt-4 pb-1 mt-4 space-y-2 font-medium border-t border-gray-200">
                        <li>
                            <h5 id="drawer-navigation-label" class="text-base font-semibold text-gray-500 uppercase">
                                Data Master
                            </h5>
                        </li>
                        <li>
                            <button type="button"
                                :class="[route().current('positions.*') || route().current('departments.*') || route().current('employees.*') ? 'text-white bg-cyan-600 hover:text-gray-100 hover:bg-cyan-700 focus:ring-cyan-200' : 'text-gray-900 bg-white hover:bg-gray-100']"
                                class="flex items-center w-full p-2 text-base font-normal transition duration-75 rounded-lg group"
                                @click="toggleDropdownCollapse('dropdown-master-employee')">
                                <svg :class="[route().current('positions.*') || route().current('departments.*') || route().current('employees.*') ? 'text-white group-hover:text-gray-100' : 'text-gray-500 group-hover:text-gray-900']"
                                    class="w-6 h-6 transition duration-75" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4H6Zm7.25-2.095c.478-.86.75-1.85.75-2.905a5.973 5.973 0 0 0-.75-2.906 4 4 0 1 1 0 5.811ZM15.466 20c.34-.588.535-1.271.535-2v-1a5.978 5.978 0 0 0-1.528-4H18a4 4 0 0 1 4 4v1a2 2 0 0 1-2 2h-4.535Z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Master Karyawan</span>
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="m1 1 4 4 4-4" />
                                </svg>
                            </button>
                            <ul id="dropdown-master-employee"
                                :class="{ 'hidden': !route().current('positions.*') && !route().current('departments.*') && !route().current('employees.*') }"
                                class="mt-1 space-y-1" data-dropdown-id="dropdown-master-employee">
                                <li v-if="hasAnyRole(['Super Admin'])">
                                    <Link :href="route('positions.index')" method="get" as="button" type="button"
                                        :class="[route().current('positions.index') ? 'bg-gray-200' : 'hover:bg-gray-100']"
                                        class="flex items-center w-full p-2 text-gray-900 font-normal transition duration-75 rounded-lg pl-11 group">
                                    Data Jabatan
                                    </Link>
                                </li>
                                <li v-if="hasAnyRole(['Super Admin'])">
                                    <Link :href="route('departments.index')" method="get" preserve-state
                                        :class="[route().current('departments.index') ? 'bg-gray-200' : 'hover:bg-gray-100']"
                                        class="flex items-center w-full p-2 text-gray-900 font-normal transition duration-75 rounded-lg pl-11 group">
                                    Data Department
                                    </Link>
                                </li>
                                <li>
                                    <Link :href="route('employees.index')" method="get" preserve-state
                                        :class="[route().current('employees.*') ? 'bg-gray-200' : 'hover:bg-gray-100']"
                                        class="flex items-center w-full p-2 text-gray-900 font-normal transition duration-75 rounded-lg pl-11 group">
                                    Data Karyawan
                                    </Link>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <Link :href="route('tickets.index')" method="get" preserve-scroll
                                :class="[route().current('tickets.*') ? 'text-white bg-cyan-600 hover:text-gray-100 hover:bg-cyan-700 focus:ring-cyan-200' : 'text-gray-900 bg-white hover:bg-gray-100']"
                                class="text-base font-normal rounded-lg flex w-full items-center p-2 focus:ring-4 group">
                            <svg :class="[route().current('tickets.*') ? 'text-white group-hover:text-gray-100' : 'text-gray-500 group-hover:text-gray-900']"
                                class="w-6 h-6 transition duration-75" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M4 5a2 2 0 0 0-2 2v2.5a1 1 0 0 0 1 1 1.5 1.5 0 1 1 0 3 1 1 0 0 0-1 1V17a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2.5a1 1 0 0 0-1-1 1.5 1.5 0 1 1 0-3 1 1 0 0 0 1-1V7a2 2 0 0 0-2-2H4Z" />
                            </svg>
                            <span class="ml-3">Data Tiket</span>
                            </Link>
                        </li>
                    </ul>

                    <ul v-if="hasAnyRole(['Super Admin', 'It Support'])" class="pt-4 pb-1 mt-4 space-y-2 font-medium border-t border-gray-200">
                        <li>
                            <h5 id="drawer-navigation-label" class="text-base font-semibold text-gray-500 uppercase">
                                Laporan
                            </h5>
                        </li>
                        <li>
                            <Link :href="route('reports.tickets.index')" method="get" preserve-scroll
                                :class="[route().current('reports.tickets.*') ? 'text-white bg-cyan-600 hover:text-gray-100 hover:bg-cyan-700 focus:ring-cyan-200' : 'text-gray-900 bg-white hover:bg-gray-100']"
                                class="text-base font-normal rounded-lg flex w-full items-center p-2 focus:ring-4 group">
                            <svg :class="[route().current('reports.tickets.*') ? 'text-white group-hover:text-gray-100' : 'text-gray-500 group-hover:text-gray-900']"
                                class="w-6 h-6 transition duration-75" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                    d="M9 7V2.221a2 2 0 0 0-.5.365L4.586 6.5a2 2 0 0 0-.365.5H9Zm2 0V2h7a2 2 0 0 1 2 2v9.293l-2-2a1 1 0 0 0-1.414 1.414l.293.293h-6.586a1 1 0 1 0 0 2h6.586l-.293.293A1 1 0 0 0 18 16.707l2-2V20a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9h5a2 2 0 0 0 2-2Z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="ml-3">Laporan Tiket</span>
                            </Link>
                        </li>
                    </ul>

                    <ul v-if="hasAnyRole(['Super Admin'])" class="pt-4 pb-1 mt-4 space-y-2 font-medium border-t border-gray-200">
                        <li>
                            <h5 id="drawer-navigation-label" class="text-base font-semibold text-gray-500 uppercase">
                                Application
                            </h5>
                        </li>
                        <li>
                            <Link :href="route('settings.edit')"
                                :class="[route().current('settings.*') ? 'text-white bg-cyan-600 hover:text-gray-100 hover:bg-cyan-700 focus:ring-cyan-200' : 'text-gray-900 bg-white hover:bg-gray-100']"
                                class="text-base font-normal rounded-lg flex w-full items-center p-2 focus:ring-4 group">
                            <svg :class="[route().current('settings.*') ? 'text-white group-hover:text-gray-100' : 'text-gray-500 group-hover:text-gray-900']"
                                class="w-6 h-6 transition duration-75" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                    d="M9.586 2.586A2 2 0 0 1 11 2h2a2 2 0 0 1 2 2v.089l.473.196.063-.063a2.002 2.002 0 0 1 2.828 0l1.414 1.414a2 2 0 0 1 0 2.827l-.063.064.196.473H20a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-.089l-.196.473.063.063a2.002 2.002 0 0 1 0 2.828l-1.414 1.414a2 2 0 0 1-2.828 0l-.063-.063-.473.196V20a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2v-.089l-.473-.196-.063.063a2.002 2.002 0 0 1-2.828 0l-1.414-1.414a2 2 0 0 1 0-2.827l.063-.064L4.089 15H4a2 2 0 0 1-2-2v-2a2 2 0 0 1 2-2h.09l.195-.473-.063-.063a2 2 0 0 1 0-2.828l1.414-1.414a2 2 0 0 1 2.827 0l.064.063L9 4.089V4a2 2 0 0 1 .586-1.414ZM8 12a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="ml-3">Pengaturan</span>
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="bottom-0 left-0 justify-center w-full p-2 space-x-4 bg-white flex" sidebar-bottom-menu>
                <Link :href="route('logout')" method="post" as="button" type="button" data-tooltip-target="tooltip-logout"
                    class="inline-flex justify-center w-full p-2 font-medium bg-gray-300 text-gray-800 rounded-lg cursor-pointer hover:text-gray-900 hover:bg-gray-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                    </path>
                </svg>
                Keluar
                </Link>
                <div id="tooltip-logout" role="tooltip"
                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                    Keluar
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            </div>
        </div>
    </aside>
</template>
