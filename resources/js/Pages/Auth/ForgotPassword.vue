<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import loginBg from "/public/images/loginbg.png";
import "../../../css/custom-styles.css";

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
});

const submit = () => {
    form.post(route("password.email"));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />
        <div
            class="relative min-h-screen flex flex-col justify-center items-center pt-6"
        >
            <img
                id="background"
                class="absolute inset-0 w-full h-full object-cover"
                :src="loginBg"
                alt=""
            />
            <div
                class="relative w-full max-w-[300px] xs:max-w-md sm:max-w-md md:max-w-md lg:max-w-md mt-6 p-6 background-blur shadow-sm overflow-hidden rounded-2xl mx-4"
            >
                <div class="mb-1 text-pink-900 font-bold text-xl xs:text-2xl">
                    Forgot your password?
                </div>
                <div class="mb-3 text-sm font-light text-gray-500">
                    Just let us know your email address and we will email you a
                    password reset link that will allow you to choose a new one.
                </div>

                <div
                    v-if="status"
                    class="mb-4 font-medium text-sm text-green-600"
                >
                    {{ status }}
                </div>

                <form @submit.prevent="submit">
                    <div>
                        <InputLabel value="Email" class="text-pink-800" />

                        <div class="relative mb-4">
                            <div
                                class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none"
                            >
                                <svg
                                    class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor"
                                    viewBox="0 0 20 16"
                                >
                                    <path
                                        d="m10.036 8.278 9.258-7.79A1.979 1.979 0 0 0 18 0H2A1.987 1.987 0 0 0 .641.541l9.395 7.737Z"
                                    />
                                    <path
                                        d="M11.241 9.817c-.36.275-.801.425-1.255.427-.428 0-.845-.138-1.187-.395L0 2.6V14a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2.5l-8.759 7.317Z"
                                    />
                                </svg>
                            </div>
                            <TextInput
                                id="email"
                                type="email"
                                class="ps-10 text-sm bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="off"
                                placeholder="example@mail.com"
                            />
                        </div>

                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <PrimaryButton
                        class="flex align-center justify-center w-full mt-0"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Send Password Reset Link
                    </PrimaryButton>

                    <div class="flex items-center justify-center">
                        <div class="text-center mt-3 text-sm text-gray-500">
                            Or
                            <Link
                                :href="route('login')"
                                class="text-pink-800 hover:underline active:text-pink-400"
                            >
                                go back to login.
                            </Link>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>
