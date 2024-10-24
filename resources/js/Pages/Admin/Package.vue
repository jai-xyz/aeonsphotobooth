<script setup>
import { Head, useForm, router, usePage } from "@inertiajs/vue3";
import AdminAuthenticatedLayout from "@/Layouts/AdminAuthenticatedLayout.vue";
import AButton from "@/Components/AButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import NumberInput from "@/Components/NumberInput.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextArea from "@/Components/TextArea.vue";
import TextInput from "@/Components/TextInput.vue";
import Modal from "@/Components/Modal.vue";
import DeleteModal from "@/Components/DeleteModal.vue";
import { watch, computed, ref } from "vue";
import DangerButton from "@/Components/DangerButton.vue";
import { Inertia } from "@inertiajs/inertia";

// TODO: SEARCH BAR

const props = defineProps({
    packages: {
        type: [Array, Object],
        required: true,
    },
});

// PAGINATION
const paginationpackages = ref(props.packages.data);
const currentPage = ref(1);

const pagination = ref({
    total: props.packages.total,
    per_page: props.packages.per_page,
    current_page: props.packages.current_page,
    last_page: props.packages.last_page,
    from: props.packages.from,
    to: props.packages.to,
});

const changePage = (page) => {
    Inertia.get(
        route("package.index", { page }),
        {},
        {
            preserveScroll: true,
            preserveState: true,
        }
    );
};

const activePage = computed(() => {
    const activePageItem = props.packages.links.find((link) => link.active);
    return activePageItem
        ? new URL(activePageItem.url).searchParams.get("page")
        : 1;
});

watch(
    () => props.packages,
    (newPackages) => {
        paginationpackages.value = newPackages.data;
        pagination.value = {
            total: newPackages.total,
            per_page: newPackages.per_page,
            current_page: newPackages.current_page,
            last_page: newPackages.last_page,
            from: newPackages.from,
            to: newPackages.to,
        };
        currentPage.value = activePage.value;
    }
);

// ADD
const addForm = useForm({
    name: "",
    price: "",
    duration: "",
    size: "",
    inclusion: "",
    note: "",
    extension: "",
});

const addingNewProduct = ref(false);

const addNewProduct = (product) => {
    addForm.name = product.name;
    addForm.price = product.price;
    addForm.duration = product.duration;
    addForm.size = product.size;
    addForm.inclusion = product.inclusion;
    addForm.note = product.note;
    addForm.extension = product.extension;
    addingNewProduct.value = true;
};

const submitAdd = () => {
    addForm.post(route("package.store", { page: currentPage.value }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => closeAddModal(),
        onFinish: () => addForm.reset(),
    });
};

const closeAddModal = () => {
    addingNewProduct.value = false;
    addForm.reset();
};

// EDIT
const editingProduct = ref(false);
const currentPackage = ref(null);

const editForm = useForm({
    id: null,
    name: "",
    price: "",
    duration: "",
    size: "",
    inclusion: "",
    note: "",
    extension: "",
});

const openEditModal = (pkg) => {
    currentPackage.value = pkg;
    editForm.id = pkg.id;
    editForm.name = pkg.name;
    editForm.price = pkg.price;
    editForm.duration = pkg.duration;
    editForm.size = [pkg.size, pkg.size2, pkg.size3, pkg.size4, pkg.size5]
        .filter((size) => size)
        .join(", ");
    editForm.inclusion = pkg.inclusion;
    editForm.note = pkg.note;
    editForm.extension = pkg.extension;

    // for checking if there are changes
    editForm.OrigId = pkg.id;
    editForm.OrigName = pkg.name;
    editForm.OrigPrice = pkg.price;
    editForm.OrigDuration = pkg.duration;
    editForm.OrigSize = [pkg.size, pkg.size2, pkg.size3, pkg.size4, pkg.size5]
        .filter((size) => size)
        .join(", ");
    editForm.OrigInclusion = pkg.inclusion;
    editForm.OrigNote = pkg.note;
    editForm.OrigExtension = pkg.extension;
    editingProduct.value = true;
};

