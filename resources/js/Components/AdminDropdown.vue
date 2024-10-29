<script setup>
import { computed } from 'vue';

const props = defineProps({
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'flex flex-col w-full p-2 text-gray-600 rounded-lg font-medium text-base bg-pink-50 transition duration-150 ease-in-out'
        : 'flex flex-col w-full p-2 text-gray-600 rounded-lg font-medium hover:bg-gray-100 transition duration-150 ease-in-out'
);
</script>

<template>
    <li :class="classes">
        <slot />
    </li>
</template>
