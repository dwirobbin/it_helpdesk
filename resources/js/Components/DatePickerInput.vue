<script setup>
import { onMounted, ref, defineModel } from 'vue';
import Datepicker from 'flowbite-datepicker/Datepicker';

const selectedDate = defineModel({
    type: [String, null],
    required: true,
});

const datepickerRef = ref(null)

onMounted(() => {
    new Datepicker(datepickerRef.value, {
        format: "dd/mm/yyyy",
    });

    datepickerRef.value.addEventListener('changeDate', (event) => {
        selectedDate.value = event.target.value;
    });
});
</script>

<template>
    <input type="text"
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5"
        v-model="selectedDate" ref="datepickerRef" v-bind="$attrs" />
</template>
