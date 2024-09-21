<script setup>
import { Head } from "@inertiajs/vue3";
import AdminAuthenticatedLayout from "@/Layouts/AdminAuthenticatedLayout.vue";
import AButton from "@/Components/AButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

defineProps({
    backdroptypes: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Packages" />

    <AdminAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Backdrop Type List
                <AButton :href="route('backdrop.create')">Add</AButton>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex justify-center flex-col"
                >
                    <div class="p-6 text-gray-900">List of backdrop type</div>

                    <table
                        class="border-separate border-spacing-2 border-2 border-slate-500 table-fixed"
                    >
                        <thead class="bg-slate-500">
                            <tr>
                                <th>Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <tr
                                v-for="backdroptype in backdroptypes" 
                                :key="backdroptype.id"
                            >
                                <td>{{ backdroptype.name }}</td>
                                <td>
                                    <AButton :href="`/admin/backdrop/edit/${backdroptype.id}`">Edit</AButton>
                                    <SecondaryButton>Hide</SecondaryButton>
                                    </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminAuthenticatedLayout>
</template>
