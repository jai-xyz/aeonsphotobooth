<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import loginBg from "/public/images/loginbg.png";
import "../../../css/custom-styles.css";
import { onMounted } from "vue";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="relative min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <img
                id="background"
                class="absolute inset-0 w-full h-full object-cover"
                :src="loginBg"
                alt="Background"
            />
            <div
                class="relative w-[325px] mt-6 px-4 background-blur shadow-sm overflow-hidden rounded-2xl xxs-xs:w-[300px]"
            >
                <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="m-6">
                    <div
                        class="flex align-center justify-center mb-2 text-pink-900 font-bold text-xl"
                    >
                        LOGIN
                    </div>
                    <div>
                        <InputLabel
                            for="email"
                            value="Email"
                            class="text-pink-800"
                        />

                        <TextInput
                            id="email"
                            type="email"
                            class="text-xs bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                        />

                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="mt-3">
                        <InputLabel
                            for="password"
                            value="Password"
                            class="text-pink-900"
                        />

                        <TextInput
                            id="password"
                            type="password"
                              class="text-xs bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                        />

                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="block mt-2">
                        <label class="flex items-center">
                            <Checkbox
                                name="remember"
                                v-model:checked="form.remember"
                            />
                            <span class="ms-1 text-xs text-gray-600"
                                >Remember me</span
                            >
                        </label>
                    </div>

                    <div class="flex items-center justify-end mt-3">
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="no-underline text-xs text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:text-blue-500"
                        >
                            Forgot your password?
                        </Link>
                    </div>

                    <PrimaryButton
                        class="mt-3 w-full flex align-center justify-center"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Log in
                    </PrimaryButton>

                    <div
                        class="flex items-center justify-center mt-4 text-xs text-gray-400"
                    >
                        <div class="flex items-center w-full">
                            <div class="flex-grow border-t border-gray-300"></div>
                            <span class="mx-2">OR</span>
                            <div class="flex-grow border-t border-gray-300"></div>
                        </div>
                    </div>

                    <SecondaryButton
                        class="mt-3 w-full flex align-center justify-center"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        <svg
                            class="w-4 h-4 text-gray-800 dark:text-white"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M12.037 21.998a10.313 10.313 0 0 1-7.168-3.049 9.888 9.888 0 0 1-2.868-7.118 9.947 9.947 0 0 1 3.064-6.949A10.37 10.37 0 0 1 12.212 2h.176a9.935 9.935 0 0 1 6.614 2.564L16.457 6.88a6.187 6.187 0 0 0-4.131-1.566 6.9 6.9 0 0 0-4.794 1.913 6.618 6.618 0 0 0-2.045 4.657 6.608 6.608 0 0 0 1.882 4.723 6.891 6.891 0 0 0 4.725 2.07h.143c1.41.072 2.8-.354 3.917-1.2a5.77 5.77 0 0 0 2.172-3.41l.043-.117H12.22v-3.41h9.678c.075.617.109 1.238.1 1.859-.099 5.741-4.017 9.6-9.746 9.6l-.215-.002Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <span class="ms-2">Continue with Google</span>
                    </SecondaryButton>

                    <div class="text-center mt-3 text-xxs text-gray-400">
                        Don't have an account?
                        <Link
                            :href="route('register')"
                            class="text-pink-800 font-bold"
                        >
                            Register here
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>