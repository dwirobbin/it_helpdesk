<script setup>
import { defineModel } from 'vue';

const props = defineProps({
    options: {
        type: [Array, null],
        required: true,
    },
    trackBy: {
        type: String,
        default: '',
    },
    label: {
        type: String,
        default: '',
    },
});

const model = defineModel({
    type: String,
    required: true,
});
</script>

<template>
    <select v-model="model" v-bind="$attrs"
        class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
        <option value="" selected>Choose</option>
        <option v-for="(option, i) in props.options" :key="i" :value="option[props.trackBy] || option">
            {{ option[props.label] || option }}
        </option>
    </select>
</template>
