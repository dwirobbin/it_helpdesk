<script setup>
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    maxWidth: {
        type: String,
        default: 'lg',
    },
});

const setting = computed(() => usePage().props.setting.data);

const generateUrl = (filePath) => window.location.origin + filePath;

const backgroundImageUrl = ref(generateUrl('/assets/images/bg-app.png'))

const maxWidthClass = computed(() => {
    return {
        lg: 'sm:max-w-lg',
        '2xl': 'sm:max-w-2xl',
    }[props.maxWidth];
});
</script>

<template>
    <main class="bg-gray-50 bg-app" :style="{
        backgroundImage: `url('${backgroundImageUrl}')`,
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        width: '100%',  // Untuk memastikan lebar penuh
        height: '100vh',  // Sesuaikan dengan tinggi yang diinginkan
    }">
        <div class="mx-auto md:h-screen flex flex-col justify-center items-center px-6 py-8">
            <a :href="route('home')" class="text-2xl font-semibold flex justify-center items-center mb-8 lg:mb-10">
                <img class="rounded-md h-14 w-14 mr-4 object-cover" :src="[setting.logo ?? generateUrl('/assets/images/default-img.png')]"
                    alt="App Logo">
                <span class="self-center text-2xl font-bold whitespace-nowrap">{{ setting.name }}</span>
            </a>
            <!-- Card -->
            <div class="w-full p-4 space-y-8 sm:p-8 bg-white rounded-lg shadow dark:bg-gray-800 lg:p-10 2xl:p-16" :class="maxWidthClass">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900">
                    {{ title }}
                </h2>

                <slot />
            </div>
        </div>
    </main>
</template>
