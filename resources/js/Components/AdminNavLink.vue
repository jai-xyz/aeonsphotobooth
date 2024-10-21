<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'flex w-full p-2 text-gray-600 rounded-lg font-medium text-base bg-pink-50 transition duration-150 ease-in-out'
        : 'flex w-full p-2 text-gray-600 rounded-lg font-medium hover:bg-gray-100 transition duration-150 ease-in-out'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
