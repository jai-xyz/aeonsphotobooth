<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import AButton from "@/Components/AButton.vue";

defineProps({
    packages: {
        type: Array,
    required: true,
    }

}); 
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>
    
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">Explore packages</div>
                    <div v-for="pkg in packages" :key="pkg.id">
                        <div class="p-6 text-gray-900">
                            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ pkg.name }}</h2>
                            <p>Price: {{ pkg.price }}</p>
                            <p>Duration: {{ pkg.duration }}</p>
                            <p>Size: {{ pkg.size }}</p>
                            <p>Inclusion: {{ pkg.inclusion }}</p>
                            <p>Note: {{ pkg.note }}</p>
                            <p>Extension: {{ pkg.extension }}</p>
                            <AButton :href="`/event/registration/${pkg.id}`">Avail</AButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
