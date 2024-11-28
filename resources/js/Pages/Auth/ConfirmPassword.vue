<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm } from "@inertiajs/vue3";
import "../../../css/custom-styles.css";

const form = useForm({
    password: "",
});

const submit = () => {
    form.post(route("password.confirm"), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirm Password" />
        <div
            class="relative min-h-screen flex flex-col justify-center items-center"
        >
            <img
                id="background"
                class="absolute inset-0 w-full h-full object-cover"
                src="/images/backgroundbg.png"
                alt=""
            />
            <div
                class="relative w-full max-w-[500px] xs-sm:max-w-[400px] xxs-xs:max-w-[310px] py-4 px-6 xxs:py-8 xs:px-12 background-blur shadow-sm overflow-hidden rounded-2xl"
            >
                <div class="mb-1 text-pink-900 font-bold text-xl xs:text-2xl">
                    Confirm Password
                </div>
                <div class="mb-3 text-sm text-gray-500">
                    This is a secure area of the application. Please confirm
                    your password before continuing.
                </div>

                <form @submit.prevent="submit">
                    <div>
                        <InputLabel
                            for="password"
                            value="Password"
                            class="text-pink-800"
                        />
                        <TextInput
                            id="password"
                            type="password"
                            class="text-sm bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                            autofocus
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.password"
                        />
                    </div>

                    <div class="flex justify-end mt-4">
                        <PrimaryButton
                            class="ms-4"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Confirm
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>
