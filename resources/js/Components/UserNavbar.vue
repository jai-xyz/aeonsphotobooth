<script setup>
import { ref, computed, watch, onMounted } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link, usePage } from "@inertiajs/vue3";
import AButton from "@/Components/AButton.vue";
import AButtonSecondary from "@/Components/AButtonSecondary.vue";
import { useRouter } from "vue-router";

const route = window.route;

const showingNavigationDropdown = ref(false);

const activeSection = ref("");

const sections = ["home", "packages", "contact-us"];

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    auth: Object,
});

const styles = computed(() => {
    return (section) =>
        activeSection.value === section
            ? {
                  display: "inline-flex",
                  alignItems: "center",
                  padding: "0.5rem 1rem",
                  borderRadius: "0.375rem",
                  backgroundColor: "#f9e7ed",
                  color: "#ec4899",
                  fontSize: "1rem",
                  fontWeight: "500",
                  lineHeight: "1.25rem",
                  textAlign: "center",
                  outline: "none",
                  borderColor: "#be185d",
                  transition: "all 0.15s ease-in-out",
              }
            : {
                  display: "inline-flex",
                  alignItems: "center",
                  padding: "0.5rem 1rem",
                  borderRadius: "0.375rem",
                  fontSize: "1rem",
                  fontWeight: "500",
                  lineHeight: "1.25rem",
                  color: "#6b7280",
                  borderColor: "#d1d5db",
                  outline: "none",
                  transition: "all 0.15s ease-in-out",
              };
});

const { component } = usePage();
const router = useRouter();

const isHomeRoute = computed(() => {
    return component === "Welcome";
});

onMounted(() => {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    activeSection.value = entry.target.id;
                }
            });
        },
        { threshold: 0.5 }
    );

    sections.forEach((section) => {
        const element = document.getElementById(section);
        if (element) {
            observer.observe(element);
        }
    });
});
</script>

<template>
    <nav class="sticky top-0 z-50 bg-white border-b border-gray-100">
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
                    v-if="isHomeRoute"
                    class="hidden lg:flex flex-1 ps-4 space-x-2 m-auto align-center lg:space-x-4"
                >
                    <a :style="styles('home')" href="#home" v-smooth-scroll class="hover:bg-[#e9eaeb]">Home</a>
                    <a :style="styles('packages')" href="#packages" v-smooth-scroll class="hover:bg-[#e9eaeb]">Packages</a>
                    <a :style="styles('contact-us')" href="#contact-us" v-smooth-scroll class="hover:bg-[#e9eaeb]">Contact Us</a>
                </div>

                <div
                    v-else
                    class="hidden lg:flex flex-1 ps-4 space-x-2 m-auto align-center lg:space-x-4"
                >
                    <NavLink :style="styles('home')" href="/#home" class="hover:bg-[#e9eaeb]">Home</NavLink>
                    <NavLink :style="styles('packages')" href="/#packages" class="hover:bg-[#e9eaeb]">Packages</NavLink>
                    <NavLink :style="styles('contact-us')" href="/#contact-us" class="hover:bg-[#e9eaeb]">Contact us</NavLink>
                </div>

                <div
                    v-if="auth.user"
                    class="hidden sm:flex align-center ms-auto"
                >
                    <Link
                        :href="route('user.event.create')"
                        class="text-primary-700 font-medium flex justify-center align-center m-auto gap-1"
                    >
                        <div class="flex gap-x-1 justify-center items-center">
                            <span class="uppercase"> Book Now</span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                class="w-5 h-5"
                                fill="currentColor"
                            >
                                <path
                                    d="M19 4h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7v-2H5V9h14v4h2V6a2 2 0 0 0-2-2zM5 7V6h14v1H5zm16 11v-3h-2v3h-3v2h3v3h2v-3h3v-2h-3z"
                                />
                            </svg>
                        </div>
                    </Link>
                    <div
                        class="border-l border-gray-300 mx-2 my-auto h-7"
                    ></div>

                    <div class="me-2 flex align-center m-auto">
                        <AButton :href="route('user.event.index')">
                            Booked Events
                        </AButton>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="hidden lg:flex align-center m-auto">
                    <template v-if="auth.user">
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
                                        <div
                                            class="pointer-events-none ms-4 mx-auto"
                                        >
                                            <div
                                                class="font-medium text-base text-gray-800"
                                            >
                                                {{
                                                    $page.props.auth.user
                                                        .fullname
                                                }}
                                            </div>
                                            <div
                                                class="font-medium text-xs text-gray-500"
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

                <!-- <ResponsiveNavLink
                    :href="route('user.package.index')"
                    :active="route().current('user.package.index')"
                >
                    Packages
                </ResponsiveNavLink> -->

                <!-- <ResponsiveNavLink
                    :href="route('dashboard')"
                    :active="route().current('dashboard')"
                >
                    Contact Us
                </ResponsiveNavLink> -->

                <ResponsiveNavLink
                    :href="route('user.event.create')"
                    :active="route().current('user.event.create')"
                >
                    Book Now
                </ResponsiveNavLink>

                <ResponsiveNavLink
                    :href="route('user.event.index')"
                    :active="route().current('user.event.index')"
                >
                    Booked Events
                </ResponsiveNavLink>
            </div>

            <!-- Responsive Settings Options -->

            <div v-if="auth.user" class="pb-1 border-t border-gray-200">
                <div class="pt-2 px-4">
                    <div class="font-medium text-base text-gray-800">
                        {{ auth ? $props.auth.user.fullname : auth }}
                    </div>
                    <div class="font-medium text-sm text-gray-500">
                        {{ auth ? $props.auth.user.email : auth }}
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

            <div v-else class="pb-1 border-t border-gray-200">
                <div class="mt-3 space-y-1">
                    <ResponsiveNavLink
                        :href="route('register')"
                        :active="route().current('register')"
                    >
                        Register
                    </ResponsiveNavLink>
                    <ResponsiveNavLink
                        :href="route('login')"
                        method="get"
                        as="button"
                    >
                        Login
                    </ResponsiveNavLink>
                </div>
            </div>
        </div>
    </nav>
</template>
