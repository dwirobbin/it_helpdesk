<script setup>
import ToastListItem from "@/Components/ToastListItem.vue";
import { onUnmounted } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import toast from "@/Stores/toast";

const page = usePage();

let removeSuccessEventListener = router.on('success', () => {
    if (page.props.flash) {
        toast.add({
            type: page.props.flash.type,
            message: page.props.flash.text,
            duration: 3000,
        });
    }
});

const remove = (index) => {
    toast.remove(index);
    page.props.flash = null;
};

onUnmounted(() => removeSuccessEventListener());
</script>

<template>
    <TransitionGroup tag="div" enter-from-class="translate-x-full opacity-0" enter-active-class="duration-500" leave-active-class="duration-500"
        leave-to-class="translate-x-full opacity-0" class="fixed top-4 right-4 z-50 space-y-4 w-full max-w-xs">
        <ToastListItem v-for="(item, index) in toast.items" v-bind:key="item.key" :type="item.type" :message="item.message"
            :duration="item.duration" @remove="remove(index)" />
    </TransitionGroup>
</template>
