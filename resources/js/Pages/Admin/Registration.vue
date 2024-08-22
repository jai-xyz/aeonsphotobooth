<script setup>
import AdminAuthenticatedLayout from "@/Layouts/AdminAuthenticatedLayout.vue";
import Modal from '@/Components/Modal.vue';
import { Head, useForm } from "@inertiajs/vue3";
import AButton from "@/Components/AButton.vue";
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { computed, ref } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    events: Object,
});

const updatingEventStatus = ref(false);

const form = useForm({
    id: "",
    user_id: "",
    status: "",
    originalStatus: ""
});

const updateEventStatus = (event) => {
    form.id = event.id;
    form.user_id = event.user_id;
    form.status = event.status;
    form.originalStatus = event.status;
    updatingEventStatus.value = true;
};

const updateStatus = () => {
    form.patch(route("event.update", { event: form.id }), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    updatingEventStatus.value = false;
    form.reset();
};


const statusOptions = ["Pending", "Accepted", "Declined"];

const isSameStatus = computed(() => {
    return form.status === form.originalStatus;
});
</script>

<template>
    <Head title="Events" />

    <AdminAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Events
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex justify-center flex-col"
                >
                    <div class="p-6 text-gray-900">Booked events</div>

                    <table
                        class="border-separate border-spacing-2 border-2 border-slate-500 table-fixed"
                    >
                        <thead class="bg-slate-500">
                            <tr>
                                <th>Event</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Address</th>
                                <th>Package Name</th>
                                <th>Package Size</th>
                                <th>Backdrop Type</th>
                                <th>Backdrop Color</th>
                                <th>Suggestion</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="event in events" :key="event.id">
                                <td>{{ event.event }}</td>
                                <td>{{ event.date }}</td>
                                <td>{{ event.time }}</td>
                                <td>{{ event.address }}</td>
                                <td>{{ event.packagename }}</td>
                                <td>{{ event.packagesize }}</td>
                                <td>{{ event.backdroptype }}</td>
                                <td>{{ event.backdropcolor }}</td>
                                <td>{{ event.suggestion }}</td>
                                <td>
                                    <span
                                        :class="{
                                            'bg-yellow-500 text-white':
                                                event.status === 'Pending',
                                            'bg-green-500 text-white':
                                                event.status === 'Accepted',
                                            'bg-red-500 text-white':
                                                event.status === 'Declined',
                                        }"
                                        class="px-2 py-1 rounded"
                                    >
                                        {{ event.status }}
                                    </span>
                                </td>

                                  <td>
                                    <PrimaryButton @click="() => updateEventStatus(event)">
                                        Update Status
                                    </PrimaryButton>
                                </td>
                            </tr>
                            <!-- no event registered -->
                            <tr v-if="events == 0">
                                <td
                                    colspan="12"
                                    class="text-center bg-gray-300"
                                >
                                    No events booked so far
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <Modal :show="updatingEventStatus" @close="closeModal">
                        <div class="p-6">
                            <h2 class="text-lg font-medium text-gray-900">
                                Are you sure you want to update the event
                                status?
                            </h2>

                            <div class="mt-6">
                                <InputLabel
                                    for="status"
                                    value="Status"
                                    class="sr-only"
                                />

                                <select
                                    class="select select-info border-gray-300 mt-1 block w-full"
                                    id="status"
                                    name="status"
                                    required
                                    v-model="form.status"
                                >
                                    <option disabled value="">
                                        Select Status
                                    </option>
                                    <option v-for="status in statusOptions"
                                    :key="status" :value="status">
                                        {{ status }}
                                    </option>
                                </select>

                                    <InputError
                                    :message="form.errors.status"
                                    class="mt-2"
                                />
                            </div>

                            <div class="mt-6 flex justify-end">
                                <SecondaryButton @click="closeModal">
                                    Cancel
                                </SecondaryButton>

                                <PrimaryButton
                                    class="ms-3"
                                    :class="{ 'opacity-25': form.processing || isSameStatus }"
                                    :disabled="form.processing || isSameStatus"
                                    @click="updateStatus"
                                    
                                >
                                    Update Status
                                </PrimaryButton>
                            </div>
                        </div>
                    </Modal>
                </div>
            </div>
        </div>
    </AdminAuthenticatedLayout>
</template>
