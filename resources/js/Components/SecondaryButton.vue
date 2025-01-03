<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
    
        :type="type"
        class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-md font-semibold text-sm text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-offset-2 transition ease-in-out duration-150"
    >
        <slot />
    </button>
</template>
