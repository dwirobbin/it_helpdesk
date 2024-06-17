<script setup>
import { onMounted, ref, watch } from 'vue';
import { Drawer } from 'flowbite';

const isShow = ref(false);
const drawer = ref(null);

function toggleSidebar() {
    isShow.value = !isShow.value;
    drawer.value.toggle();
}

watch(isShow, () => {
    if (isShow.value) {
        const sidebarBackdrop = document.querySelector('body > div[drawer-backdrop]');
        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', () => isShow.value = false);
        }
    }
});

onMounted(() => {
    const sidebar = document.getElementById('sidebar');
    drawer.value = new Drawer(sidebar, {
        backdrop: true,
        backdropClasses: "bg-gray-900/30 fixed inset-0 z-10",
    });
});
</script>

<template>
    <button type="button" id="toggleSidebarMobile" @click="toggleSidebar"
        class="inline-flex items-center p-2 mr-2 text-sm text-gray-500 rounded-lg lg:hidden cursor-pointer hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
        <svg v-if="isShow" id="toggleSidebarMobileHamburger" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd"></path>
        </svg>
        <svg v-else id="toggleSidebarMobileClose" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd"
                d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                clip-rule="evenodd"></path>
        </svg>
    </button>
</template>
