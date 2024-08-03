<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import AButton from '@/Components/AButton.vue';
import EventListTable from '@/Pages/Event/Partials/EventListTable.vue';

defineProps({
    events: {
        type: Array,
        required: true,
    },
});

</script>


<template>
    <Head title="Events" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Events  <AButton :href="route('event.create')">Add</AButton> </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex justify-center flex-col">
                    <div class="p-6 text-gray-900">Lists of Event</div>
                    <EventListTable :events="events" class="mx-10 mb-10" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

