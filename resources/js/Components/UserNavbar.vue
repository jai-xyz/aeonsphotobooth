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
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
                            class="hidden md:flex flex-1 justify-center space-x-8"
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
                        <div class="hidden md:flex align-center m-auto">
                            <template v-if="auth.user">
                                           <div class="me-2">
                                <AButton :href="route('user.profile.edit')">
                                    Profile
                                </AButton>
                                </div>

                                <AButtonSecondary
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    class="text-gray-800 bg-white"
                                >
                                    Log Out
                                </AButtonSecondary>
                            </template>

                            <template v-else>
                                <div class="me-2">
                                <AButton :href="route('login')">
                                    Login
                                </AButton>
                                </div>

                                <AButtonSecondary
                                    :href="route('register')"
                                    class="text-gray-800 bg-white "
                                >
                                    Register
                                </AButtonSecondary>
                            </template>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center md:hidden">
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
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
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
                    class="md:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            :href="route('user.event.index')"
                            :active="route().current('user.event.index')"
                        >
                            Event
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">
                                <!-- {{ $page.props.auth.user.fullname }} -->
                            </div>
                            <div class="font-medium text-sm text-gray-500">
                                <!-- {{ $page.props.auth.user.email }} -->
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