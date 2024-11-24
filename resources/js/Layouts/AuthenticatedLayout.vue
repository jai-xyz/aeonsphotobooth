<script setup>
import UserNavbar from "@/Components/UserNavbar.vue";

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    auth: Object,
});
</script>

<template>
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