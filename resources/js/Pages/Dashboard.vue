<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePermission } from "@/Composables/permissions";

const { hasAnyRole } = usePermission();

defineProps({
    total: {
        type: Object,
        default: () => { },
    }
});
</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="p-4 bg-white block sm:flex items-center justify-between shadow-sm border-b border-gray-200 lg:mt-1">
            <div class="w-full flex flex-col justify-left sm:flex-row sm:justify-between gap-y-3">
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 order-2 sm:order-1">Dashboard</h1>
                <nav class="flex order-1 sm:order-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2">
                        <li class="inline-flex items-center">
                            <svg class="w-5 h-5 mr-2.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                                </path>
                            </svg>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="p-4">
            <div class="mb-4 w-full flex flex-wrap gap-4">
                <div class="grow bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8" :class="[hasAnyRole(['Super Admin']) ? 'w-72' : 'w-56']">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="leading-none space-x-2">
                                <span class="text-2xl sm:text-3xl font-bold text-gray-900">{{ total.data.tickets.waiting }}</span>
                                <span class="text-base text-gray-600 font-semibold">Tiket</span>
                            </div>
                            <h3 class="text-base font-normal text-gray-500">Dalam Antrian</h3>
                        </div>
                        <div class="ml-5 w-0 flex items-center justify-end flex-1 text-green-500 text-base font-bold">
                            <svg class="w-[26px] h-[26px] text-yellow-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17 20v-5h2v6.988H3V15h1.98v5H17Z" />
                                <path
                                    d="m6.84 14.522 8.73 1.825.369-1.755-8.73-1.825-.369 1.755Zm1.155-4.323 8.083 3.764.739-1.617-8.083-3.787-.739 1.64Zm3.372-5.481L10.235 6.08l6.859 5.704 1.132-1.362-6.859-5.704ZM15.57 17H6.655v2h8.915v-2ZM12.861 3.111l6.193 6.415 1.414-1.415-6.43-6.177-1.177 1.177Z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="grow bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8" :class="[hasAnyRole(['Super Admin']) ? 'w-72' : 'w-56']">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="leading-none space-x-2">
                                <span class="text-2xl sm:text-3xl font-bold text-gray-900">{{ total.data.tickets.processing }}</span>
                                <span class="text-base text-gray-600 font-semibold">Tiket</span>
                            </div>
                            <h3 class="text-base font-normal text-gray-500">Menunggu Diproses</h3>
                        </div>
                        <div class="ml-5 w-0 flex items-center justify-end flex-1 text-green-500 text-base font-bold">
                            <svg class="w-[26px] h-[26px] text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.651 7.65a7.131 7.131 0 0 0-12.68 3.15M18.001 4v4h-4m-7.652 8.35a7.13 7.13 0 0 0 12.68-3.15M6 20v-4h4" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="grow bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8" :class="[hasAnyRole(['Super Admin']) ? 'w-72' : 'w-56']">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="leading-none space-x-2">
                                <span class="text-2xl sm:text-3xl font-bold text-gray-900">{{ total.data.tickets.in_process }}</span>
                                <span class="text-base text-gray-600 font-semibold">Tiket</span>
                            </div>
                            <h3 class="text-base font-normal text-gray-500">Sedang Diproses</h3>
                        </div>
                        <div class="ml-5 w-0 flex items-center justify-end flex-1 text-red-500 text-base font-bold">
                            <svg class="w-[26px] h-[26px] text-purple-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.5 4h-13m13 16h-13M8 20v-3.333a2 2 0 0 1 .4-1.2L10 12.6a1 1 0 0 0 0-1.2L8.4 8.533a2 2 0 0 1-.4-1.2V4h8v3.333a2 2 0 0 1-.4 1.2L13.957 11.4a1 1 0 0 0 0 1.2l1.643 2.867a2 2 0 0 1 .4 1.2V20H8Z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="grow bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8" :class="[hasAnyRole(['Super Admin']) ? 'w-72' : 'w-56']">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="leading-none space-x-2">
                                <span class="text-2xl sm:text-3xl font-bold text-gray-900">{{ total.data.tickets.completed }}</span>
                                <span class="text-base text-gray-600 font-semibold">Tiket</span>
                            </div>
                            <h3 class="text-base font-normal text-gray-500">Selesai</h3>
                        </div>
                        <div class="ml-5 w-0 flex items-center justify-end flex-1 text-red-500 text-base font-bold">
                            <svg class="w-[26px] h-[26px] text-green-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m8.032 12 1.984 1.984 4.96-4.96m4.55 5.272.893-.893a1.984 1.984 0 0 0 0-2.806l-.893-.893a1.984 1.984 0 0 1-.581-1.403V7.04a1.984 1.984 0 0 0-1.984-1.984h-1.262a1.983 1.983 0 0 1-1.403-.581l-.893-.893a1.984 1.984 0 0 0-2.806 0l-.893.893a1.984 1.984 0 0 1-1.403.581H7.04A1.984 1.984 0 0 0 5.055 7.04v1.262c0 .527-.209 1.031-.581 1.403l-.893.893a1.984 1.984 0 0 0 0 2.806l.893.893c.372.372.581.876.581 1.403v1.262a1.984 1.984 0 0 0 1.984 1.984h1.262c.527 0 1.031.209 1.403.581l.893.893a1.984 1.984 0 0 0 2.806 0l.893-.893a1.985 1.985 0 0 1 1.403-.581h1.262a1.984 1.984 0 0 0 1.984-1.984V15.7c0-.527.209-1.031.581-1.403Z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div v-if="hasAnyRole(['Super Admin'])" class="grow bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8"
                    :class="[hasAnyRole(['Super Admin']) ? 'w-72' : 'w-56']">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl sm:text-3xl leading-none font-bold text-gray-900">
                                {{ total.data.employees }}
                            </span>
                            <h3 class="text-base font-normal text-gray-500">Karyawan</h3>
                        </div>
                        <div class="ml-5 w-0 flex items-center justify-end flex-1 text-red-500 text-base font-bold">
                            <svg class="w-[26px] h-[26px] text-red-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                    d="M16 19h4a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-2m-2.236-4a3 3 0 1 0 0-4M3 18v-1a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Zm8-10a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
