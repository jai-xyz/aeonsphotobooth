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
import DeleteModal from "@/Components/DeleteModal.vue";
import DangerButton from "@/Components/DangerButton.vue";

const props = defineProps({
    backdroptypes: {
        type: [Array, Object],
        required: true,
    },
});

/* #############################
            PAGINATION
   ############################# */
const paginationbackdroptypes = ref(props.backdroptypes.data);
const currentPage = ref(1);

const pagination = ref({
    total: props.backdroptypes.total,
    per_page: props.backdroptypes.per_page,
    current_page: props.backdroptypes.current_page,
    last_page: props.backdroptypes.last_page,
    from: props.backdroptypes.from,
    to: props.backdroptypes.to,
});

const changePage = (page) => {
    Inertia.get(
        route("backdroptype.index", { page }),
        {},
        {
            preserveScroll: true,
            preserveState: true,
        }
    );
};

const activePage = computed(() => {
    const activePageItem = props.backdroptypes.links.find(
        (link) => link.active
    );
    return activePageItem
        ? new URL(activePageItem.url).searchParams.get("page")
        : 1;
});

watch(
    () => props.backdroptypes,
    (newBackdropTypes) => {
        paginationbackdroptypes.value = newBackdropTypes.data;
        pagination.value = {
            total: newBackdropTypes.total,
            per_page: newBackdropTypes.per_page,
            current_page: newBackdropTypes.current_page,
            last_page: newBackdropTypes.last_page,
            from: newBackdropTypes.from,
            to: newBackdropTypes.to,
        };
        currentPage.value = activePage.value;
    }
);

/* #############################
        ADD BACKDROP TYPE
   ############################# */
const addBackdropTypeForm = useForm({
    name: "",
});

const addingBackdropType = ref(false);

const addNewBackdropType = (backdroptype) => {
    addBackdropTypeForm.name = backdroptype.name;
    addingBackdropType.value = true;
};

const closeAddBackdropTypeModal = () => {
    addingBackdropType.value = false;
    addBackdropTypeForm.reset();
};

const submitAddBackdropType = () => {
    addBackdropTypeForm.post(
        route("backdroptype.store", { page: currentPage.value }),
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => closeAddBackdropTypeModal(),
            onFinish: () => addBackdropTypeForm.reset(),
        }
    );
};

/* #############################
        EDIT BACKDROP TYPE
   ############################# */
const currentBackdropType = ref(null);
const editingBackdropType = ref(false);

const editBackdropTypeForm = useForm({
    id: null,
    name: "",
});

const openEditModal = (type) => {
    currentBackdropType.value = type;
    editBackdropTypeForm.id = type.id;
    editBackdropTypeForm.name = type.name;
    editingBackdropType.value = true;
};

const closeEditBackdropTypeModal = () => {
    editingBackdropType.value = false;
    editBackdropTypeForm.reset();
};

const submitEditBackdropType = () => {
    editBackdropTypeForm.patch(
        route("backdroptype.update", {
            id: editBackdropTypeForm.id,
            page: currentPage.value,
        }),
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => closeEditBackdropTypeModal(),
            onFinish: () => editBackdropTypeForm.reset(),
        }
    );
};

const noChanges = computed(() => {
    return (
        editBackdropTypeForm.id === currentBackdropType.value.id &&
        editBackdropTypeForm.name === currentBackdropType.value.name
    );
});

/* #############################
        DELETE BACKDROP TYPE
   ############################# */
const deletingBackdroptype = ref(false);

const openDeleteModal = (type) => {
    currentBackdropType.value = type;
    deletingBackdroptype.value = true;
};

const closeDeleteModal = () => {
    deletingBackdroptype.value = false;
    deleteForm.reset();
};

const deleteForm = useForm({});

const submitDelete = () => {
    deleteForm.delete(
        route("backdroptype.destroy", currentBackdropType.value.id),
        {
            preserveScroll: true,
            onSuccess: () => closeDeleteModal(),
        }
    );
};
</script>