const submitEdit = () => {
    editForm.patch(
        route("package.update", { id: editForm.id, page: currentPage.value }),
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => closeEditModal(),
            onFinish: () => editForm.reset(),
        }
    );
};

const closeEditModal = () => {
    editingProduct.value = false;
    editForm.reset();
};

const noChanges = computed(() => {
    return (
        editForm.OrigId === editForm.id &&
        editForm.OrigName === editForm.name &&
        editForm.OrigPrice === editForm.price &&
        editForm.OrigDuration === editForm.duration &&
        editForm.OrigSize === editForm.size &&
        editForm.OrigInclusion === editForm.inclusion &&
        editForm.OrigNote === editForm.note &&
        editForm.OrigExtension === editForm.extension
    );
});

// DELETE
const deletingProduct = ref(false);

const openDeleteModal = (pkg) => {
    currentPackage.value = pkg;
    deletingProduct.value = true;
};

const closeDeleteModal = () => {
    deletingProduct.value = false;
    deleteForm.reset();
};

const deleteForm = useForm({});

const submitDelete = () => {
    deleteForm.delete(route("package.destroy", currentPackage.value.id), {
        preserveScroll: true,
        onSuccess: () => closeDeleteModal(),
    });
};

// SEARCH
let search = ref(usePage().props.search),
    pageNumber = ref(1);

let packageUrl = computed(() => {
    let url = new URL(route("package.index"));
    url.searchParams.append("page", pageNumber.value);

    if (search.value) {
        url.searchParams.append("search", search.value);
    }

    return url;
});

watch(
    () => packageUrl.value,
    (updatedPackageUrl) => {
        router.visit(updatedPackageUrl, {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        });
    }
);
</script>

