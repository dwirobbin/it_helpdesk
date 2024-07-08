<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const setting = computed(() => usePage().props.setting.data);
</script>

<template>
    <footer class="pt-1 pb-4 px-4">
        <div class="p-4 bg-white border rounded-lg shadow flex flex-col sm:flex-row items-center sm:justify-center md:p-6 xl:p-7">
            <p class="text-sm text-center text-gray-500 mb-6 space-y-1 sm:mb-0">
                &copy; {{ new Date().getFullYear() }}
                <a :href="route('home')" class="hover:underline" target="_blank">{{ setting.app_name }}</a>.
                {{ setting.app_footer }}.
            </p>
        </div>
    </footer>
</template>
