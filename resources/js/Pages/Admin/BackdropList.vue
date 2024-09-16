<script setup>
import { Head } from "@inertiajs/vue3";
import AdminAuthenticatedLayout from "@/Layouts/AdminAuthenticatedLayout.vue";
import AButton from "@/Components/AButton.vue";

defineProps({
    backdrops: {
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
                Backdrop
                <AButton :href="route('backdrop.create')">Add</AButton>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex justify-center flex-col"
                >
                    <div class="p-6 text-gray-900">List of backdrops</div>

                    <table
                        class="border-separate border-spacing-2 border-2 border-slate-500 table-fixed"
                    >
                        <thead class="bg-slate-500">
                            <tr>
                                <th>Type</th>
                                <th>Color</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <tr
                                v-for="backdrop in backdrops"
                                :key="backdrop.id"
                            >
                                <td>{{ backdrop.backdroptype }}</td>
                                <td>{{ backdrop.color }}</td>
                                <td v-if="backdrop.backdroptype == 'Plain'">
                                    <img :src="`/uploads/backdrop/plain/` + backdrop.image" width="125px" alt="Backdrop Image" />
                                </td>

                                <td v-else-if="backdrop.backdroptype == 'Sequins'">
                                    <img :src="`/uploads/backdrop/sequins/`  + backdrop.image" width="125px" alt="Backdrop Image" />
                                </td>

                                  <td v-else-if="backdrop.backdroptype == 'Custom'">
                                    <img :src="`/uploads/backdrop/custom/`  + backdrop.image" width="125px" alt="Backdrop Image" />
                                </td>
                                <!-- <td><AButton :href="`/admin/package/edit/${pkg.id}`">Edit</AButton></td> -->
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminAuthenticatedLayout>
</template>
