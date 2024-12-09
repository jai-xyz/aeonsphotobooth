<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import NumberInput from "@/Components/NumberInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import "../../../css/custom-styles.css";

const form = useForm({
    fullname: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};

const signInWithGoogle = () => {
    window.location.href = route('google.auth');
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="flex min-h-screen">
            <div
                class="relative hidden lg:flex w-4/5 justify-center items-center bg-[#f9e1ea]"
            >
                <img
                    id="background"
                    class="absolute inset-0 w-full h-full object-contain"
                    src="/images/registerbg.png"
                    alt=""
                />
            </div>
            <div class="bg-gray-50 w-full lg:w-3/5 flex justify-center items-center">
                <div class="w-[400px] px-4 overflow-hidden rounded-2xl">
                    <form @submit.prevent="submit" class="mx-6">
                        <div
                            class="mb-1 text-pink-900 font-bold text-xl xs:text-2xl"
                        >
                            Create an Account
                        </div>
                        <div class="mb-3 text-sm text-gray-500">
                            Fun awaits. Join the photo booth party! <br />
                            Capture the moment. Sign up now.
                        </div>
                        <div>
                            <InputLabel
                                for="fullname"
                                value="Fullname"
                                class="text-pink-800"
                            />

                            <TextInput
                                id="fullname"
                                type="text"
                                class="text-sm  border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
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
                            <InputLabel
                                for="email"
                                value="Email"
                                class="text-pink-800"
                            />

                            <TextInput
                                id="email"
                                type="email"
                                class="text-sm  border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
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
                            <InputLabel
                                for="password"
                                value="Password"
                                class="text-pink-800"
                            />

                            <TextInput
                                id="password"
                                type="password"
                                class="text-sm  border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
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
                                class="text-pink-800"
                                for="password_confirmation"
                                value="Confirm Password"
                            />

                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="text-sm  border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
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
                        @click="signInWithGoogle"
                    >
                        <img
                            src="/images/google_icon_logo.svg"
                            alt="google-icon"
                            width="20px"
                        />
                        <span class="text-sm ms-2">Continue with Google</span>
                    </SecondaryButton>


                        <div class="text-center mt-3 text-sm text-gray-500">
                            Already have an account?
                            <Link
                                :href="route('login')"
                                class="text-pink-800 hover:underline active:text-pink-400"
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
