<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  href: {
    type: String,
    required: true
  },
  disabled: {
    type: Boolean,
    default: false
  }
});

</script>

<template>
    <Link :href="href" :disabled="disabled"
        class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-md font-semibold text-sm text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150"
    >
        <slot />
    </Link>
</template>

