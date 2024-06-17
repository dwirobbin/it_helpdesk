<script setup>
import ToastList from "@/Components/ToastList.vue";
import Navbar from '@/Layouts/Partials/Navbar.vue';
import Sidebar from '@/Layouts/Partials/Sidebar.vue';
import Footer from '@/Layouts/Partials/Footer.vue';
</script>

<template>
    <ToastList />

    <Navbar />

    <div class="flex overflow-hidden bg-white">

        <Sidebar />

        <div class="h-screen w-full flex flex-col justify-between bg-gray-50 relative overflow-y-auto lg:ml-64 pt-14">
            <main>
                <slot />
            </main>

            <Footer />
        </div>
    </div>
</template>
