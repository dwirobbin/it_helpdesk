<script setup>
import { onMounted, ref } from 'vue';

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <button
        class="text-white bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 font-medium rounded-lg text-base px-2 md:px-4 py-2.5 w-full sm:w-auto text-center"
        ref="input" v-bind="$attrs">
        <slot />
    </button>
</template>
