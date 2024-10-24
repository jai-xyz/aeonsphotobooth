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
    const toggleButtonHamburger = document.getElementById(
        "toggleSidebarMobileHamburger"
    );
    const toggleButtonClose = document.getElementById(
        "toggleSidebarMobileClose"
    );
    const sidebar = document.getElementById("sidebar");
    const sidebarBackdrop = document.getElementById("sidebarBackdrop");

    isSidebarVisible.value = !isSidebarVisible.value;

    if (
        toggleButtonHamburger &&
        toggleButtonClose &&
        sidebar &&
        sidebarBackdrop
    ) {
        toggleButtonHamburger.style.display = isSidebarVisible.value
            ? "none"
            : "block";
        toggleButtonClose.style.display = isSidebarVisible.value
            ? "block"
            : "none";
        sidebar.classList.toggle("hidden", !isSidebarVisible.value);
        sidebarBackdrop.classList.toggle("hidden", !isSidebarVisible.value);
    }
}

function handleBackdropClick() {
    isSidebarVisible.value = false;
    const toggleButtonHamburger = document.getElementById(
        "toggleSidebarMobileHamburger"
    );
    const toggleButtonClose = document.getElementById(
        "toggleSidebarMobileClose"
    );
    const sidebar = document.getElementById("sidebar");
    const sidebarBackdrop = document.getElementById("sidebarBackdrop");

    if (
        toggleButtonHamburger &&
        toggleButtonClose &&
        sidebar &&
        sidebarBackdrop
    ) {
        toggleButtonHamburger.style.display = "block";
        toggleButtonClose.style.display = "none";
        sidebar.classList.add("hidden");
        sidebarBackdrop.classList.add("hidden");
    }
}

onMounted(() => {
    const sidebarBackdrop = document.getElementById("sidebarBackdrop");
    if (sidebarBackdrop) {
        sidebarBackdrop.addEventListener("click", handleBackdropClick);
    }
});

