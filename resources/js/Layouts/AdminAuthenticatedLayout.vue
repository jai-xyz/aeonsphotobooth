<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import AdminNavLink from "@/Components/AdminNavLink.vue";
import { Link } from "@inertiajs/vue3";

const isSidebarVisible = ref(false);
const showingNavigationDropdown = ref(false);
const isDropdownVisible = ref(false);

function toggleDropdown() {
    isDropdownVisible.value = !isDropdownVisible.value;
}

function toggleSidebar() {
    const toggleButtonHamburger = document.getElementById('toggleSidebarMobileHamburger');
    const toggleButtonClose = document.getElementById('toggleSidebarMobileClose');
    const sidebar = document.getElementById('sidebar');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');

    isSidebarVisible.value = !isSidebarVisible.value;

    if (toggleButtonHamburger && toggleButtonClose && sidebar && sidebarBackdrop) {
        toggleButtonHamburger.style.display = isSidebarVisible.value ? 'none' : 'block';
        toggleButtonClose.style.display = isSidebarVisible.value ? 'block' : 'none';
        sidebar.classList.toggle('hidden', !isSidebarVisible.value);
        sidebarBackdrop.classList.toggle('hidden', !isSidebarVisible.value);
    }
}

function handleBackdropClick() {
    isSidebarVisible.value = false;
    const toggleButtonHamburger = document.getElementById('toggleSidebarMobileHamburger');
    const toggleButtonClose = document.getElementById('toggleSidebarMobileClose');
    const sidebar = document.getElementById('sidebar');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');

    if (toggleButtonHamburger && toggleButtonClose && sidebar && sidebarBackdrop) {
        toggleButtonHamburger.style.display = 'block';
        toggleButtonClose.style.display = 'none';
        sidebar.classList.add('hidden');
        sidebarBackdrop.classList.add('hidden');
    }
}

onMounted(() => {
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    if (sidebarBackdrop) {
        sidebarBackdrop.addEventListener('click', handleBackdropClick);
    }
});

