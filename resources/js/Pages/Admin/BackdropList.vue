<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import AdminAuthenticatedLayout from "@/Layouts/AdminAuthenticatedLayout.vue";
import AButton from "@/Components/AButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { computed, ref, watch } from "vue";
import { Inertia } from "@inertiajs/inertia";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
    backdrops: {
        type: [Array, Object],
        required: true,
    },
    backdroptypes: {
        type: [Array, Object],
        required: true,
    },
});

// TODO: SHOWING PAGINATION FROM, TO AND RESULTS

const paginationbackdrops = ref(props.backdrops.data);
const currentPage = ref(1);

const pagination = ref({
    total: props.backdrops.total,
    per_page: props.backdrops.per_page,
    current_page: props.backdrops.current_page,
    last_page: props.backdrops.last_page,
    from: props.backdrops.from,
    to: props.backdrops.to,
});

const changePage = (page) => {
    Inertia.get(
        route("backdrop.index", { page }),
        {},
        {
            preserveScroll: true,
            preserveState: true,
        }
    );
};

const activePage = computed(() => {
    const activePageItem = props.backdrops.links.find((link) => link.active);
    return activePageItem
        ? new URL(activePageItem.url).searchParams.get("page")
        : 1;
});

watch(
    () => props.backdrops,
    (newBackdrops) => {
        paginationbackdrops.value = newBackdrops.data;
        pagination.value = {
            total: newBackdrops.total,
            per_page: newBackdrops.per_page,
            current_page: newBackdrops.current_page,
            last_page: newBackdrops.last_page,
            from: newBackdrops.from,
            to: newBackdrops.to,
        };
        currentPage.value = activePage.value;
    }
);

// FILTER & COUNT
const backdrops = ref(props.backdrops);

const allCount = computed(() => props.backdrops.data.length);
const plainCount = computed(
    () =>
        props.backdrops.data.filter(
            (backdrop) => backdrop.backdroptype.toLowerCase() === "plain"
        ).length
);
const sequinsCount = computed(
    () =>
        props.backdrops.data.filter(
            (backdrop) => backdrop.backdroptype.toLowerCase() === "sequins"
        ).length
);
const customCount = computed(
    () =>
        props.backdrops.data.filter(
            (backdrop) => backdrop.backdroptype.toLowerCase() === "custom"
        ).length
);

const currentFilter = ref("all");

const filterTable = (type) => {
    currentFilter.value = type;
};

const filteredBackdrops = computed(() => {
    if (currentFilter.value === "plain") {
        return props.backdrops.data.filter(
            (backdrop) => backdrop.backdroptype.toLowerCase() === "plain"
        );
    } else if (currentFilter.value === "sequins") {
        return props.backdrops.data.filter(
            (backdrop) => backdrop.backdroptype.toLowerCase() === "sequins"
        );
    } else if (currentFilter.value === "custom") {
        return props.backdrops.data.filter(
            (backdrop) => backdrop.backdroptype.toLowerCase() === "custom"
        );
    }
    return props.backdrops.data;
});

const filteredCount = computed(() => filteredBackdrops.value.length);

const displayCount = computed(() => {
    if (currentFilter.value === "plain") {
        return plainCount.value === 0 ? 0 : plainCount.value;
    } else if (currentFilter.value === "sequins") {
        return sequinsCount.value === 0 ? 0 : sequinsCount.value;
    } else if (currentFilter.value === "custom") {
        return customCount.value === 0 ? 0 : customCount.value;
    } else {
        return allCount.value === 0 ? 0 : allCount.value;
    }
});

// ADD BACKDROP & MODALS
const addBackdropForm = useForm({
    backdroptype_id: "",
    color: "",
    image: null,
});

const handleFileUpload = (event) => {
    addBackdropForm.image = event.target.files[0];
};

const addingBackdrop = ref(false);

const addNewBackdrop = (type) => {
    addBackdropForm.backdroptype_id = type.backdroptype_id;
    addBackdropForm.color = type.color;
    addBackdropForm.image = type.image;
    addingBackdrop.value = true;
};

