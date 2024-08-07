<script setup>
import { onMounted, ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Dropdown } from 'flowbite';
import HumbergerButton from '@/Components/HumbergerButton.vue';

const page = usePage();

const user = computed(() => page.props.auth.user?.data);

const setting = computed(() => page.props.setting.data);

const generateUrl = (filePath) => window.location.origin + filePath;

const dropdowns = ref({});

onMounted(() => {
    const dropdownElements = document.querySelectorAll('div[id^="dropdown-"]');
    dropdownElements.forEach(menuEl => {
        const targetId = menuEl.getAttribute('id');
        const triggerId = `dropdown-${targetId.split('-')[1]}-btn`;
        const triggerEl = document.getElementById(triggerId);

        if (targetId && triggerEl) {
            dropdowns[targetId] = new Dropdown(menuEl, triggerEl, {
                placement: 'bottom-start'
            });
        }
    });
});
</script>

<template>
    <nav class="fixed top-0 bg-white border-b border-gray-200 z-30 w-full">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start">

                    <HumbergerButton />

                    <Link :href="route('home')" class="hidden lg:flex text-xl font-bold items-center lg:ml-2.5">
                    <img class="mr-2 rounded-md w-8 h-8 object-cover" :src="[setting.app_logo ?? generateUrl('/assets/images/default-img.png')]"
                        alt="App Logo">
                    <span class="self-center whitespace-nowrap">{{ setting.app_name }}</span>
                    </Link>
                </div>
                <div class="flex items-center space-x-1">
                    <div>
                        <button type="button" id="dropdown-user-btn" class="flex space-x-1 items-center text-sm rounded-full md:me-0">
                            <h1 class="font-medium"><b>{{ user.role.name }}</b></h1>
                            <img class="w-[2.15rem] h-[2.15rem] rounded-full" :src="user?.photo" alt="user photo">
                        </button>
                        <div class="z-50 my-4 absolute translate-x-[-20rem] hidden text-base list-none bg-white divide-y divide-gray-100 rounded shadow border dark:bg-gray-700 dark:divide-gray-600"
                            id="dropdown-user-menu">
                            <div class="px-4 py-3" role="none">
                                <p class="text-sm text-gray-900 dark:text-white" role="none">
                                    {{ user?.name }}
                                </p>
                                <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                                    {{ user?.email }}
                                </p>
                            </div>
                            <ul class="py-1" role="none">
                                <li>
                                    <Link :href="route('profile.edit')" method="get" as="button" type="button"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                    Profil Saya
                                    </Link>
                                </li>
                                <li>
                                    <Link :href="route('logout')" method="post" as="button" type="button"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                    Keluar
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <svg class="w-[18px] h-[18px] text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                    </svg>
                </div>
            </div>
        </div>
    </nav>
</template>
