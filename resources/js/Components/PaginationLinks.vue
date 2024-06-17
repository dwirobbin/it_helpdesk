<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    pagination: {
        type: Object,
        required: true,
    }
});

const pageWindow = usePage();

const page = ref(props.pagination.current_page);

watch(() => props.pagination.current_page, (newPage) => {
    page.value = newPage;
});

function loadPage(pageUrl) {
    router.get(pageWindow.url, { page: pageUrl }, {
        preserveScroll: true,
        preserveState: true
    });
}

const noPreviousPage = computed(() => props.pagination.current_page - 1 <= 0);

const noNextPage = computed(() => props.pagination.current_page + 1 > props.pagination.last_page);

const onlyNumber = ($event) => {
    let charCode = ($event.keyCode ? $event.keyCode : $event.which);
    if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 9) { // charCode != 46 is except dot
        $event.preventDefault();
    } else {
        return true;
    }
};
</script>

<template>
    <div
        class="bottom-0 right-0 flex flex-col gap-x-6 gap-y-5 h-[8.5rem] md:h-[4.1rem] justify-center md:flex-row md:justify-between items-center pt-4 bg-white border-t border-gray-200">
        <div class="flex items-center md:mb-0">
            <span class="text-sm font-normal text-gray-500">
                Tampil
                <span class="font-semibold text-gray-900" v-text="`${pagination.from}-${pagination.to}`" />
                dari
                <span class="font-semibold text-gray-900" v-text="pagination.total" />
            </span>
        </div>

        <nav v-if="pagination.last_page > 1">
            <div class="flex flex-nowrap items-center justify-center space-x-1 text-sm">
                <button :disabled="noPreviousPage" :class="{ 'opacity-40': noPreviousPage }" @click="loadPage(1)"
                    class="inline-flex justify-center items-center px-2 h-[2.35rem] w-12 text-sm font-medium text-dark bg-gray-200 border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 lg:h-3 lg:w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                </button>
                <button :disabled="noPreviousPage" :class="{ 'opacity-40': noPreviousPage }" @click="loadPage(pagination.current_page - 1)"
                    class="inline-flex justify-center items-center px-2 h-[2.35rem] w-12 ms-3 text-sm font-medium text-dark bg-gray-200 border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 lg:h-3 lg:w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <div class="flex flex-col space-y-2 justify-center items-center md:flex-row md:space-y-0 md:items-center md:space-x-1">
                    <input type="number" inputmode="numeric" pattern="[0-9]*" @keypress="onlyNumber" @keydown.enter="loadPage(page)"
                        v-model="page"
                        class="px-2 w-12 h-[2.35rem] text-sm text-center font-medium text-dark bg-gray-200 border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500" />
                    <div class="px-2 text-gray-800 dark:text-gray-300 lg:text-sm text-nowrap">of {{ pagination.last_page }}</div>
                </div>

                <button :disabled="noNextPage" :class="{ 'opacity-40': noNextPage }" @click="loadPage(pagination.current_page + 1)"
                    class="inline-flex justify-center items-center px-2 h-[2.35rem] w-12 ms-3 text-sm font-medium text-dark bg-gray-200 border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 lg:h-3 lg:w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <button :disabled="noNextPage" :class="{ 'opacity-40': noNextPage }" @click="loadPage(pagination.last_page)"
                    class="inline-flex justify-center items-center px-2 h-[2.35rem] w-12 ms-3 text-sm font-medium text-dark bg-gray-200 border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 lg:h-3 lg:w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </nav>
    </div>
</template>

<style scoped>
/* Chrome, Safari, Edge, Opera */
input::-webkit-inner-spin-button,
input::-webkit-outer-spin-button {
    -webkit-appearance: none;
    appearance: none;
    margin: 0;
}

/* Firefox */
input[type=number] {
    -moz-appearance: textfield;
}
</style>
