<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, Link } from "@inertiajs/vue3";
import "../../../css/custom-styles.css";

// Define the props for the component
const props = defineProps({
    auth: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Registration" />

    <AuthenticatedLayout :auth="auth">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Event Registration
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg pb-12"
                >
                    <div class="px-14 pt-6">
                        <div class="flex justify-center align-center">
                            <div class="container">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="svg-success"
                                    viewBox="0 0 24 24"
                                >
                                    <g
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-miterlimit="10"
                                    >
                                        <circle
                                            class="success-circle-outline"
                                            cx="12"
                                            cy="12"
                                            r="11.5"
                                        />
                                        <circle
                                            class="success-circle-fill"
                                            cx="12"
                                            cy="12"
                                            r="11.5"
                                        />
                                        <polyline
                                            class="success-tick"
                                            points="17,8.5 9.5,15.5 7,13"
                                        />
                                    </g>
                                </svg>
                            </div>
                        </div>

                        <h2 class="text-center">
                            Thank you for completing your registration!
                        </h2>
                        <p class="text-center text-sm">
                       You will receive an email notification once your event has been reviewed, 
                        </p>    <p class="text-center">
                letting you know if it has been accepted or declined.
                        </p>


                        <p class="text-center text-sm">
                            <strong>
                                <p>If your event is accepted:</p>
                                Important: A down payment of ₱500 is required to
                                secure your event booking. <br />
                                Please check your email for payment instructions and confirmation updates once the payment is processed.</strong
                            >
                        </p>
                       
                    </div>
                     <div
                            class="flex justify-center align-center gap-4 mt-6"
                        >
                            <Link
                                :href="route('user.event.index')"
                                class="text-pink-800 hover:underline active:text-pink-400"
                            >
                            Event List
                            </Link>
                            |
                            <Link
                                :href="route('home')"
                                class="text-pink-800 hover:underline active:text-pink-400"
                            >
                                 Home
                            </Link>
                        </div>
                </div>
                
            </div>
        </div>
    </AuthenticatedLayout>
</template>