const closeAddBackdropModal = () => {
    addingBackdrop.value = false;
    addBackdropForm.reset();
};

const submitAddBackdrop = () => {
    addBackdropForm.post(route("backdrop.store", { page: currentPage.value }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => closeAddBackdropModal(),
        onFinish: () => addBackdropForm.reset(),
    });
};

// EDIT BACKDROP & MODALS
const editingBackdrop = ref(false);
// const currentBackdrop = ref(null);

const editBackdropForm = useForm({
    forceFormData: true,
    id: null,
    backdroptype_id: "",
    color: "",
    image: null,
});

const handleFileUploadEdit = (event) => {
    editBackdropForm.image = event.target.files[0];
};

const openEditModal = (backdrop) => {
    console.log("Backdrop object received:", backdrop);

    if (!backdrop || !backdrop.id || !backdrop.image) {
        console.error("Backdrop object is missing required properties.");
        return;
    }

    // currentBackdrop.value = backdrop;
    editBackdropForm.id = backdrop.id;
    editBackdropForm.backdroptype_id = backdrop.backdroptype_id;
    editBackdropForm.color = backdrop.color;
    editBackdropForm.image = backdrop.image;

    // checking if there's changes on the form
    editBackdropForm.origId = backdrop.id;
    editBackdropForm.origBackdroptypeId = backdrop.backdroptype_id;
    editBackdropForm.origColor = backdrop.color;
    editBackdropForm.origImage = backdrop.image;

    editingBackdrop.value = true;

    console.log("Backdrop data loaded into form:", editBackdropForm);
};

const noChangesEdit = computed(() => {
    return (
        editBackdropForm.origId === editBackdropForm.id &&
        editBackdropForm.origBackdroptypeId ===
            editBackdropForm.backdroptype_id &&
        editBackdropForm.origColor === editBackdropForm.color &&
        editBackdropForm.origImage === editBackdropForm.image
    );
});

const closeEditModal = () => {
    editingBackdrop.value = false;
    editBackdropForm.reset();
};

const submitEditBackdrop = () => {
    console.log("Submitting form with data:", editBackdropForm);

    if (noChangesEdit.value) {
        editBackdropForm.backdroptype_id = editBackdropForm.origBackdroptypeId;
        editBackdropForm.color = editBackdropForm.origColor;
        editBackdropForm.image = editBackdropForm.origImage;
    }

    editBackdropForm.post(
        route("backdrop.update", {
            backdrop: editBackdropForm.id, page: currentPage.value
        }),
     {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => closeEditModal(),
        onFinish: () => editBackdropForm.reset(),
    });
};

</script>

