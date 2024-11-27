<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import loginBg from "/public/images/loginbg.png";
import "../../../css/custom-styles.css";
import AButtonSecondary from "@/Components/AButtonSecondary.vue";

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />
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
                class="relative w-full max-w-[310px] xs:max-w-md sm:max-w-md md:max-w-md lg:max-w-md mt-6 p-6 background-blur shadow-sm overflow-hidden rounded-2xl mx-4"
            >
        <div class="mb-4 text-xs text-gray-600 ">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link
            we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </div>

        <div class="mb-4 font-medium text-sm text-green-600" v-if="verificationLinkSent">
            A new verification link has been sent to the email address you provided during registration.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 block xs:flex items-center justify-between">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Resend Verification Email
                </PrimaryButton>

                <AButtonSecondary
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 mt-4 xs:mt-0 xs:mx-auto"
                    >Log Out</AButtonSecondary
                >
            </div>
        </form>

            </div>
            </div>
    </GuestLayout>
</template>
