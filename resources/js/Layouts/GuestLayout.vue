<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Head>
        <link rel="icon" href="/images/favicon.ico" type="image/x-icon">
    </Head>
    <slot />
</template>