<template>
    <Head title="Backdrops" />

    <AdminAuthenticatedLayout>
        <div
            class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700"
        >
            <div class="w-full mb-1">
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
                                        >Backdrops</span
                                    >
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1
                        class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white"
                    >
                        All backdrops
                    </h1>
                </div>
                <div class="items-center justify-between block sm:flex">
                    <div
                        class="flex flex-wrap items-center mb-4 sm:mb-0 w-full sm:w-auto"
                    >
                        <div class="relative w-full">
                            <div class="flex flex-wrap items-center">
                                <button
                                    type="button"
                                    :class="[
                                        'flex items-center justify-center shadow-sm border font-semibold me-2 px-3.5 pb-1 rounded-full',
                                        {
                                            'border-primary-600 bg-primary-600 text-white':
                                                currentFilter === 'all',
                                            'bg-white border-pink-200 text-primary-600':
                                                currentFilter !== 'all',
                                        },
                                    ]"
                                    @click="filterTable('all')"
                                >
                                    All
                                    <span class="text-pink-500 ml-1">{{
                                        allCount
                                    }}</span>
                                </button>
                                <button
                                    type="button"
                                    :class="[
                                        'flex items-center justify-center shadow-sm border font-semibold me-2 px-3.5 pb-1 rounded-full',
                                        {
                                            'border-primary-600 bg-primary-600 text-white':
                                                currentFilter === 'plain',
                                            'bg-white border-pink-200 text-primary-600':
                                                currentFilter !== 'plain',
                                        },
                                    ]"
                                    @click="filterTable('plain')"
                                >
                                    Plain
                                    <span class="text-pink-500 ml-1">{{
                                        plainCount
                                    }}</span>
                                </button>
                                <button
                                    type="button"
                                    :class="[
                                        'flex items-center justify-center shadow-sm border font-semibold me-2 px-3.5 pb-1 rounded-full',
                                        {
                                            'border-primary-600 bg-primary-600 text-white':
                                                currentFilter === 'sequins',
                                            'bg-white border-pink-200 text-primary-600':
                                                currentFilter !== 'sequins',
                                        },
                                    ]"
                                    @click="filterTable('sequins')"
                                >
                                    Sequins
                                    <span class="text-pink-500 ml-1">{{
                                        sequinsCount
                                    }}</span>
                                </button>
                                <button
                                    type="button"
                                    :class="[
                                        'flex items-center justify-center shadow-sm border font-semibold me-2 px-3.5 pb-1 rounded-full',
                                        {
                                            'border-primary-600 bg-primary-600 text-white':
                                                currentFilter === 'custom',
                                            'bg-white border-pink-200 text-primary-600':
                                                currentFilter !== 'custom',
                                        },
                                    ]"
                                    @click="filterTable('custom')"
                                >
                                    Custom
                                    <span class="text-pink-500 ml-1">{{
                                        customCount
                                    }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between w-full gap-1 sm:w-auto">
                        <PrimaryButton
                            @click="
                                () =>
                                    addNewBackdrop({
                                        backdroptype_id: '',
                                        color: '',
                                        image: null,
                                    })
                            "
                            class="normal-case"
                        >
                            Add new backdrop
                        </PrimaryButton>
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
                                    <th scope="col" class="p-4">
                                        <div class="flex items-center">
                                            <input
                                                id="checkbox-all"
                                                aria-describedby="checkbox-1"
                                                type="checkbox"
                                                class="w-4 h-4 border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:focus:ring-primary-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600"
                                            />
                                            <label
                                                for="checkbox-all"
                                                class="sr-only"
                                                >checkbox</label
                                            >
                                        </div>
                                    </th>
                                    <th
                                        scope="col"
                                        class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400"
                                    >
                                        Backdrop Type
                                    </th>
                                    <th
                                        scope="col"
                                        class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400"
                                    >
                                        Color
                                    </th>
                                    <th
                                        scope="col"
                                        class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400"
                                    >
                                        Image
                                    </th>
                                    <th
                                        scope="col"
                                        class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400"
                                    >
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                            >
                                <tr
                                    v-for="backdrop in filteredBackdrops"
                                    :key="backdrop.id"
                                    class="hover:bg-gray-100 dark:hover:bg-gray-700"
                                >
                                    <td class="w-4 p-4">
                                        <div class="flex items-center">
                                            <input
                                                id="checkbox-{{ .id }}"
                                                aria-describedby="checkbox-1"
                                                type="checkbox"
                                                class="w-4 h-4 border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:focus:ring-primary-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600"
                                            />
                                            <label
                                                for="checkbox-{{ .id }}"
                                                class="sr-only"
                                                >checkbox</label
                                            >
                                        </div>
                                    </td>
                                    <td
                                        class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                    >
                                        {{ backdrop.backdroptype }}
                                    </td>

                                    <td
                                        class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                    >
                                        {{ backdrop.color }}
                                    </td>

                                    <td
                                        v-if="backdrop.backdroptype == 'Plain'"
                                        class="max-w-sm p-4 overflow-hidden text-base font-normal text-gray-500 truncate xl:max-w-xs dark:text-gray-400"
                                    >
                                        <img
                                            :src="
                                                `/uploads/backdrop/plain/` +
                                                backdrop.image
                                            "
                                            width="100px"
                                            alt="Backdrop Image"
                                        />
                                    </td>

                                    <td
                                        v-else-if="
                                            backdrop.backdroptype == 'Sequins'
                                        "
                                        class="max-w-sm p-4 overflow-hidden text-base font-normal text-gray-500 truncate xl:max-w-xs dark:text-gray-400"
                                    >
                                        <img
                                            :src="
                                                `/uploads/backdrop/sequins/` +
                                                backdrop.image
                                            "
                                            width="100px"
                                            alt="Backdrop Image"
                                        />
                                    </td>

                                    <td
                                        v-else-if="
                                            backdrop.backdroptype == 'Custom'
                                        "
                                        class="max-w-sm p-4 overflow-hidden text-base font-normal text-gray-500 truncate xl:max-w-xs dark:text-gray-400"
                                    >
                                        <img
                                            :src="
                                                `/uploads/backdrop/custom/` +
                                                backdrop.image
                                            "
                                            width="100px"
                                            alt="Backdrop Image"
                                        />
                                    </td>

                                    <td class="p-4 space-x-2 whitespace-nowrap">
                                        <button
                                            type="button"
                                            id="updateProductButton"
                                            @click="openEditModal(backdrop)"
                                            class="inline-flex items-center p-2 text-sm font-medium text-center text-white rounded-md bg-yellow-300 hover:bg-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-300 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-yellow-300 dark:hover:bg-yellow-400 dark:focus:ring-yellow-200"
                                        >
                                            <svg
                                                class="w-4 h-4"
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"
                                                ></path>
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                    clip-rule="evenodd"
                                                ></path>
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            id="deleteProductButton"
                                            class="inline-flex items-center p-2 text-sm font-medium text-center text-white bg-red-700 rounded-md hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-700 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-red-700 dark:hover:bg-red-800 dark:focus:ring-red-900"
                                        >
                                            <svg
                                                class="w-4 h-4"
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd"
                                                ></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="backdrops.data.length === 0">
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
                    <span class="font-semibold text-gray-700 dark:text-white">{{
                        filteredCount ? 1 : 0
                    }}</span>
                    to
                    <span class="font-semibold text-gray-700 dark:text-white">
                        {{ filteredCount ? filteredCount : 0 }}</span
                    >
                    of
                    <span class="font-semibold text-gray-700 dark:text-white">
                        {{ displayCount }}
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

        <!-- <td><AButton :href="`/admin/backdrop/edit/${backdrop.id}`">Edit</AButton></td> -->

        <!-- ADD BACKDROP TYPE MODAL -->
        <Modal :show="addingBackdrop" @close="closeAddBackdropModal">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                <!-- Modal header -->
                <div
                    class="flex items-start justify-between p-5 border-b rounded-t dark:border-gray-700"
                >
                    <h3 class="text-xl font-semibold dark:text-white">
                        Add new backdrop
                    </h3>
                    <button
                        type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white"
                        @click="closeAddBackdropModal"
                    >
                        <svg
                            class="w-5 h-5"
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
                </div>
                <!-- Modal body -->

                <div class="p-6 space-y-6">
                    <form @submit.prevent="submitAddBackdrop">
                        <div class="mb-6">
                            <div class="col-span-6 sm:col-span-3">
                                <div>
                                    <InputLabel
                                        for="backdroptype"
                                        value="Backdrop Type"
                                    />

                                    <select
                                        id="add-backdroptype"
                                        placeholder="Backdrop Type"
                                        class="select select-info rounded-lg border-gray-300 mt-1 block w-full"
                                        v-model="
                                            addBackdropForm.backdroptype_id
                                        "
                                    >
                                        <option disabled value="">
                                            Backdrop Type
                                        </option>
                                        <option
                                            v-for="backdroptype in backdroptypes"
                                            :key="backdroptype.id"
                                            :value="backdroptype.id"
                                        >
                                            {{ backdroptype.name }}
                                        </option>
                                    </select>
                                    <InputError
                                        class="mt-2"
                                        :message="
                                            addBackdropForm.errors.backdroptype
                                        "
                                    />
                                </div>

                                <div>
                                    <InputLabel for="color" value="Color" />

                                    <TextInput
                                        id="add-color"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="addBackdropForm.color"
                                        required
                                        autocomplete="off"
                                        placeholder="e.g. Black, White, Red, etc."
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="addBackdropForm.errors.color"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="backdropimage"
                                        value="Backdrop Image"
                                    />

                                    <input
                                        type="file"
                                        class="file-input w-full mt-1 ps-0 border-gray-300 file-input-info"
                                        name="add-image"
                                        id="add-image"
                                        @change="handleFileUpload"
                                    />
                                </div>
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div class="mt-6 flex justify-end">
                            <SecondaryButton
                                class="normal-case py-2.5 px-5"
                                @click="closeAddBackdropModal"
                            >
                                Cancel
                            </SecondaryButton>

                            <PrimaryButton
                                class="normal-case ms-3"
                                :class="{
                                    'opacity-25': addBackdropForm.processing,
                                }"
                                :disabled="addBackdropForm.processing"
                            >
                                Add Backdrop Type
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </Modal>

        <!-- EDIT BACKDROP MODAL -->
        <Modal :show="editingBackdrop" @close="closeEditModal">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                <div
                    class="flex items-start justify-between p-5 border-b rounded-t dark:border-gray-700"
                >
                    <h3 class="text-xl font-semibold dark:text-white">
                        Edit backdrop
                    </h3>
                    <button
                        type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white"
                        @click="closeEditModal"
                    >
                        <svg
                            class="w-5 h-5"
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
                </div>
                <div class="p-6 space-y-6">
                    <form @submit.prevent="submitEditBackdrop">
                        <div class="mb-6">
                            <div class="col-span-6 sm:col-span-3">
                                <div>
                                    <InputLabel
                                        for="backdroptype"
                                        value="Backdrop Type"
                                    />
                                    <select
                                        id="edit-backdroptype"
                                        placeholder="Backdrop Type"
                                        required
                                        class="select select-info rounded-lg border-gray-300 mt-1 block w-full"
                                        v-model="
                                            editBackdropForm.backdroptype_id
                                        "
                                    >
                                        <option disabled value="">
                                            Backdrop Type
                                        </option>
                                        <option
                                            v-for="backdroptype in backdroptypes"
                                            :key="backdroptype.id"
                                            :value="backdroptype.id"
                                        >
                                            {{ backdroptype.name }}
                                        </option>
                                    </select>
                                    <InputError
                                        class="mt-2"
                                        :message="
                                            editBackdropForm.errors
                                                .backdroptype_id
                                        "
                                    />
                                </div> 

                                <div>
                                    <InputLabel for="color" value="Color" />
                                    <TextInput
                                        id="edit-color"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="editBackdropForm.color"
                                        required
                                        autocomplete="off"
                                        placeholder="e.g. Black, White, Red, etc."
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="editBackdropForm.errors.color"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="backdropimage"
                                        value="Backdrop Image"
                                    />
                                    <input
                                        type="file"
                                        class="file-input w-full mt-1 ps-0 border-gray-300 file-input-info"
                                        name="edit-image"
                                        id="edit-image"
                                        @change="handleFileUploadEdit"
                                    />
                                    {{
                                        editBackdropForm.image
                                            ? editBackdropForm.image.name
                                            : ""
                                    }}
                                    <InputError
                                        class="mt-2"
                                        :message="editBackdropForm.errors.image"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <SecondaryButton
                                class="normal-case px-5"
                                @click="closeEditModal"
                            >
                                Cancel
                            </SecondaryButton>
                            <PrimaryButton
                                class="normal-case ms-3"
                                :class="{
                                    'opacity-25':
                                        editBackdropForm.processing || noChangesEdit
                                        ,
                                }"
                                :disabled="
                                    editBackdropForm.processing || noChangesEdit
                                "
                            >
                                Update
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </Modal>
    </AdminAuthenticatedLayout>
</template>
