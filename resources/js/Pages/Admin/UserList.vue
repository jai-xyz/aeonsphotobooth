<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import AdminAuthenticatedLayout from "@/Layouts/AdminAuthenticatedLayout.vue";
import AButton from "@/Components/AButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import TextInput from "@/Components/TextInput.vue";
import Toast from "@/Components/Toast.vue";
import "../../../css/custom-styles.css";
import { Inertia } from "@inertiajs/inertia";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import DangerButton from "@/Components/DangerButton.vue";

const props = defineProps({
    users: {
        type: [Array, Object],
        required: true,
    },
});

/* #############################
            PAGINATION
   ############################# */
const paginationusers = ref(props.users.data);
const currentPage = ref(1);

const pagination = ref({
    total: props.users.total,
    per_page: props.users.per_page,
    current_page: props.users.current_page,
    last_page: props.users.last_page,
    from: props.users.from,
    to: props.users.to,
});

const changePage = (page) => {
    Inertia.get(
        route("user.index", { page }),
        {},
        {
            preserveScroll: true,
            preserveState: true,
        }
    );
};

const activePage = computed(() => {
    const activePageItem = props.users.links.find(
        (link) => link.active
    );
    return activePageItem
        ? new URL(activePageItem.url).searchParams.get("page")
        : 1;
});

watch(
    () => props.users,
    (newusers) => {
        paginationusers.value = newusers.data;
        pagination.value = {
            total: newusers.total,
            per_page: newusers.per_page,
            current_page: newusers.current_page,
            last_page: newusers.last_page,
            from: newusers.from,
            to: newusers.to,
        };
        currentPage.value = activePage.value;
    }
);

</script>

<template>
    <Head title="Users" />

    <AdminAuthenticatedLayout>
        <Toast />
        <div
            class="px-4 pt-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700"
        >
            <div class="w-full">
                <div class="mb-4">
                    <nav class="flex mb-5">
                        <ol
                            class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2"
                        >
                            <li class="inline-flex items-center">
                                <a
                                    :href="route('admin.dashboard')"
                                    class="inline-flex items-center text-gray-500 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white"
                                >
                                    <svg
                                        class="w-5 h-5 mr-2.5"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                                        ></path>
                                    </svg>
                                    Home
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg
                                        class="w-6 h-6 text-gray-400"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                    <span
                                        class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500"
                                        >Users</span
                                    >
                                </div>
                            </li>
                            
                        </ol>
                    </nav>
                    <div class="items-center justify-between block sm:flex">
                        <div
                            class="flex flex-wrap items-center mb-4 sm:mb-0 w-full sm:w-auto"
                        >
                            <div class="relative w-full">
                                <div class="flex flex-wrap items-center">
                                    <h1
                                        class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white"
                                    >
                                        List of Users
                                    </h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col">
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden shadow">
                        <table
                            class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600"
                        >
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th
                                        scope="col"
                                        class="text-center p-4 text-xs font-extrabold text-gray-700 uppercase dark:text-gray-400"
                                    >
                                        Full name
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center p-4 text-xs font-extrabold text-gray-700 uppercase dark:text-gray-400"
                                    >
                                        Email
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                            >
                                <tr
                                    v-for="user in users.data"
                                    :key="user.id"
                                    class="hover:bg-gray-100 dark:hover:bg-gray-700"
                                >
                                    <td
                                        class="text-center p-4 text-base font-normal text-gray-900 whitespace-nowrap dark:text-white"
                                    >
                                        {{ user.fullname }}
                                    </td>

                                    <td
                                        class="text-center p-4 space-x-2 whitespace-nowrap"
                                    >
                                        {{ user.email }}
                                        
                                    </td>
                                </tr>
                                <tr v-if="users.data.length === 0">
                                    <td
                                        colspan="12"
                                        class="p-4 text-center text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400"
                                    >
                                        No records found
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700"
        >
            <div class="flex items-center mb-4 sm:mb-0">
                <span
                    class="text-sm font-normal text-gray-500 dark:text-gray-400"
                    >Showing
                    <span class="font-semibold text-gray-700 dark:text-white"
                        >{{ pagination.from ? pagination.from : 0 }} to
                        {{ pagination.to ? pagination.to : 0 }}</span
                    >
                    of
                    <span class="font-semibold text-gray-700 dark:text-white">
                        {{ pagination.total }}
                    </span>
                    results</span
                >
            </div>
            <div class="flex items-center space-x-3">
                <nav>
                    <ul class="flex items-center -space-x-px h-10 text-base">
                        <li>
                            <button
                                @click="changePage(pagination.current_page - 1)"
                                :disabled="pagination.current_page === 1"
                                :class="[
                                    'flex items-center justify-center px-4 h-10 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg',
                                    pagination.current_page === 1
                                        ? 'cursor-default opacity-50'
                                        : 'hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-700 dark:hover:text-white',
                                ]"
                            >
                                <svg
                                    class="w-3 h-3 rtl:rotate-180"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 6 10"
                                >
                                    <path
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 1 1 5l4 4"
                                    />
                                </svg>
                            </button>
                        </li>
                        <li v-for="page in pagination.last_page" :key="page">
                            <button
                                @click="changePage(page)"
                                :class="[
                                    'flex items-center justify-center px-4 h-10 leading-tight text-gray-500 border border-gray-300  dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400',
                                    page === pagination.current_page
                                        ? 'bg-pink-400 text-white border-pink-400'
                                        : 'hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-700 dark:hover:text-white',
                                ]"
                            >
                                {{ page }}
                            </button>
                        </li>
                        <li>
                            <button
                                @click="changePage(pagination.current_page + 1)"
                                :disabled="
                                    pagination.current_page ===
                                    pagination.last_page
                                "
                                :class="[
                                    'flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg ',
                                    pagination.current_page ===
                                    pagination.last_page
                                        ? 'cursor-default opacity-50'
                                        : 'hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-700 dark:hover:text-white',
                                ]"
                            >
                                <svg
                                    class="w-3 h-3 rtl:rotate-180"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 6 10"
                                >
                                    <path
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="m1 9 4-4-4-4"
                                    />
                                </svg>
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

      
    </AdminAuthenticatedLayout>
</template>