<template>
    <Head title="Packages" />

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
                                        >Packages</span
                                    >
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1
                        class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white"
                    >
                        All packages
                    </h1>
                </div>
                <div
                    class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700"
                >
                    <div class="flex items-center mb-4 sm:mb-0">
                        <div class="relative w-48 mt-1 sm:w-64 xl:w-96">
                            <div
                                class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none"
                            >
                                <svg
                                    class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"
                                    />
                                </svg>
                            </div>
                            <input
                                type="text"
                                name="email"
                                id="search"
                                v-model="search"
                                autocomplete="off"
                                class="ps-10 pr-10 bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Search for packages"
                            />
                            <div
                                v-show="search !== ''"
                                class="absolute inset-y-0 end-0 flex items-center pe-3 cursor-pointer"
                                @click="search = ''"
                            >
                                <svg
                                    class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18 17.94 6M18 18 6.06 6"
                                    />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <PrimaryButton
                        @click="
                            () =>
                                addNewProduct({
                                    name: '',
                                    price: '',
                                    duration: '',
                                    size: '',
                                    inclusion: '',
                                    note: '',
                                    extension: '',
                                })
                        "
                        class="normal-case"
                    >
                        Add new product
                    </PrimaryButton>
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
                                        Package Name
                                    </th>
                                    <th
                                        scope="col"
                                        class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400"
                                    >
                                        Price
                                    </th>
                                    <th
                                        scope="col"
                                        class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400"
                                    >
                                        Duration
                                    </th>
                                    <th
                                        scope="col"
                                        class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400"
                                    >
                                        Size/s
                                    </th>
                                    <th
                                        scope="col"
                                        class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400"
                                    >
                                        Inclusion
                                    </th>
                                    <th
                                        scope="col"
                                        class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400"
                                    >
                                        Note
                                    </th>
                                    <th
                                        scope="col"
                                        class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400"
                                    >
                                        Extension
                                    </th>
                                    <th
                                        scope="col"
                                        class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                            >
                                <tr
                                    v-for="pkg in packages.data"
                                    :key="pkg.id"
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
                                        class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400"
                                    >
                                        <div
                                            class="text-base font-semibold text-gray-900 dark:text-white"
                                        >
                                            {{ pkg.name }}
                                        </div>
                                        <div
                                            class="text-sm font-normal text-gray-500 dark:text-gray-400"
                                        >
                                            category
                                        </div>
                                    </td>
                                    <td
                                        class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                    >
                                        ₱{{ pkg.price }}
                                    </td>
                                    <td
                                        class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                    >
                                        {{ pkg.duration }}
                                    </td>
                                    <td
                                        class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                    >
                                        {{ pkg.size
                                        }}{{ pkg.size2 ? ", " + pkg.size2 : ""
                                        }}{{ pkg.size3 ? ", " + pkg.size3 : ""
                                        }}{{ pkg.size4 ? ", " + pkg.size4 : ""
                                        }}{{
                                            pkg.size5 ? ", " + pkg.size5 : ""
                                        }}
                                    </td>
                                    <td
                                        class="max-w-sm p-4 overflow-hidden text-base font-normal text-gray-500 truncate xl:max-w-xs dark:text-gray-400"
                                    >
                                        {{ pkg.inclusion }}
                                    </td>
                                    <td
                                        class="max-w-sm p-4 overflow-hidden text-base font-normal text-gray-500 truncate xl:max-w-xs dark:text-gray-400"
                                    >
                                        {{ pkg.note }}
                                    </td>
                                    <td
                                        class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                    >
                                        {{ pkg.extension }}
                                    </td>

                                    <td class="p-4 space-x-2 whitespace-nowrap">
                                        <button
                                            type="button"
                                            id="updateProductButton"
                                            @click="openEditModal(pkg)"
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
                                            @click="openDeleteModal(pkg)"
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
                                 <tr v-if="packages.data.length === 0">
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
                        >{{ pagination.from }} to {{ pagination.to }}</span
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

        <!-- ADD PRODUCT MODAL -->
        <Modal :show="addingNewProduct" @close="closeAddModal">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                <!-- Modal header -->
                <div
                    class="flex items-start justify-between p-5 border-b rounded-t dark:border-gray-700"
                >
                    <h3 class="text-xl font-semibold dark:text-white">
                        Add new package
                    </h3>
                    <button
                        type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white"
                        @click="closeAddModal"
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
                    <form @submit.prevent="submitAdd">
                        <div class="grid grid-cols-6 gap-6 mb-6">
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
                                    v-model="addForm.name"
                                    required
                                    autofocus
                                    autocomplete="off"
                                    placeholder="e.g. Package A"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="addForm.errors.name"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel
                                    for="price"
                                    value="Price"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                />
                                <NumberInput
                                    id="price"
                                    type="text"
                                    class="shadow-sm text-sm bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                    v-model="addForm.price"
                                    required
                                    autocomplete="off"
                                    placeholder="e.g. ₱3500"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="addForm.errors.price"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel
                                    for="duration"
                                    value="Duration"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                />
                                <TextInput
                                    id="duration"
                                    type="text"
                                    class="shadow-sm text-sm bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                    v-model="addForm.duration"
                                    required
                                    autocomplete="off"
                                    placeholder="e.g. 2hrs"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="addForm.errors.duration"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel
                                    for="size"
                                    value="Size"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                />
                                <TextInput
                                    id="size"
                                    type="text"
                                    class="shadow-sm text-sm bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                    v-model="addForm.size"
                                    required
                                    autocomplete="off"
                                    placeholder="e.g. 4r"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="addForm.errors.size"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel
                                    for="note"
                                    value="Note"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                />
                                <TextInput
                                    id="note"
                                    type="text"
                                    class="shadow-sm text-sm bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                    v-model="addForm.note"
                                    required
                                    autocomplete="off"
                                    placeholder="e.g. transportation charge not included yet."
                                />

                                <InputError
                                    class="mt-2"
                                    :message="addForm.errors.note"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel
                                    for="extension"
                                    value="Extension"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                />
                                <TextInput
                                    id="extension"
                                    type="text"
                                    class="shadow-sm text-sm bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                    v-model="addForm.extension"
                                    required
                                    autocomplete="off"
                                    placeholder="e.g. ₱1000.00 / hour extension"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="addForm.errors.extension"
                                />
                            </div>

                            <div class="col-span-6">
                                <InputLabel
                                    for="inclusion"
                                    value="Inclusion"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                />

                                <TextArea
                                    id="inclusion"
                                    type="text"
                                    rows="4"
                                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    v-model="addForm.inclusion"
                                    required
                                    autocomplete="off"
                                    placeholder="e.g. 2 HOURS of Unlimited shots - 4 shot template (Single Printing)"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="addForm.errors.inclusion"
                                />
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div class="mt-6 flex justify-start gap-6">
                            <PrimaryButton
                                class="normal-case"
                                :class="{ 'opacity-25': addForm.processing }"
                                :disabled="addForm.processing"
                            >
                                Add package
                            </PrimaryButton>

                            <SecondaryButton
                                class="normal-case py-2.5 px-5"
                                @click="closeAddModal"
                            >
                                Cancel
                            </SecondaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </Modal>

        <!-- EDIT PRODUCT MODAL -->
        <Modal :show="editingProduct" @close="closeEditModal">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                <div
                    class="flex items-start justify-between p-5 border-b rounded-t dark:border-gray-700"
                >
                    <h3 class="text-xl font-semibold dark:text-white">
                        Edit package
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
                    <form @submit.prevent="submitEdit">
                        <div class="grid grid-cols-6 gap-6 mb-6">
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
                                    v-model="editForm.name"
                                    required
                                    autofocus
                                    autocomplete="off"
                                    placeholder="e.g. Package A"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="editForm.errors.name"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel
                                    for="price"
                                    value="Price"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                />
                                <NumberInput
                                    id="price"
                                    type="text"
                                    class="shadow-sm text-sm bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                    v-model="editForm.price"
                                    required
                                    autocomplete="off"
                                    placeholder="e.g. ₱3500"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="editForm.errors.price"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel
                                    for="duration"
                                    value="Duration"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                />
                                <TextInput
                                    id="duration"
                                    type="text"
                                    class="shadow-sm text-sm bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                    v-model="editForm.duration"
                                    required
                                    autocomplete="off"
                                    placeholder="e.g. 2hrs"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="editForm.errors.duration"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel
                                    for="size"
                                    value="Size"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                />
                                <TextInput
                                    id="size"
                                    type="text"
                                    class="shadow-sm text-sm bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                    v-model="editForm.size"
                                    required
                                    autocomplete="off"
                                    placeholder="e.g. 4r"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="editForm.errors.size"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel
                                    for="note"
                                    value="Note"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                />
                                <TextInput
                                    id="note"
                                    type="text"
                                    class="shadow-sm text-sm bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                    v-model="editForm.note"
                                    required
                                    autocomplete="off"
                                    placeholder="e.g. transportation charge not included yet."
                                />

                                <InputError
                                    class="mt-2"
                                    :message="editForm.errors.note"
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <InputLabel
                                    for="extension"
                                    value="Extension"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                />
                                <TextInput
                                    id="extension"
                                    type="text"
                                    class="shadow-sm text-sm bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                    v-model="editForm.extension"
                                    required
                                    autocomplete="off"
                                    placeholder="e.g. ₱1000.00 / hour extension"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="editForm.errors.extension"
                                />
                            </div>

                            <div class="col-span-6">
                                <InputLabel
                                    for="inclusion"
                                    value="Inclusion"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                />

                                <TextArea
                                    id="inclusion"
                                    type="text"
                                    rows="6"
                                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    v-model="editForm.inclusion"
                                    required
                                    autocomplete="off"
                                    placeholder="e.g. 2 HOURS of Unlimited shots - 4 shot template (Single Printing)"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="editForm.errors.inclusion"
                                />
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div class="mt-6 flex justify-start gap-6">
                            <PrimaryButton
                                class="normal-case"
                                :class="{
                                    'opacity-25':
                                        editForm.processing || noChanges,
                                }"
                                :disabled="editForm.processing || noChanges"
                            >
                                Update
                            </PrimaryButton>

                            <SecondaryButton
                                class="normal-case px-5"
                                @click="closeEditModal"
                            >
                                Cancel
                            </SecondaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </Modal>

        <!-- DELETE PRODUCT MODAL -->

        <DeleteModal :show="deletingProduct" @close="closeDeleteModal">
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
                            Are you sure you want to delete this package? This
                            action cannot be undone.
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
