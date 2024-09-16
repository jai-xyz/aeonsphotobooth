<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import AdminAuthenticatedLayout from "@/Layouts/AdminAuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
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
    image: null,
});

const handleFileUpload = (event) => {
    form.image = event.target.files[0];
};

const submit = () => {
    const formData = new FormData();
    formData.append("backdroptype_id", form.backdroptype_id);
    formData.append("color", form.color);
    formData.append("backdropimg", form.image);

    form.post(route("backdropcolor.store"), {
        data: formData,
        onSuccess: () => {
            console.log("success");
        },
        onError: () => {
            console.log("error");
        },
    });
};
</script>

<template>
    <div>
        <div>
            <div>
                <h2 class="text-lg font-medium text-gray-900">Add Backdrop Color</h2>

                <form @submit.prevent="submit" class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="backdroptype" value="Backdrop Type" />

                        <select
                            id="backdroptype"
                            placeholder="Backdrop Type"
                            class="select select-info rounded-lg border-gray-300 mt-1 block w-full"
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
                            autocomplete="off"
                            placeholder="e.g. Black, White, Red, etc."
                        />
                        <InputError class="mt-2" :message="form.errors.color" />
                    </div>

                    <div>
                        <InputLabel
                            for="backdropimage"
                            value="Backdrop Image"
                        />

                        <input
                            type="file"
                            class="file-input w-full mt-1 ps-0 border-gray-300 file-input-info"
                            name="image"
                            id="image"
                            @change="handleFileUpload"
                        />
                    </div>

                    <PrimaryButton
                        class="mt-6"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Add Backdrop color
                    </PrimaryButton>
                </form>
            </div>
        </div>
    </div>
</template>
