<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
// import Select from "@/Components/Select.vue";
import { ref } from "vue";

const userId = usePage().props.auth.user.id;

const form = useForm({
    event: "",
    date: "",
    time: "",
    address: "",
    backdroptype: "",
    backdropcolor: "",
    suggestion: "",
    user_id: userId,
});

const submit = () => {
    form.post(route("event.store"));
};

const options = ref([
    {
        value: "Plain",
        image: "/images/plain.png",
    },
    {
        value: "Sequins",
        image: "/images/sequins.jpg",
    },
]);

const activeStep = ref(1);

const validateStep = () => {
    let isValid = true;
    form.errors = {}; // Clear previous errors

    if (activeStep.value === 1) {
          if (!form.event) {
            form.errors.event = "Event name is required.";
            isValid = false;
        }
        if (!form.address) {
            form.errors.address = "Address is required.";
            isValid = false;
        }

         if (!form.date) {
            form.errors.date = "Date is required.";
            isValid = false;
        } else if (!validateDate(form.date)) {
            form.errors.date = "Date must be a future date.";
            isValid = false;
        }
        if (!form.time) {
            form.errors.time = "Time is required.";
            isValid = false;
        }
    } else if (activeStep.value === 2) {
       
         if (!form.backdroptype) {
            form.errors.backdroptype = "Backdrop type is required.";
            isValid = false;
        }
        if (!form.backdropcolor) {
            form.errors.backdropcolor = "Backdrop color is required.";
            isValid = false;
        }
        if (!form.suggestion) {
            form.errors.suggestion = "Suggestion is required.";
            isValid = false;
        }
    } else if (activeStep.value === 3) {
     
    }
    return isValid;
};

const nextStep = () => {
    if (validateStep()) {
        activeStep.value++;
    }
};

const prevStep = () => {
    activeStep.value--;
};

const validateDate = (date) => {
    const currentDate = new Date();
    const selectedDate = new Date(date);

    if (selectedDate < currentDate) {
        return false;
    }
    return true;
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
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg px-14 py-6"
                >
                    <form @submit.prevent="submit">
                        <!-- STEP 1: BACKDROP DETAILS -->
                        <div v-if="activeStep === 1">
                            <h1 class="text-center">
                                Step 1: Backdrop Details
                            </h1>
                            <div
                                class="grid grid-cols-1 mt-6 md:grid-cols-3 gap-4"
                            >
     <div>
                                    <InputLabel
                                        for="event"
                                        value="Event Name"
                                    />
                                    <TextInput
                                        id="event"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.event"
                                        required
                                        autofocus
                                        autocomplete="event"
                                        placeholder="e.g. Aeon's Birthday"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.event"
                                    />
                                </div>

                                <div>
                                    <InputLabel for="address" value="Address" />
                                    <TextInput
                                        id="address"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.address"
                                        required
                                        autocomplete="address"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.address"
                                    />
                                </div>
                                <div>
                                    <InputLabel for="date" value="Date" />
                                    <TextInput
                                        id="date"
                                        type="date"
                                        class="mt-1 block w-full"
                                        v-model="form.date"
                                        :required="validateDate"
                                        autocomplete="date"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.date"
                                    />
                                </div>

                                <div>
                                    <InputLabel for="time" value="Time" />
                                    <TextInput
                                        id="time"
                                        type="time"
                                        class="mt-1 block w-full"
                                        v-model="form.time"
                                        required
                                        autocomplete="time"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.time"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- STEP 2: EVENT DATE DETAILS -->
                        <div v-if="activeStep === 2">
                            <h1 class="text-center">
                                Step 2: Event Date Details
                            </h1>
                            <div
                                class="grid grid-cols-1 mt-6 md:grid-cols-3 gap-4"
                            >

                             <div>
                                    <InputLabel
                                        for="backdroptype"
                                        value="Backdrop Type"
                                    />
                                    <select
                                        class="select select-info border-gray-300 mt-1 block w-full"
                                        id="backdroptype"
                                        name="backdroptype"
                                        required
                                        v-model="form.backdroptype"
                                    >
                                        <option disabled value="">
                                            Select Background Type
                                        </option>
                                        <option
                                            v-for="(option, index) in options"
                                            :key="index"
                                            :value="option.value"
                                        >
                                            {{ option.value }}
                                        </option>
                                    </select>

                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.backdroptype"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="backdropcolor"
                                        value="Backdrop Color"
                                    />
                                    <TextInput
                                        id="backdropcolor"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.backdropcolor"
                                        required
                                        autocomplete="backdropcolor"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.backdropcolor"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="suggestion"
                                        value="Suggestions"
                                    />
                                    <TextInput
                                        id="suggestion"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.suggestion"
                                        required
                                        autocomplete="suggestion"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.suggestion"
                                    />
                                </div>

                               
                            </div>
                        </div>

                        <!-- STEP 3: EVENT DETAILS -->
                        <div v-if="activeStep === 3">
                            <h1 class="text-center">Step 3: Event Details</h1>
                            <div
                                class="grid grid-cols-1 mt-6 md:grid-cols-3 gap-4"
                            >
                               
                            </div>
                        </div>

                        <input
                            type="hidden"
                            name="user_id"
                            :value="form.user_id"
                        />

                        <PrimaryButton
                            class="mt-6 flex float-left"
                            @Click="prevStep"
                            v-if="activeStep > 1"
                        >
                            Back
                        </PrimaryButton>

                        <PrimaryButton
                            class="mt-6 flex float-right"
                            v-if="activeStep < 3"
                            @Click="nextStep"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Next >
                        </PrimaryButton>

                        <PrimaryButton
                            class="mt-6 flex float-right"
                            v-if="activeStep === 3"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Register
                        </PrimaryButton>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