onBeforeUnmount(() => {
    const sidebarBackdrop = document.getElementById("sidebarBackdrop");
    if (sidebarBackdrop) {
        sidebarBackdrop.removeEventListener("click", handleBackdropClick);
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
        <div class="flex pt-16 bg-gray-50 dark:bg-gray-900">
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
                                <AdminNavLink
                                    :href="route('admin.dashboard')"
                                    :active="route().current('admin.dashboard')"
                                >
                                    <svg
                                        :class="[
                                            'w-6 h-6 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white',
                                            {
                                                'text-pink-400':
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
                                        viewBox="0 0 22 22"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"
                                        />
                                        <path
                                            d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"
                                        />
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
                                                'text-pink-400':
                                                    route().current(
                                                        'package.index'
                                                    ),
                                                'text-gray-400 dark:text-gray-400':
                                                    !route().current(
                                                        'package.index'
                                                    ),
                                            },
                                        ]"
                                        fill="currentColor"
                                        viewBox="0 0 22 22"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.024 3.783A1 1 0 0 1 6 3h12a1 1 0 0 1 .976.783L20.802 12h-4.244a1.99 1.99 0 0 0-1.824 1.205 2.978 2.978 0 0 1-5.468 0A1.991 1.991 0 0 0 7.442 12H3.198l1.826-8.217ZM3 14v5a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5h-4.43a4.978 4.978 0 0 1-9.14 0H3Zm5-7a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H9a1 1 0 0 1-1-1Zm0 2a1 1 0 0 0 0 2h8a1 1 0 1 0 0-2H8Z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    <span class="ml-3">Packages </span>
                                </AdminNavLink>

                                <AdminNavLink
                                    :href="route('event.index')"
                                    :active="route().current('event.index')"
                                >
                                    <svg
                                        :class="[
                                            'w-6 h-6 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white',
                                            {
                                                'text-pink-400':
                                                    route().current(
                                                        'event.index'
                                                    ),
                                                'text-gray-400 dark:text-gray-400':
                                                    !route().current(
                                                        'event.index'
                                                    ),
                                            },
                                        ]"
                                        fill="currentColor"
                                        viewBox="0 0 22 22"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5 5a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1 2 2 0 0 1 2 2v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a2 2 0 0 1 2-2ZM3 19v-7a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Zm6.01-6a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm2 0a1 1 0 1 1 2 0 1 1 0 0 1-2 0Zm6 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm-10 4a1 1 0 1 1 2 0 1 1 0 0 1-2 0Zm6 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm2 0a1 1 0 1 1 2 0 1 1 0 0 1-2 0Z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>

                                    <span class="ml-3">Events </span>
                                </AdminNavLink>

                                <AdminNavLink
                                    :href="route('backdrop.index')"
                                    :active="route().current('backdrop.index')"
                                >
                                    <svg
                                        :class="[
                                            'w-7 h-7 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white',
                                            {
                                                'text-pink-400':
                                                    route().current(
                                                        'backdrop.index'
                                                    ),
                                                'text-gray-400 dark:text-gray-400':
                                                    !route().current(
                                                        'backdrop.index'
                                                    ),
                                            },
                                        ]"
                                        fill="currentColor"
                                        viewBox="0 0 240 240"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M0 0 C51.48 0 102.96 0 156 0 C156 51.81 156 103.62 156 157 C104.52 157 53.04 157 0 157 C0 105.19 0 53.38 0 0 Z "
                                            transform="translate(34,60)"
                                        />
                                        <path
                                            d="M0 0 C1.9375 2 1.9375 2 3 4 C3.33 2.68 3.66 1.36 4 0 C57.46 0 110.92 0 166 0 C166.33 1.32 166.66 2.64 167 4 C167.350625 3.34 167.70125 2.68 168.0625 2 C170 0 170 0 173.5 -0.5 C177 0 177 0 178.9375 1.6875 C180.19214083 4.41818887 180.45702746 6.02932151 180 9 C179.25823407 9.66463367 179.25823407 9.66463367 178.50148296 10.34269428 C176.46557583 12.5898863 176.61683472 13.95089849 176.60127258 16.95439148 C176.58873444 17.97662811 176.57619629 18.99886475 176.5632782 20.05207825 C176.56471329 21.17273178 176.56614838 22.29338531 176.56762695 23.44799805 C176.5574353 24.62861816 176.54724365 25.80923828 176.53674316 27.02563477 C176.51170658 30.26302093 176.49988349 33.50013463 176.49388909 36.737602 C176.48451551 40.12094079 176.45831302 43.50414785 176.43388367 46.8874054 C176.39018684 53.29402789 176.36165953 59.70062487 176.33856028 66.10735112 C176.31152559 73.40119067 176.26761308 80.69489791 176.22227156 87.98864305 C176.12937978 102.99235742 176.05771989 117.99610602 176 133 C178.31 133 180.62 133 183 133 C182.91516357 133.5681543 182.83032715 134.13630859 182.74291992 134.72167969 C182.03814368 143.35798542 185.88297524 149.13361478 190.3125 156.25 C191.01182862 157.40500342 191.70911028 158.56124881 192.40429688 159.71875 C193.68631465 161.84925297 194.97792232 163.97403832 196.2824707 166.09082031 C198.00065528 169.00110992 199.5245614 171.96052311 201 175 C199.02 175.99 199.02 175.99 197 177 C189.9767809 165.86613208 183.49903397 154.4471621 177 143 C176.67 154.22 176.34 165.44 176 177 C174.35 177 172.7 177 171 177 C170.67 166.11 170.34 155.22 170 144 C168.68 146.31 167.36 148.62 166 151 C165.34 151 164.68 151 164 151 C163.97315688 148.18743719 163.95323812 145.37512759 163.9375 142.5625 C163.92912109 141.76005859 163.92074219 140.95761719 163.91210938 140.13085938 C163.9034857 138.08704779 163.9477185 136.04316098 164 134 C164.33 133.67 164.66 133.34 165 133 C166.98 133 168.96 133 171 133 C170.67 93.07 170.34 53.14 170 12 C168.02 11.01 168.02 11.01 166 10 C166 13.3 166 16.6 166 20 C112.54 20 59.08 20 4 20 C4 16.7 4 13.4 4 10 C2.02 10.66 0.04 11.32 -2 12 C-2 51.93 -2 91.86 -2 133 C0.31 133 2.62 133 5 133 C6 134 6 134 6.09765625 137.81640625 C6.09098089 139.39846682 6.07901875 140.98051159 6.0625 142.5625 C6.05798828 143.36880859 6.05347656 144.17511719 6.04882812 145.00585938 C6.03700518 147.00393756 6.01906914 149.00197783 6 151 C5.34 151 4.68 151 4 151 C0 144.25 0 144.25 0 142 C-0.66 142 -1.32 142 -2 142 C-2 153.55 -2 165.1 -2 177 C-3.32 177 -4.64 177 -6 177 C-6.33 165.78 -6.66 154.56 -7 143 C-10.9665142 150.07189268 -10.9665142 150.07189268 -14.92602539 157.14770508 C-18.73466149 163.91186684 -22.76541194 170.49433001 -27 177 C-28.32 176.34 -29.64 175.68 -31 175 C-30.25364216 173.6022158 -29.50240277 172.20703962 -28.75 170.8125 C-27.94949219 169.32363281 -27.94949219 169.32363281 -27.1328125 167.8046875 C-25.48033567 164.79913154 -23.71719293 161.87239333 -21.9296875 158.9453125 C-21.38731445 158.04063232 -20.84494141 157.13595215 -20.28613281 156.20385742 C-19.19618735 154.38872337 -18.08922493 152.58367739 -16.96386719 150.7902832 C-13.32924804 144.67704617 -12.2297496 140.09105827 -13 133 C-10.69 133 -8.38 133 -6 133 C-6.06049303 117.27524648 -6.13796327 101.55067459 -6.23571491 85.8261137 C-6.28070447 78.52512237 -6.32011172 71.22418016 -6.34643555 63.9230957 C-6.36939018 57.56038884 -6.40275951 51.19783702 -6.44870156 44.83525306 C-6.47266833 41.46544414 -6.49137431 38.09578909 -6.49761391 34.72589684 C-6.50719094 30.96636283 -6.53517413 27.20739422 -6.56762695 23.44799805 C-6.56619186 22.32734451 -6.56475677 21.20669098 -6.5632782 20.05207825 C-6.58208542 18.5187233 -6.58208542 18.5187233 -6.60127258 16.95439148 C-6.60588155 16.06486125 -6.61049051 15.17533102 -6.61523914 14.25884533 C-7.08024929 11.52887431 -8.00786318 10.83172211 -10 9 C-10.38976435 5.88188517 -10.50422245 3.88980433 -8.9375 1.125 C-5.85928089 -0.66235303 -3.47093932 -0.43386742 0 0 Z "
                                            transform="translate(26,39)"
                                        />
                                    </svg>
                                    <span class="ml-2">Backdrops</span>
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
                class="relative w-full h-full overflow-y-auto bg-gray-50 lg:ml-64 dark:bg-gray-900"
            >
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
