<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import AdminAuthenticatedLayout from "@/Layouts/AdminAuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import NumberInput from "@/Components/NumberInput.vue";
import TextArea from "@/Components/TextArea.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const form = useForm({
    name: "",
    price: "",
    duration: "",
    size: "",
    inclusion: "",
    note: "",
    extension: "",
});

const submit = () => {
    form.post(route("package.store"));
};
</script>

<template>
    <Head title="Packages" />

    <AdminAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Add Package
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">Add new package</div>
                    
                    <form @submit.prevent="submit" class="px-12 mb-10">
                        <div>
                            <InputLabel for="name" value="Name" />

                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="off"
                                placeholder="e.g. Package A"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.name"
                            />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="price" value="Price" />

                            <NumberInput
                                id="price"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.price"
                                required
                                autocomplete="off"
                                placeholder="e.g. ₱3500"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.price"
                            />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="duration" value="Duration" />

                            <TextInput
                                id="duration"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.duration"
                                required
                                autocomplete="off"
                                placeholder="e.g. 2hrs"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.duration"
                            />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="size" value="Size" />

                            <TextInput
                                id="size"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.size"
                                required
                                autocomplete="off"
                                placeholder="e.g. 4r"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.size"
                            />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="inclusion" value="Inclusion" />

                            <TextArea
                                id="inclusion"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.inclusion"
                                required
                                autocomplete="off"
                                placeholder="e.g. 2 HOURS of Unlimited shots - 4 shot template (Single Printing)"

                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.inclusion"
                            />
                        </div>


                           <div class="mt-4">
                            <InputLabel for="note" value="Note" />

                            <TextInput
                                id="note"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.note"
                                required
                                autocomplete="off"
                                placeholder="e.g. transportation charge not included yet."

                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.note"
                            />
                        </div>


                           <div class="mt-4">
                            <InputLabel for="extension" value="Extension" />

                            <TextInput
                                id="extension"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.extension"
                                required
                                autocomplete="off"
                                placeholder="e.g. ₱1000.00 / hour extension"

                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.extension"
                            />
                        </div>

                        <PrimaryButton
                            class="mt-6 mb-6 flex float-right"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Add package
                        </PrimaryButton>
                    </form>
                </div>
            </div>
        </div>
    </AdminAuthenticatedLayout>
</template>
