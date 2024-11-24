<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import AButton from "@/Components/AButton.vue";


const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    auth: Object,
    packages: {
        type: Array,
        required: true,
    },
});

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout :auth="auth" >
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>
    
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                   <div class="flex justify-between align-center"> 
                   <div class="p-6 text-gray-900">Explore packages "Slider" </div>
                   <div class="p-6 text-gray-900"><a href="/packages">See all packages</a></div>
                   </div>
                    <div class="flex flex-wrap -mx-3 px-5">
                        <div v-for="pkg in packages" :key="pkg.id" class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 px-3 mb-6">
                            <div class="p-6 text-gray-900 border">
                                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ pkg.name }}</h2>
                                <p>Price: {{ pkg.price }}</p>
                                <p>Duration: {{ pkg.duration }}</p>
                                <p>Size: {{ pkg.size }}{{ pkg.size2 ? ', ' + pkg.size2 : '' }}{{ pkg.size3 ? ', ' + pkg.size3 : '' }}{{ pkg.size4 ? ', ' + pkg.size4 : '' }}{{ pkg.size5 ? ', ' + pkg.size5 : '' }}</p>
                                <ul class="list-disc pl-5">
                                    <li v-for="(item, index) in pkg.inclusion.split('\n')" :key="index">{{ item }}</li>
                                </ul>
                                <p>Note: {{ pkg.note }}</p>
                                <p>Extension: {{ pkg.extension }}</p>
                                <AButton :href="`/packages/${pkg.id}`">View Package</AButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
