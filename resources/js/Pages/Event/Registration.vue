<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";

const userId = usePage().props.auth.user.id;

const form = useForm({
    event: "",
    date: "",
    time: "",
    address: "",
    backdroptype: "",
    backdropcolor: "",
    numofpics: "",
    suggestion: "",
    user_id: userId,
});

const submit = () => {
    form.post(route('event.store'), {
    });
};

</script>

<template>
    <Head title="Registration" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Event Registration
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg px-14 py-6">

                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <InputLabel for="event" value="Event Name" />
                                <TextInput
                                    id="event"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.event"
                                    required
                                    autofocus
                                    autocomplete="event"
                                />
                                <InputError class="mt-2" :message="form.errors.event" />
                            </div>

                            <div>
                                <InputLabel for="date" value="Date" />
                                <TextInput
                                    id="date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.date"
                                    required
                                    autofocus
                                    autocomplete="date"
                                />
                                <InputError class="mt-2" :message="form.errors.date" />
                            </div>

                            <div>
                                <InputLabel for="time" value="Time" />
                                <TextInput
                                    id="time"
                                    type="time"
                                    class="mt-1 block w-full"
                                    v-model="form.time"
                                    required
                                    autofocus
                                    autocomplete="time"
                                />
                                <InputError class="mt-2" :message="form.errors.time" />
                            </div>
                        </div>

                            <div class="grid grid-cols-1 mt-6 md:grid-cols-3 gap-4 ">
                            <div>
                                <InputLabel for="address" value="Address" />
                                <TextInput
                                    id="address"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.address"
                                    required
                                    autofocus
                                    autocomplete="address"
                                />
                                <InputError class="mt-2" :message="form.errors.address" />
                            </div>

                            <div>
                                <InputLabel for="backdroptype" value="Backdrop Type" />
                                <TextInput
                                    id="backdroptype"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.backdroptype"
                                    required
                                    autofocus
                                    autocomplete="backdroptype"
                                />
                                <InputError class="mt-2" :message="form.errors.backdroptype" />
                            </div>

                            <div>
                                <InputLabel for="backdropcolor" value="Backdrop Color" />
                                <TextInput
                                    id="backdropcolor"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.backdropcolor"
                                    required
                                    autofocus
                                    autocomplete="backdropcolor"
                                />
                                <InputError class="mt-2" :message="form.errors.backdropcolor" />
                            </div>

                        </div>

                            <div class="grid grid-cols-1 mt-6 md:grid-cols-3 gap-4 ">
                         
                            <div>
                                <InputLabel for="suggestion" value="Suggestions" />
                                <TextInput
                                    id="suggestion"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.suggestion"
                                    required
                                    autofocus
                                    autocomplete="suggestion"
                                />
                                <InputError class="mt-2" :message="form.errors.suggestion" />
                            </div>

                         
                        </div>  

                        <!-- hidden: userId -->

                        <input type="hidden" name="user_id" :value="form.user_id">

                        <PrimaryButton class="mt-6 flex float-right" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Register
                        </PrimaryButton>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>