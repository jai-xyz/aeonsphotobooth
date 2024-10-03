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

        <div
            class="relative min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0 "
        >
            <img
                id="background"
                class="absolute inset-0 w-full h-full object-cove"
                :src="loginBg"
                alt="Background"
            />
            <div
                class="relative w-[325px] mt-6 px-6 background-blur shadow-sm overflow-hidden rounded-2xl xxs-xs:w-[300px] xxs-xs:px-0"
            >
                <div
                    v-if="status"
                    class="mb-4 font-medium text-sm text-green-600"
                >
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="m-6">
                    <div class="flex align-center justify-center">
                        <img
                            id="logo"
                            src=""
                            alt="Logo"
                            width="50px"
                            class=""
                        />
                    </div>

                    <div
                        class="flex align-center justify-center mb-2 text-pink-900 font-bold text-xl"
                    >
                        Login
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

                        <InputError
                            class="mt-2"
                            :message="form.errors.password"
                        />
                    </div>

                    <div class="flex items-center justify-between mt-2">
                        <label class="flex items-center">
                            <Checkbox
                                name="remember"
                                v-model:checked="form.remember"
                            />
                            <span class="ms-1 mt-1 text-xs text-gray-600"
                                >Remember me</span
                            >
                        </label>

                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-xs mt-1 text-pink-800 font-bold hover:text-pink-600 active:text-pink-400"
                        >
                            Forgot Password?
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
                            <div
                                class="flex-grow border-t border-gray-300"
                            ></div>
                            <span class="mx-2">OR</span>
                            <div
                                class="flex-grow border-t border-gray-300"
                            ></div>
                        </div>
                    </div>

                    <SecondaryButton
                        class="mt-3 w-full flex align-center justify-center px-0"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                      <img src="/images/google_icon_logo.svg" alt="" width="20px">
                        <span class="text-xs ms-2">Continue with Google</span>
                    </SecondaryButton>

                    <div class="text-center mt-3 text-xs text-gray-500">
                        Don't have an account?
                        <Link
                            :href="route('register')"
                            class="text-pink-800 font-bold hover:text-pink-600 active:text-pink-400"
                        >
                            Register for free
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>
