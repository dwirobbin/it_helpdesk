<script setup>
import { defineModel, ref, onMounted } from 'vue';

const model = defineModel({
    type: [String, null],
    required: true,
});

const input = ref();

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

const onlyNumber = ($event) => {
    let charCode = ($event.keyCode ? $event.keyCode : $event.which);
    if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 9) { // charCode != 46 is except dot
        $event.preventDefault();
    } else {
        return true;
    }
};

// disable up, down arrow keys
const disableArrows = (event) => {
    if ([38, 40].indexOf(event.keyCode) > -1) {
        event.preventDefault();
    }
};
</script>

<template>
    <input type="text" inputmode="numeric" pattern="[0-9]*"
        class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
        v-model="model" ref="input" v-bind="$attrs" @keypress="onlyNumber" @keydown="disableArrows($event)" />
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
