<script setup>
import UserNavbar from "@/Components/UserNavbar.vue";
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    auth: Object,
});
</script>

<template>
    <Head>
        <link rel="icon" href="/images/favicon.ico" type="image/x-icon">
    </Head>
    <div>
        <div class="min-h-screen bg-gray-100">
            <UserNavbar :auth="auth" :can-login="canLogin" :can-register="canRegister" />

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>