onBeforeUnmount(() => {
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    if (sidebarBackdrop) {
        sidebarBackdrop.removeEventListener('click', handleBackdropClick);
    }
});
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <div :class="['flex flex-col flex-1 transition-all duration-300']">
            <nav
                class="fixed z-30 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700"
            >
                <div class="mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <div class="flex items-center">
                            <button
                                id="toggleSidebarMobile"
                                class="p-2 text-gray-600 rounded cursor-pointer lg:hidden hover:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 dark:focus:bg-gray-700 focus:ring-2 focus:ring-gray-100 dark:focus:ring-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                                @click="toggleSidebar"
                            >
                                <svg
                                    v-show="!isSidebarVisible"
                                    id="toggleSidebarMobileHamburger"
                                    class="w-6 h-6"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                        clip-rule="evenodd"
                                    ></path>
                                </svg>
                                <svg
                                    v-show="isSidebarVisible"
                                    id="toggleSidebarMobileClose"
                                    class="w-6 h-6"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    ></path>
                                </svg>
                            </button>
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('admin.dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <!-- Settings Dropdown -->
                            <!-- TODO: ADD PLACE HOLDER FOR IMAGE -->
                            <div class="ms-3 relative flex items-center">
                                <img
                                    class="w-10 h-10 rounded-full"
                                    src="/uploads/backdrop/custom/checkered_black_and_white.jpg"
                                    alt="Rounded avatar"
                                />
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                {{
                                                    $page.props.auth.user
                                                        .usertype
                                                }}
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                    class="ms-2 -me-0.5 h-6 w-6"
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
                                            <div class="py-2">
                                                <div
                                                    class="pointer-events-none ms-4"
                                                >
                                                    {{
                                                        $page.props.auth.user.fullname
                                                            .charAt(0)
                                                            .toUpperCase() +
                                                        $page.props.auth.user.fullname
                                                            .slice(1)
                                                            .toLowerCase()
                                                    }}
                                                </div>
                                                <div
                                                    class="pointer-events-none ms-4 text-xs italic text-gray-400"
                                                >
                                                    Administrator
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
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
        <div class="flex pt-16 overflow-hidden bg-gray-50 dark:bg-gray-900">
            <!-- SIDEBAR -->

            <aside
                id="sidebar"
                :class="[
                    'fixed top-0 left-0 z-20 flex flex-col flex-shrink-0 w-64 h-full pt-16 font-normal duration-75 lg:flex transition-width',
                    { 'hidden lg:block': !isSidebarVisible },
                ]"
            >
                <div
                    class="relative flex flex-col flex-1 min-h-0 pt-0 bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700"
                >
                    <div class="flex flex-col flex-1 pt-5 pb-4 overflow-y-auto">
                        <div
                            class="flex-1 px-3 space-y-1 bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                        >
                            <ul class="pb-2 space-y-2">
                                <!-- <li>
                                    <a
                                        :href="route('dashboard')"
                                        class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 group dark:text-gray-200 dark:hover:bg-gray-700"
                                    >
                                        <svg
                                            class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"
                                            ></path>
                                            <path
                                                d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"
                                            ></path>
                                        </svg>
                                        <span class="ml-3">Dashboard</span>
                                    </a>
                                </li> -->

                                <AdminNavLink
                                    :href="route('admin.dashboard')"
                                    :active="route().current('admin.dashboard')"
                                >
                                    <svg
                                        :class="[
                                            'w-6 h-6 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white',
                                            {
                                                'text-pink-500':
                                                    route().current(
                                                        'admin.dashboard'
                                                    ),
                                                'text-gray-400 dark:text-gray-400':
                                                    !route().current(
                                                        'admin.dashboard'
                                                    ),
                                            },
                                        ]"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"
                                        ></path>
                                        <path
                                            d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"
                                        ></path>
                                    </svg>
                                    <span class="ml-3">Dashboard</span>
                                </AdminNavLink>

                                <AdminNavLink
                                    :href="route('package.index')"
                                    :active="route().current('package.index')"
                                >
                                        <svg
                                            :class="[
                                            'w-6 h-6 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white',
                                            {
                                                'text-pink-500':
                                                    route().current(
                                                        'package.index'
                                                    ),
                                                'text-gray-400 dark:text-gray-400':
                                                    !route().current(
                                                        'package.index'
                                                    ),
                                            },
                                        ]"  fill="currentColor"
                                            viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"
                                            ></path>
                                            <path
                                                d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"
                                            ></path>
                                        </svg>
                                        <span class="ml-3">Packages </span>
                                </AdminNavLink>

                                <li>
                                    <button
                                        type="button"
                                        class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                                        @click="toggleDropdown"
                                    >
                                        <svg
                                            class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"
                                            aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="currentColor"
                                            viewBox="0 0 18 21"
                                        >
                                            <path
                                                d="M15 12a1 1 0 0 0 .962-.726l2-7A1 1 0 0 0 17 3H3.77L3.175.745A1 1 0 0 0 2.208 0H1a1 1 0 0 0 0 2h.438l.6 2.255v.019l2 7 .746 2.986A3 3 0 1 0 9 17a2.966 2.966 0 0 0-.184-1h2.368c-.118.32-.18.659-.184 1a3 3 0 1 0 3-3H6.78l-.5-2H15Z"
                                            />
                                        </svg>
                                        <span
                                            class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap"
                                            >E-commerce</span
                                        >
                                        <svg
                                            class="w-3 h-3"
                                            :class="{
                                                'rotate-180': isDropdownVisible,
                                            }"
                                            aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 10 6"
                                        >
                                            <path
                                                stroke="gray"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="m1 1 4 4 4-4"
                                            />
                                        </svg>
                                    </button>
                                    <ul
                                        id="dropdown-example"
                                        :class="{
                                            hidden: !isDropdownVisible,
                                            'py-2 space-y-2': true,
                                        }"
                                    >
                                        <li>
                                            <a
                                                href="#"
                                                class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                                                >Products</a
                                            >
                                        </li>
                                        <li>
                                            <a
                                                href="#"
                                                class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                                                >Billing</a
                                            >
                                        </li>
                                        <li>
                                            <a
                                                href="#"
                                                class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                                                >Invoice</a
                                            >
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </aside>

            <div
                class="fixed inset-0 z-10 flex lg:hidden bg-gray-600/50 dark:bg-gray-900/90"
                id="sidebarBackdrop"
                :class="{ hidden: !isSidebarVisible }"
            ></div>

            <!-- Primary Navigation Menu -->

            <div
                id="main-content"
                class="relative w-full h-full p-4 overflow-y-auto bg-gray-50 lg:ml-64 dark:bg-gray-900"
            >
                <div v-if="$slots.breadcrumbs">
                    <div>
                        <slot name="breadcrumbs" />
                    </div>
                </div>
                <!-- Page Heading -->

                <header v-if="$slots.header">
                    <div class="mx-auto">
                        <slot name="header" />
                    </div>
                </header>

                <!-- Page Content -->

                <main>
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
