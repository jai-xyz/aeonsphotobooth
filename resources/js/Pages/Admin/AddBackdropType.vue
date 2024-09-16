<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import AdminAuthenticatedLayout from "@/Layouts/AdminAuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const form = useForm({
    name: "",
});

const submit = () => {
    form.post(route("backdroptype.store"));
};
</script>

<template>
    <div>
        <div>
            <div class="bg-white sm:rounded-lg">
                <h2 class="text-lg font-medium text-gray-900">Add Backdrop Type</h2>

                <form @submit.prevent="submit" class="mt-6 space-y-6">
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
                            placeholder="e.g. Plain"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <PrimaryButton
                        class="mt-6"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Add Backdrop type
                    </PrimaryButton>
                </form>
            </div>
        </div>
    </div>
</template>