<template>
    <Head title="Backdrop Types" />

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
                                        >Backdrop</span
                                    >
                                </div>
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
                                        >Backdrops Types</span
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
                                        List of Backdrop Types
                                    </h1>
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex justify-between w-full gap-1 sm:w-auto"
                        >
                            <PrimaryButton
                                @click="
                                    () =>
                                        addNewBackdropType({
                                            name: '',
                                        })
                                "
                                class="normal-case"
                            >
                                Add backdrop type
                            </PrimaryButton>
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
                                        class="text-center p-4 text-xs font-medium text-gray-500 uppercase dark:text-gray-400"
                                    >
                                        Type
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center p-4 text-xs font-medium text-gray-500 uppercase dark:text-gray-400"
                                    >
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                            >
                                <tr
                                    v-for="backdroptype in backdroptypes.data"
                                    :key="backdroptype.id"
                                    class="hover:bg-gray-100 dark:hover:bg-gray-700"
                                >
                                    <td
                                        class="text-center p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                    >
                                        {{ backdroptype.name }}
                                    </td>

                                    <td
                                        class="text-center p-4 space-x-2 whitespace-nowrap"
                                    >
                                        <button
                                            type="button"
                                            id="updateProductButton"
                                            @click="openEditModal(backdroptype)"
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
                                            v-if="!backdroptype.isUsed"
                                            type="button"
                                            id="deleteProductButton"
                                            @click="
                                                openDeleteModal(backdroptype)
                                            "
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
                                <tr v-if="backdroptypes.data.length === 0">
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

        <!-- ADD BACKDROP TYPE MODAL -->
        <Modal :show="addingBackdropType" @close="closeAddBackdropTypeModal">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                <!-- Modal header -->
                <div
                    class="flex items-start justify-between p-5 border-b rounded-t dark:border-gray-700"
                >
                    <h3 class="text-xl font-semibold dark:text-white">
                        Add new backdrop type
                    </h3>
                    <button
                        type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white"
                        @click="closeAddBackdropTypeModal"
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
                    <form @submit.prevent="submitAddBackdropType">
                        <div class="mb-6">
                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel
                                    for="name"
                                    value="Name"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                />

                                <TextInput
                                    id="name"
                                    type="text"
                                    class="shadow-sm text-sm bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                    v-model="addBackdropTypeForm.name"
                                    required
                                    autofocus
                                    autocomplete="off"
                                    placeholder="e.g. Plain"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="addBackdropTypeForm.errors.name"
                                />
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div class="mt-6 flex justify-end">
                            <SecondaryButton
                                class="normal-case py-2.5 px-5"
                                @click="closeAddBackdropTypeModal"
                            >
                                Cancel
                            </SecondaryButton>

                            <PrimaryButton
                                class="normal-case ms-3"
                                :class="{
                                    'opacity-25':
                                        addBackdropTypeForm.processing,
                                }"
                                :disabled="addBackdropTypeForm.processing"
                            >
                                Add Backdrop Type
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </Modal>

        <!-- EDIT BACKDROP TYPE MODAL -->
        <Modal :show="editingBackdropType" @close="closeEditBackdropTypeModal">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                <!-- Modal header -->
                <div
                    class="flex items-start justify-between p-5 border-b rounded-t dark:border-gray-700"
                >
                    <h3 class="text-xl font-semibold dark:text-white">
                        Add new backdrop type
                    </h3>
                    <button
                        type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white"
                        @click="closeEditBackdropTypeModal"
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
                    <form @submit.prevent="submitEditBackdropType">
                        <div class="mb-6">
                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel
                                    for="name"
                                    value="Name"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                />

                                <TextInput
                                    id="name"
                                    type="text"
                                    class="shadow-sm text-sm bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                    v-model="editBackdropTypeForm.name"
                                    required
                                    autofocus
                                    autocomplete="off"
                                    placeholder="e.g. Plain"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="editBackdropTypeForm.errors.name"
                                />
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div class="mt-6 flex justify-end">
                            <SecondaryButton
                                class="normal-case py-2.5 px-5"
                                @click="closeEditBackdropTypeModal"
                            >
                                Cancel
                            </SecondaryButton>

                            <PrimaryButton
                                class="normal-case ms-3"
                                :class="{
                                    'opacity-25':
                                        editBackdropTypeForm.processing ||
                                        noChanges,
                                }"
                                :disabled="
                                    editBackdropTypeForm.processing || noChanges
                                "
                            >
                                Add Backdrop Type
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </Modal>

        <!-- DELETE BACKDROP TYPE MODAL -->
        <DeleteModal :show="deletingBackdroptype" @close="closeDeleteModal">
            <div class="relative w-full h-full max-w-md md:h-auto">
                <!-- Modal content -->
                <div
                    class="relative bg-white rounded-lg shadow dark:bg-gray-800"
                >
                    <!-- Modal header -->
                    <div class="flex justify-end p-2">
                        <button
                            type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white"
                            @click="closeDeleteModal"
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
                    <div class="p-6 pt-0 text-center">
                        <svg
                            class="w-16 h-16 mx-auto text-red-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            ></path>
                        </svg>
                        <h3
                            class="mt-5 mb-6 text-lg text-gray-500 dark:text-gray-400"
                        >
                            Are you sure you want to delete this backdrop type?
                            This action cannot be undone.
                        </h3>
                        <!-- Modal footer -->
                        <div class="mt-6 flex justify-center gap-6">
                            <DangerButton
                                class="normal-case bg-danger"
                                :class="{
                                    'opacity-25': deleteForm.processing,
                                }"
                                :disabled="deleteForm.processing"
                                @click="submitDelete"
                            >
                                Yes, I'm sure
                            </DangerButton>

                            <SecondaryButton
                                class="normal-case px-5"
                                @click="closeDeleteModal"
                            >
                                No, cancel
                            </SecondaryButton>
                        </div>
                        <!-- <a
                            href="#"
                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center mr-2 dark:focus:ring-red-800"
                        >
                            Yes, I'm sure
                        </a>
                        <a
                            href="#"
                            class="text-gray-900 bg-white hover:bg-gray-100 focus:ring-4 focus:ring-primary-300 border border-gray-200 font-medium inline-flex items-center rounded-lg text-base px-3 py-2.5 text-center dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700"
                            data-modal-hide="delete-user-modal"
                        >
                            No, cancel
                        </a> -->
                    </div>
                </div>
            </div>
        </DeleteModal>
    </AdminAuthenticatedLayout>
</template>
