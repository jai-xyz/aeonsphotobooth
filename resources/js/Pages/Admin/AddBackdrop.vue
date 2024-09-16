<script setup>
import AdminAuthenticatedLayout from "@/Layouts/AdminAuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import AddBackdropType from "../Admin/AddBackdropType.vue";
import AddBackdropColor from "../Admin/AddBackdropColor.vue";
import AButton from "@/Components/AButton.vue";

defineProps({
    backdroptypes: {
        type: Array,
        required: true,
    }
});

</script>


<template>
    <Head title="Backdrop" />

    <AdminAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Backdrop  <AButton :href="route('backdrop.index')">View List</AButton>
            </h2>
        </template> 

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg mb-9">
                    <AddBackdropType class="max-w-xl" />
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <AddBackdropColor class="max-w-xl" :backdroptypes="backdroptypes" />
                </div>
            </div>
        </div>
    </AdminAuthenticatedLayout>
</template>