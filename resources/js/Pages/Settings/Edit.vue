<script setup>
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UpdateAgencyInformation from "./Partials/UpdateAgencyInformation.vue";
import UpdateAppInformation from "./Partials/UpdateAppInformation.vue";
import { usePermission } from "@/Composables/permissions";
import { route } from 'ziggy';

const { hasAnyRole } = usePermission();

const setting = usePage().props.setting.data;

</script>

<template>

    <Head title="Pengaturan Aplikasi & Instansi" />

    <AuthenticatedLayout>
        <div class="p-4 bg-white block sm:flex items-center justify-between shadow-sm border-b border-gray-200 lg:mt-1">
            <div class="w-full flex flex-col justify-left sm:flex-row sm:justify-between gap-y-3">
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 order-2 sm:order-1">Pengaturan Aplikasi & Instansi</h1>
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
                                <span class="text-gray-400 ml-1 md:ml-2 text-sm font-medium" aria-current="page">
                                    Edit Pengaturan Aplikasi & Instansi
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div v-if="hasAnyRole(['Super Admin'])" class="p-4">
            <div class="p-4 bg-white block border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="p-4 space-y-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Informasi Aplikasi</h2>
                    <UpdateAppInformation :setting="setting" />

                    <hr class="mt-9 mb-7 divide-y-4 divide-solid" />

                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Informasi Instansi</h2>
                    <UpdateAgencyInformation :setting="setting" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
