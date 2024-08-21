<script setup>
import { Head } from "@inertiajs/vue3";
import AdminAuthenticatedLayout from "@/Layouts/AdminAuthenticatedLayout.vue";
import AButton from "@/Components/AButton.vue";

defineProps({
    packages: {
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
                Packages
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex justify-center flex-col">
                    <div class="p-6 text-gray-900">List of packages</div>
<div class="overflow-x-auto">
                    <table class="border-separate border-spacing-2 border-2 border-slate-500 table-fixed  table-pin-rows">
                                <thead class="bg-slate-500">
                                    <tr>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Duration</th>
                                        <th>Size/s</th>
                                        <th>Inclusion</th>
                                        <th>Note</th>
                                        <th>Extension</th>
                                        <th>Action</th> 
                                    </tr>
                                </thead>

                                <tbody>
                                      <tr v-for="pkg in packages" :key="pkg.id">
                                        <td>{{ pkg.name }}</td>
                                        <td>{{ pkg.price }}</td>
                                        <td>{{ pkg.duration }}</td>
                                       <td>{{ pkg.size }}{{ pkg.size2 ? ', ' + pkg.size2 : '' }}{{ pkg.size2 ? ', ' + pkg.size2 : '' }}{{ pkg.size3 ? ', ' + pkg.size3 : '' }}{{ pkg.size4 ? ', ' + pkg.size4 : '' }}{{ pkg.size5 ? ', ' + pkg.size5 : '' }}</td>
                                        <td>{{ pkg.inclusion }}</td>
                                        <td>{{ pkg.note }}</td>
                                        <td>{{ pkg.extension }}</td>
                                        <td><AButton :href="`/admin/package/edit/${pkg.id}`">Edit</AButton></td>
                                    </tr>
                                </tbody>
                            </table>
                             </div>
                </div>
            </div>
        </div>
    </AdminAuthenticatedLayout>
</template>