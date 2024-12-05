<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";
import AButton from "@/Components/AButton.vue";
import AButtonSecondary from "@/Components/AButtonSecondary.vue";

const showingNavigationDropdown = ref(false);

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    auth: Object,
});
</script>

<template>
    <nav class="sticky top-0 bg-white border-b border-gray-100">
        <!-- Primary Navigation Menu -->
        <div class="max-w-full mx-auto px-2 sm:px-4 lg:px-6">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <Link :href="route('home')">
                        <ApplicationLogo
                            class="block h-9 w-auto fill-current text-gray-800"
                        />
                    </Link>
                </div>

                <!-- Navigation Links -->
                <div
                    class="hidden lg:flex flex-1 ps-4 justify-left space-x-2 m-auto align-center lg:space-x-4"
                >
                    <NavLink
                        :href="route('home')"
                        :active="route().current('home')"
                    >
                        Home
                    </NavLink>
                    <NavLink
                        :href="route('user.package.index')"
                        :active="route().current('user.package.index')"
                    >
                        Packages
                    </NavLink>
                    <NavLink
                        :href="route('user.event.index')"
                        :active="route().current('user.event.index')"
                    >
                        FAQs
                    </NavLink>
                    <NavLink
                        :href="route('user.event.index')"
                        :active="route().current('user.event.index')"
                    >
                        Contact Us
                    </NavLink>
                </div>

                <!-- Navigation Buttons -->
                <div class="hidden lg:flex align-center m-auto">
                    <template v-if="auth.user">
                        <div class="flex align-center m-auto">
                            <Link
                                :href="route('user.event.create')"
                                class="text-primary-700 font-medium flex justify-center align-center m-auto gap-1"
                            >
                                Book Now
                                <svg
                                    fill="#f28ab0"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 448 512"
                                    width="5"
                                    height="5"
                                    class="w-6 h-6 m-auto"
                                >
                                    <path
                                        d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 144L48 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l144 0 0 144c0 17.7 14.3 32 32 32s32-14.3 32-32l0-144 144 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-144 0 0-144z"
                                    />
                                </svg>
                            </Link>
                        </div>
                        <div
                            class="border-l border-gray-300 mx-2 my-auto h-8"
                        ></div>

                        <div class="me-2 flex align-center m-auto">
                            <AButton :href="route('user.event.index')">
                                Booked Events
                            </AButton>
                        </div>

                        <!--  <AButtonSecondary
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="text-gray-800 bg-white"
                        >
                            Log Out
                        </AButtonSecondary> -->
                        <Dropdown align="right" width="56">
                            <template #trigger>
                                <span class="inline-flex rounded-md">
                                    <button
                                        type="button"
                                        class="inline-flex items-center py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                            class="-me-0.5 h-6 w-6"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </button>
                                </span>
                            </template>

                            <template #content>
                                <div
                                    class="bg-white divide-y divide-gray-100 dark:bg-gray-700 dark:divide-gray-600"
                                >
                                    <div>
                                        <div class="pointer-events-none ms-4">
                                            <div
                                                class="font-medium text-base text-gray-800"
                                            >
                                                {{
                                                    $page.props.auth.user
                                                        .fullname
                                                }}
                                            </div>
                                            <div
                                                class="font-medium text-sm text-gray-500"
                                            >
                                                {{
                                                    $page.props.auth.user.email
                                                }}
                                            </div>
                                        </div>
                                        <div class="pt-2">
                                            <DropdownLink
                                                :href="
                                                    route('user.profile.edit')
                                                "
                                                method="get"
                                                as="button"
                                            >
                                                Profile
                                            </DropdownLink>
                                        </div>
                                    </div>
                                    <div>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </div>
                                </div>
                            </template>
                        </Dropdown>
                    </template>

                    <template v-else>
                        <div class="me-2">
                            <AButton :href="route('login')"> Login </AButton>
                        </div>

                        <AButtonSecondary
                            :href="route('register')"
                            class="text-gray-800 bg-white"
                        >
                            Register
                        </AButtonSecondary>
                    </template>
                </div>

                <!-- Hamburger -->
                <div class="-me-2 flex items-center lg:hidden">
                    <button
                        @click="
                            showingNavigationDropdown =
                                !showingNavigationDropdown
                        "
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                    >
                        <svg
                            class="h-6 w-6"
                            stroke="currentColor"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <path
                                :class="{
                                    hidden: showingNavigationDropdown,
                                    'inline-flex': !showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                :class="{
                                    hidden: !showingNavigationDropdown,
                                    'inline-flex': showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div
            :class="{
                block: showingNavigationDropdown,
                hidden: !showingNavigationDropdown,
            }"
            class="lg:hidden"
        >
            <div class="pt-2 pb-3 space-y-1">
                <ResponsiveNavLink
                    :href="route('home')"
                    :active="route().current('home')"
                >
                    Home
                </ResponsiveNavLink>

                <ResponsiveNavLink
                    :href="route('user.package.index')"
                    :active="route().current('user.package.index')"
                >
                    Packages
                </ResponsiveNavLink>

                <ResponsiveNavLink
                    :href="route('dashboard')"
                    :active="route().current('dashboard')"
                >
                    Contact Us
                </ResponsiveNavLink>
            </div>

            <!-- Responsive Settings Options -->
            <div class="pb-1 border-t border-gray-200">
                <div class="pt-2 px-4">
                    <div class="font-medium text-base text-gray-800">
                        {{ $page.props.auth.user.fullname }}
                    </div>
                    <div class="font-medium text-sm text-gray-500">
                        {{ $page.props.auth.user.email }}
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <ResponsiveNavLink
                        :href="route('user.profile.edit')"
                        :active="route().current('user.profile.edit')"
                    >
                        Profile
                    </ResponsiveNavLink>
                    <ResponsiveNavLink
                        :href="route('logout')"
                        method="post"
                        as="button"
                    >
                        Log Out
                    </ResponsiveNavLink>
                </div>
            </div>
        </div>
    </nav>
</template>
