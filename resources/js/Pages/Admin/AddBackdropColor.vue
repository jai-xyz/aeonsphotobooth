<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import AdminAuthenticatedLayout from "@/Layouts/AdminAuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import NumberInput from "@/Components/NumberInput.vue";
import TextArea from "@/Components/TextArea.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

defineProps({
    backdroptypes: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    backdroptype_id: "",
    color: "",
});

const submit = () => {
    form.post(route("backdropcolor.store"));
};
</script>

<template>
    <Head title="Packages" />

    <AdminAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Add Backdrop Color
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">Add new backdrop color</div>

                    <form @submit.prevent="submit" class="px-12 mb-10">
                        <div>
                            <InputLabel for="backdroptype" value="Backdrop Type" />

                            <select
                                id="backdroptype"
                                placeholder="Backdrop Type"
                                class="select select-info rounded-e-none border-gray-300 mt-1 block w-full"
                                v-model="form.backdroptype_id"
                            >
                                <option disabled value="">Backdrop Type</option>
                                <option
                                    v-for="backdroptype in backdroptypes"
                                    :key="backdroptype.id"
                                    :value="backdroptype.id"
                                >
                                    {{ backdroptype.name }}
                                </option>
                            </select>
                            <InputError
                                class="mt-2"
                                :message="form.errors.backdroptype"
                            />
                        </div>

                        <div>
                            <InputLabel for="color" value="Color" />

                            <TextInput
                                id="color"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.color"
                                required
                                autofocus
                                autocomplete="off"
                                placeholder="e.g. Plain"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.color"
                            />
                        </div>

                        <PrimaryButton
                            class="mt-6 mb-6 flex float-right"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Add Backdrop color
                        </PrimaryButton>
                    </form>
                </div>
            </div>
        </div>
    </AdminAuthenticatedLayout>
</template>
