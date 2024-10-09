<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import NumberInput from "@/Components/NumberInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import loginBg from "/public/images/loginbg.png";
import trialBg from "/public/images/trial.png";
import "../../../css/custom-styles.css";

const form = useForm({
    fullname: "",
    contactno: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="flex min-h-screen">
            <div
                class="hidden md:flex w-4/5 justify-center items-center relative"
            >
                <img
                    id="background"
                    class="absolute inset-0 w-full h-full object-cover"
                    :src="trialBg"
                    alt=""
                />
            </div>
            <div class="w-full md:w-3/5 flex justify-center items-center">
                <div class="w-[400px] px-4 overflow-hidden rounded-2xl">
                    <form @submit.prevent="submit" class="mx-6">
                        <div class="mb-1 text-pink-900 font-bold text-xl">
                            Create an Account
                        </div>
                        <div
                            class="text-xs xs:text-sm mb-2 text-gray-500 font-thin"
                        >
                            Fun awaits. Join the photo booth party! <br />
                            Capture the moment. Sign up now.
                        </div>
                        <div>
                            <InputLabel for="fullname" value="Fullname" />

                            <TextInput
                                id="fullname"
                                type="text"
                                class="text-xs bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                v-model="form.fullname"
                                required
                                autofocus
                                autocomplete="off"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.fullname"
                            />
                        </div>

                        <div class="mt-3">
                            <InputLabel for="email" value="Email" />

                            <TextInput
                                id="email"
                                type="email"
                                class="text-xs bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                v-model="form.email"
                                required
                                autocomplete="off"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.email"
                            />
                        </div>

                        <div class="mt-3">
                            <InputLabel for="contactno" value="Phone Number" />

                            <NumberInput
                                id="contactno"
                                type="text"
                                class="text-xs bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                v-model="form.contactno"
                                required
                                autocomplete="off"
                                minlength="11"
                                maxlength="11"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.contactno"
                            />
                        </div>

                        <div class="mt-3">
                            <InputLabel for="password" value="Password" />

                            <TextInput
                                id="password"
                                type="password"
                                class="text-xs bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                v-model="form.password"
                                required
                                autocomplete="off"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.password"
                            />
                        </div>

                        <div class="mt-3">
                            <InputLabel
                                for="password_confirmation"
                                value="Confirm Password"
                            />

                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="text-xs bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="off"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.password_confirmation"
                            />
                        </div>

                        <PrimaryButton
                            class="mt-3 w-full"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Register
                        </PrimaryButton>

                        <div class="text-center mt-3 text-sm text-gray-500">
                            Already have an account?
                            <Link
                                :href="route('login')"
                                class="text-pink-800 font-bold hover:underline active:text-pink-400"
                            >
                                Login here
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
