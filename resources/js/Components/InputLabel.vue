<script setup>
defineProps({
    value: {
        type: String,
    },
    required: Boolean,
});
</script>

<template>
    <label class="text-sm font-medium text-gray-900 block mb-2">
        <span v-if="value">{{ value }}</span>
        <span v-else>
            <slot />
        </span>
        <span v-if="required" class="text-red-600">*</span>
    </label>
</template>
