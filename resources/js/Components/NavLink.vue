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
    onClick:{
        type: Function,
        required: false,
    }
});

const classes = computed(() =>
    props.active
        ? 'inline-flex items-center px-2 py-2 rounded bg-pink-50 text-pink-400 text-base font-medium leading-5 text-gray-900 focus:outline-none focus:border-pink-700 transition duration-150 ease-in-out'
        : 'inline-flex items-center px-2 py-2 rounded text-base font-medium leading-5 text-gray-500 hover:bg-gray-50 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out'
);

const handleClick = (event) => {
    if (props.onClick) {
        event.preventDefault();
        props.onClick(event);
    }
};
</script>

<template>
    <Link :href="href" :class="classes" @click="handleClick">
        <slot />
    </Link>
</template>
