<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import AButton from "@/Components/AButton.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import UserToast from "@/Components/UserToast.vue";
import "../../../css/custom-styles.css";
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
    activeevents: {
        type: [Array, Object],
        required: true,
    },
    archivedevents: {
        type: [Array, Object],
        required: true,
    },
    auth: {
        type: Object,
        required: true,
    },
});

/* #############################
        PAGINATION - ACTIVE
   ############################# */

const paginationactiveevents = ref(props.activeevents.data);
const activeCurrentPage = ref(1);

const activepagination = ref({
    total: props.activeevents.total,
    per_page: props.activeevents.per_page,
    current_page: props.activeevents.current_page,
    last_page: props.activeevents.last_page,
    from: props.activeevents.from,
    to: props.activeevents.to,
});

const activeChangePage = (page) => {
    Inertia.get(
        route("user.event.index", { page }),
        {},
        {
            preserveScroll: true,
            preserveState: true,
        }
    );
};

const activePage = computed(() => {
    const activePageItem = props.activeevents.links.find((link) => link.active);
    return activePageItem
        ? new URL(activePageItem.url).searchParams.get("page")
        : 1;
});

watch(
    () => props.activeevents,
    (newPackages) => {
        paginationactiveevents.value = newPackages.data;
        activepagination.value = {
            total: newPackages.total,
            per_page: newPackages.per_page,
            current_page: newPackages.current_page,
            last_page: newPackages.last_page,
            from: newPackages.from,
            to: newPackages.to,
        };
        activeCurrentPage.value = activePage.value;
    }
);

/* #############################
        PAGINATION - archived
   ############################# */

const paginationarchivedevents = ref(props.archivedevents.data);
const archivedCurrentPage = ref(1);

const archivedpagination = ref({
    total: props.archivedevents.total,
    per_page: props.archivedevents.per_page,
    current_page: props.archivedevents.current_page,
    last_page: props.archivedevents.last_page,
    from: props.archivedevents.from,
    to: props.archivedevents.to,
});

const archivedChangePage = (page) => {
    Inertia.get(
        route("user.event.index", { page }),
        {},
        {
            preserveScroll: true,
            preserveState: true,
        }
    );
};

const archivedPage = computed(() => {
    const archivedPageItem = props.archivedevents.links.find(
        (link) => link.active
    );
    return archivedPageItem
        ? new URL(archivedPageItem.url).searchParams.get("page")
        : 1;
});

watch(
    () => props.archivedevents,
    (newPackages) => {
        paginationarchivedevents.value = newPackages.data;
        archivedpagination.value = {
            total: newPackages.total,
            per_page: newPackages.per_page,
            current_page: newPackages.current_page,
            last_page: newPackages.last_page,
            from: newPackages.from,
            to: newPackages.to,
        };
        archivedCurrentPage.value = archivedPage.value;
    }
);

function formatDate(dateStr) {
    const date = new Date(dateStr);
    const monthNames = [
        "Jan.",
        "Feb.",
        "Mar.",
        "Apr.",
        "May.",
        "Jun.",
        "Jul.",
        "Aug.",
        "Sep.",
        "Oct.",
        "Nov.",
        "Dec.",
    ];
    const month = monthNames[date.getMonth()];
    const day = String(date.getDate()).padStart(2, "0");
    const year = date.getFullYear();
    return `${month} ${day}, ${year}`;
}

/* #############################
        COLLAPSIBLE ROWS
   ############################# */
const visibleDetails = ref({});

const toggleDetails = (eventId) => {
    visibleDetails.value[eventId] = !visibleDetails.value[eventId];
};

const isDetailsVisible = (eventId) => {
    return !!visibleDetails.value[eventId];
};

/* #############################
        UPDATE EVENT STATUS
   ############################# */
const updatingEventStatus = ref(false);

const form = useForm({
    id: "",
    user_id: "",
    status: "",
});

const updateEventStatus = (event) => {
    form.id = event.id;
    form.user_id = event.user_id;
    form.status = event.status;
    updatingEventStatus.value = true;
};

const updateStatus = () => {
    form.patch(route("user.event.update", { event: form.id }), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onFinish: () => form.reset(),
    });
    console.log(form);
};

const closeModal = () => {
    updatingEventStatus.value = false;
};

/* #############################
        CONFIRMATION BACKDROP 
   ############################# */
const confirmationModal = ref(false);
const selectedEventId = ref(null);

const openConfirmationModal = (eventId) => {
    selectedEventId.value = eventId;
    confirmationModal.value = true;
};

const closeConfirmationModal = () => {
    confirmationModal.value = false;
};

const handleConfirm = () => {
    confirmationModal.value = false;
    cancelEvent(selectedEventId.value);
};

const cancelEvent = (eventId) => {
    // Find the event by ID and update its status to 'Cancelled'
    const activeeventsArray = Array.isArray(props.activeevents)
        ? props.activeevents
        : props.activeevents.data;
    const event = activeeventsArray.find((event) => event.id === eventId);
    if (event) {
        form.id = event.id;
        form.user_id = event.user_id;
        form.status = "Cancel";
        updateStatus();
    }
};

let view = ref("active");
</script>

<template>
    <UserToast />
    <Head title="Booked Events" />

    <AuthenticatedLayout :auth="auth">
        <div class="py-12 max-w-[1400px] mx-auto sm:px-18 lg:px-2">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg pb-6">
                <div class="flex justify-between items-center mx-12 pt-8 pb-2">
                    <h1
                        class="text-center text-2xl font-bold uppercase text-primary-700 dark:text-white"
                    >
                        Booked Events
                    </h1>
                    <div class="flex items-center align-center">
                        <button
                            @click="view = 'active'"
                            class="rounded-e-none flex justify-center align-center px-4 py-2 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest transition ease-in-out duration-150"
                            :class="{
                                'bg-primary-500 text-white focus:outline-none focus:ring-none focus:ring-0':
                                    view === 'active',
                                'text-gray-500  bg-primary-700':
                                    view !== 'active',
                            }"
                        >
                            Ongoing Events
                        </button>
                        <button
                            @click="view = 'archived'"
                            class="rounded-s-none flex justify-center align-center px-4 py-2 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest transition ease-in-out duration-150"
                            :class="{
                                'bg-primary-500 text-white focus:outline-none focus:ring-none focus:ring-0':
                                    view === 'archived',
                                'text-gray-500 bg-primary-700':
                                    view !== 'archived',
                            }"
                        >
                            Past Events
                        </button>
                    </div>
                </div>
                <div class="flex flex-col mx-12">
                    <div class="overflow-x-auto">
                        <div class="inline-block min-w-full align-middle">
                            <div class="overflow-hidden shadow">
                                <div v-if="view === 'active'">
                                    <table
                                        class="min-w-full divide-y border divide-gray-200 table-fixed dark:divide-gray-600"
                                    >
                                        <thead
                                            class="bg-primary-600 dark:bg-gray-700"
                                        >
                                            <tr>
                                                <th
                                                    scope="col"
                                                    class="p-4 text-xs font-extrabold text-left text-white uppercase dark:text-gray-400"
                                                >
                                                    Event Title
                                                </th>
                                                <th
                                                    scope="col"
                                                    class="p-4 text-xs font-extrabold text-left text-white uppercase dark:text-gray-400"
                                                >
                                                    Date & Time
                                                </th>
                                                <th
                                                    scope="col"
                                                    class="p-4 text-xs font-extrabold text-left text-white uppercase dark:text-gray-400"
                                                >
                                                    Contact Person
                                                </th>

                                                <th
                                                    scope="col"
                                                    class="p-4 text-xs font-extrabold text-left text-white uppercase dark:text-gray-400"
                                                >
                                                    Attachment/s
                                                </th>

                                                <th
                                                    scope="col"
                                                    class="p-4 text-xs font-extrabold text-left text-white uppercase dark:text-gray-400"
                                                >
                                                    Status
                                                </th>
                                                <!-- v-show="
                                                        props.activeevents.data.some(
                                                            (event) =>
                                                                event.status ===
                                                                'Accept' || 
                                                        )
                                                    " -->
                                                <th
                                                    scope="col"
                                                    class="p-4 text-xs font-extrabold text-left text-white uppercase dark:text-gray-400"
                                                >
                                                    Payment Status
                                                </th>

                                                <th
                                                    scope="col"
                                                    class="p-4 text-xs font-extrabold text-left text-white uppercase dark:text-gray-400"
                                                >
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            v-for="event in activeevents.data"
                                            :key="event.id"
                                            class="bg-white divide-y divide-gray-200 bg-gray-1000 dark:bg-gray-800 dark:divide-gray-700"
                                        >
                                            <tr
                                                class="hover:bg-gray-100 dark:hover:bg-gray-700"
                                            >
                                                <td
                                                    @click="
                                                        toggleDetails(event.id)
                                                    "
                                                    class="p-4 text-sm font-normal text-gray-700 whitespace-nowrap dark:text-gray-400 cursor-pointer"
                                                >
                                                    <div
                                                        class="text-base font-semibold text-gray-900 dark:text-white"
                                                    >
                                                        {{ event.event }}
                                                    </div>
                                                    <div
                                                        class="text-sm font-normal text-gray-600 dark:text-gray-400"
                                                    >
                                                        {{ event.theme }}
                                                    </div>
                                                </td>
                                                <td
                                                    class="p-4 text-sm font-normal text-gray-600 whitespace-nowrap dark:text-gray-400"
                                                >
                                                    <div
                                                        class="text-base font-normal text-gray-900 dark:text-white"
                                                    >
                                                        {{
                                                            formatDate(
                                                                event.date
                                                            )
                                                        }}
                                                    </div>
                                                    <div
                                                        class="text-base font-normal text-gray-600 dark:text-white"
                                                    >
                                                        {{ event.time }}
                                                    </div>
                                                </td>
                                                <td
                                                    class="p-4 text-sm font-normal text-gray-600 whitespace-nowrap dark:text-gray-400"
                                                >
                                                    <div
                                                        class="text-base font-normal text-gray-900 dark:text-white"
                                                    >
                                                        {{
                                                            event.contactperson
                                                        }}
                                                    </div>
                                                    <div
                                                        class="text-base font-normal text-gray-600 dark:text-white"
                                                    >
                                                        {{ event.contactno }}
                                                    </div>
                                                    <div
                                                        class="text-sm font-normal text-gray-600 dark:text-white"
                                                    >
                                                        {{ event.email }}
                                                    </div>
                                                </td>
                                                <td
                                                    class="max-w-sm p-4 overflow-hidden text-base font-normal text-gray-600 xl:max-w-xs dark:text-gray-400"
                                                >
                                                    <div v-if="event.images">
                                                        <div
                                                            v-for="(
                                                                image, index
                                                            ) in JSON.parse(
                                                                event.images
                                                            )"
                                                            :key="index"
                                                        >
                                                            <img
                                                                :src="image"
                                                                :alt="
                                                                    'Image ' +
                                                                    (index + 1)
                                                                "
                                                                width="100"
                                                                class="p-1"
                                                            />
                                                        </div>
                                                    </div>
                                                </td>
                                                <td
                                                    class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                                >
                                                    <span
                                                        :class="{
                                                            'bg-yellow-7 text-yellow-77 font-semibold me-2 px-2.5 py-0.5 rounded-full dark:bg-yellow-7 dark:text-yellow-77':
                                                                event.status ===
                                                                'Pending',
                                                            'bg-green-7 text-green-77 font-semibold me-2 px-2.5 py-0.5 rounded-full dark:bg-green-7 dark:text-green-77':
                                                                event.status ===
                                                                    'Accept' &&
                                                                event.payment_status !==
                                                                    'paid',
                                                            'bg-red-7 text-red-77 font-semibold me-2 px-2.5 py-0.5 rounded-full dark:bg-red-7900 dark:text-red-77':
                                                                event.status ===
                                                                    'Decline' ||
                                                                event.status ===
                                                                    'Cancel',
                                                            'bg-blue-7 text-blue-77 font-semibold me-2 px-2.5 py-0.5 rounded-full dark:bg-blue-7 dark:text-blue-77':
                                                                event.status ===
                                                                    'Accept' &&
                                                                event.payment_status ===
                                                                    'paid',
                                                        }"
                                                        class="px-2 py-1 rounded"
                                                    >
                                                        {{
                                                            event.status ===
                                                                "Accept" &&
                                                            event.payment_status ===
                                                                "paid"
                                                                ? "Listed"
                                                                : event.status ===
                                                                  "Accept"
                                                                ? "Accepted"
                                                                : event.status ===
                                                                  "Decline"
                                                                ? "Declined"
                                                                : event.status ===
                                                                  "Pending"
                                                                ? "Pending"
                                                                : event.status ===
                                                                  "Cancel"
                                                                ? "Cancelled"
                                                                : ""
                                                        }}
                                                    </span>
                                                </td>
                                                <td
                                                    v-show="
                                                        event.status ===
                                                            'Accept' ||
                                                        (event.status ===
                                                            'Cancel' &&
                                                            event.payment_status ===
                                                                'Pending') ||
                                                        (event.status ===
                                                            'Pending' &&
                                                            event.payment_status ===
                                                                'Pending')
                                                    "
                                                    class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                                >
                                                    <span
                                                        :class="{
                                                            'bg-gray-400 text-gray-200 font-semibold me-2 px-2.5 py-0.5 rounded-full dark:bg-gray-900 dark:text-gray-200':
                                                                (event.status ===
                                                                    'Cancel' &&
                                                                    event.payment_status ===
                                                                        'Pending') ||
                                                                (event.status ===
                                                                    'Pending' &&
                                                                    event.payment_status ===
                                                                        'Pending'),
                                                            'bg-yellow-7 text-yellow-77 font-semibold me-2 px-2.5 py-0.5 rounded-full dark:bg-yellow-7 dark:text-yellow-77':
                                                                event.payment_status ===
                                                                    'Pending' &&
                                                                event.status !==
                                                                    'Pending',
                                                            'bg-green-7 text-green-77 font-semibold me-2 px-2.5 py-0.5 rounded-full dark:bg-green-7 dark:text-green-77':
                                                                event.payment_status ===
                                                                'paid',
                                                        }"
                                                        class="px-2 py-1 rounded"
                                                    >
                                                        {{
                                                            (event.status ===
                                                                "Cancel" &&
                                                                event.payment_status ===
                                                                    "Pending") ||
                                                            (event.status ===
                                                                "Pending" &&
                                                                event.payment_status ===
                                                                    "Pending")
                                                                ? "Not Applicable"
                                                                : event.payment_status ===
                                                                  "paid"
                                                                ? "Paid"
                                                                : event.payment_status ===
                                                                  "Pending"
                                                                ? "Pending"
                                                                : ""
                                                        }}
                                                    </span>
                                                </td>
                                                <td
                                                    class="p-4 space-x-2 whitespace-nowrap"
                                                >
                                                    <div
                                                        class="flex flex-col items-center justify-center text-center gap-y-2"
                                                    >
                                                        <button
                                                            v-if="
                                                                event.status !==
                                                                'Cancel'
                                                            "
                                                            @click="
                                                                openConfirmationModal(
                                                                    event.id
                                                                )
                                                            "
                                                            class="inline-flex items-center text-xs text-white rounded-md bg-red-500 p-2 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-red-300 dark:hover:bg-red-400 dark:focus:ring-red-200"
                                                            title="Cancel Event"
                                                        >
                                                            <div
                                                                class="flex items-center space-x-1"
                                                            >
                                                                <svg
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    viewBox="0 0 24 24"
                                                                    fill="none"
                                                                    stroke="currentColor"
                                                                    stroke-width="4"
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    class="w-4 h-4"
                                                                >
                                                                    <line
                                                                        x1="18"
                                                                        y1="6"
                                                                        x2="6"
                                                                        y2="18"
                                                                    ></line>
                                                                    <line
                                                                        x1="6"
                                                                        y1="6"
                                                                        x2="18"
                                                                        y2="18"
                                                                    ></line>
                                                                </svg>
                                                                <span
                                                                    >CANCEL
                                                                    EVENT</span
                                                                >
                                                            </div>
                                                        </button>

                                                        <button
                                                            @click="
                                                                toggleDetails(
                                                                    event.id
                                                                )
                                                            "
                                                            class="inline-flex items-center text-xs text-white rounded-md bg-gray-500 p-2 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-gray-300 dark:hover:bg-gray-400 dark:focus:ring-gray-200"
                                                        >
                                                            <div
                                                                class="flex items-center space-x-1"
                                                            >
                                                                <svg
                                                                    class="w-3 h-3"
                                                                    :class="{
                                                                        'rotate-180':
                                                                            isDetailsVisible(
                                                                                event.id
                                                                            ),
                                                                    }"
                                                                    aria-hidden="true"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    fill="none"
                                                                    viewBox="0 0 10 6"
                                                                >
                                                                    <path
                                                                        stroke="white"
                                                                        stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="m1 1 4 4 4-4"
                                                                    />
                                                                </svg>
                                                                <span
                                                                    >OTHER
                                                                    DETAILS</span
                                                                >
                                                            </div>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Ensure the detailed row is within the same v-for loop -->
                                            <tr
                                                v-if="
                                                    isDetailsVisible(event.id)
                                                "
                                                :key="'details-' + event.id"
                                                class="bg-gray-50 dark:bg-gray-700"
                                            >
                                                <td colspan="7" class="p-2">
                                                    <table
                                                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-600"
                                                    >
                                                        <thead
                                                            class="bg-gray-100 dark:bg-gray-700"
                                                        >
                                                            <tr>
                                                                <th
                                                                    scope="col"
                                                                    class="p-4 text-xs font-extrabold text-left text-gray-700 uppercase dark:text-gray-400"
                                                                >
                                                                    Address
                                                                </th>
                                                                <th
                                                                    scope="col"
                                                                    class="p-4 text-xs font-extrabold text-left text-gray-700 uppercase dark:text-gray-400"
                                                                >
                                                                    Package
                                                                </th>
                                                                <th
                                                                    scope="col"
                                                                    class="p-4 text-xs font-extrabold text-left text-gray-700 uppercase dark:text-gray-400"
                                                                >
                                                                    Backdrop
                                                                </th>
                                                                <th
                                                                    scope="col"
                                                                    class="p-4 text-xs font-extrabold text-left text-gray-700 uppercase dark:text-gray-400"
                                                                >
                                                                    Suggestion
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td
                                                                    class="p-4 text-sm font-normal bg-white text-gray-900 dark:text-gray-400"
                                                                >
                                                                    {{
                                                                        event.street
                                                                    }},
                                                                    {{
                                                                        event.barangay
                                                                    }},
                                                                    {{
                                                                        event.city
                                                                    }},
                                                                    {{
                                                                        event.province
                                                                    }}
                                                                </td>
                                                                <td
                                                                    class="p-4 text-sm font-normal bg-white text-gray-900 dark:text-gray-400"
                                                                >
                                                                    <div>
                                                                        <strong
                                                                            >Package:</strong
                                                                        >
                                                                        {{
                                                                            event.packagename
                                                                        }}
                                                                    </div>
                                                                    <div>
                                                                        <strong
                                                                            >Price:</strong
                                                                        >
                                                                        {{
                                                                            event.price
                                                                        }}
                                                                    </div>
                                                                    <div>
                                                                        <strong
                                                                            >Number
                                                                            of
                                                                            Shots:</strong
                                                                        >
                                                                        {{
                                                                            event.number_of_shots
                                                                        }}
                                                                    </div>
                                                                </td>
                                                                <td
                                                                    class="p-4 text-sm font-normal bg-white text-gray-900 dark:text-gray-400"
                                                                >
                                                                    <div>
                                                                        <strong
                                                                            >Backdrop
                                                                            Type:</strong
                                                                        >
                                                                        {{
                                                                            event.backdroptype
                                                                        }}
                                                                    </div>
                                                                    <div>
                                                                        <strong
                                                                            >Backdrop
                                                                            Color:</strong
                                                                        >
                                                                        {{
                                                                            event.backdropcolor
                                                                        }}
                                                                    </div>
                                                                </td>
                                                                <td
                                                                    class="p-4 text-sm font-normal bg-white text-gray-900 dark:text-gray-400"
                                                                >
                                                                    {{
                                                                        event.suggestion
                                                                    }}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tr
                                            v-if="
                                                activeevents.data.length === 0
                                            "
                                        >
                                            <td
                                                colspan="12"
                                                class="p-4 text-center text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400"
                                            >
                                                No records found
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div v-if="view === 'archived'">
                                    <table
                                        class="min-w-full divide-y border divide-gray-200 table-fixed dark:divide-gray-600"
                                    >
                                        <thead
                                            class="bg-primary-600 dark:bg-gray-700"
                                        >
                                            <tr>
                                                <th
                                                    scope="col"
                                                    class="p-4 text-xs font-extrabold text-left text-white uppercase dark:text-gray-400"
                                                >
                                                    Event Title
                                                </th>
                                                <th
                                                    scope="col"
                                                    class="p-4 text-xs font-extrabold text-left text-white uppercase dark:text-gray-400"
                                                >
                                                    Date & Time
                                                </th>
                                                <th
                                                    scope="col"
                                                    class="p-4 text-xs font-extrabold text-left text-white uppercase dark:text-gray-400"
                                                >
                                                    Contact Person
                                                </th>

                                                <th
                                                    scope="col"
                                                    class="p-4 text-xs font-extrabold text-left text-white uppercase dark:text-gray-400"
                                                >
                                                    Attachment/s
                                                </th>

                                                <th
                                                    scope="col"
                                                    class="p-4 text-xs font-extrabold text-left text-white uppercase dark:text-gray-400"
                                                >
                                                    Status
                                                </th>

                                                <th
                                                    scope="col"
                                                    class="p-4 text-xs font-extrabold text-left text-white uppercase dark:text-gray-400"
                                                >
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            v-for="event in archivedevents.data"
                                            :key="event.id"
                                            class="bg-white divide-y divide-gray-200 bg-gray-1000 dark:bg-gray-800 dark:divide-gray-700"
                                        >
                                            <tr
                                                class="hover:bg-gray-100 dark:hover:bg-gray-700"
                                            >
                                                <td
                                                    @click="
                                                        toggleDetails(event.id)
                                                    "
                                                    class="p-4 text-sm font-normal text-gray-700 whitespace-nowrap dark:text-gray-400 cursor-pointer"
                                                >
                                                    <div
                                                        class="text-base font-semibold text-gray-900 dark:text-white"
                                                    >
                                                        {{ event.event }}
                                                    </div>
                                                    <div
                                                        class="text-sm font-normal text-gray-600 dark:text-gray-400"
                                                    >
                                                        {{ event.theme }}
                                                    </div>
                                                </td>
                                                <td
                                                    class="p-4 text-sm font-normal text-gray-600 whitespace-nowrap dark:text-gray-400"
                                                >
                                                    <div
                                                        class="text-base font-normal text-gray-900 dark:text-white"
                                                    >
                                                        {{
                                                            formatDate(
                                                                event.date
                                                            )
                                                        }}
                                                    </div>
                                                    <div
                                                        class="text-base font-normal text-gray-600 dark:text-white"
                                                    >
                                                        {{ event.time }}
                                                    </div>
                                                </td>
                                                <td
                                                    class="p-4 text-sm font-normal text-gray-600 whitespace-nowrap dark:text-gray-400"
                                                >
                                                    <div
                                                        class="text-base font-normal text-gray-900 dark:text-white"
                                                    >
                                                        {{
                                                            event.contactperson
                                                        }}
                                                    </div>
                                                    <div
                                                        class="text-base font-normal text-gray-600 dark:text-white"
                                                    >
                                                        {{ event.contactno }}
                                                    </div>
                                                    <div
                                                        class="text-sm font-normal text-gray-600 dark:text-white"
                                                    >
                                                        {{ event.email }}
                                                    </div>
                                                </td>
                                                <td
                                                    class="max-w-sm p-4 overflow-hidden text-base font-normal text-gray-600 xl:max-w-xs dark:text-gray-400"
                                                >
                                                    <div v-if="event.images">
                                                        <div
                                                            v-for="(
                                                                image, index
                                                            ) in JSON.parse(
                                                                event.images
                                                            )"
                                                            :key="index"
                                                        >
                                                            <img
                                                                :src="image"
                                                                :alt="
                                                                    'Image ' +
                                                                    (index + 1)
                                                                "
                                                                width="100"
                                                                class="p-1"
                                                            />
                                                        </div>
                                                    </div>
                                                </td>
                                                <td
                                                    class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                                >
                                                    <span
                                                        :class="{
                                                            'bg-red-7 text-red-77 font-semibold me-2 px-2.5 py-0.5 rounded-full dark:bg-red-7900 dark:text-red-77':
                                                                event.status ===
                                                                    'Decline' ||
                                                                event.status ===
                                                                    'Cancel',
                                                            'bg-blue-7 text-blue-77 font-semibold me-2 px-2.5 py-0.5 rounded-full dark:bg-blue-7 dark:text-blue-77':
                                                                event.status ===
                                                                    'Complete' &&
                                                                event.payment_status ===
                                                                    'paid',
                                                        }"
                                                        class="px-2 py-1 rounded"
                                                    >
                                                        {{
                                                            event.status ===
                                                                "Complete" &&
                                                            event.payment_status ===
                                                                "paid"
                                                                ? "Complete"
                                                                : event.status ===
                                                                  "Decline"
                                                                ? "Declined"
                                                                : event.status ===
                                                                  "Cancel"
                                                                ? "Cancelled"
                                                                : ""
                                                        }}
                                                    </span>
                                                </td>
                                                <td
                                                    v-show="
                                                        event.status ===
                                                            'Accept' ||
                                                        (event.status ===
                                                            'Cancel' &&
                                                            event.payment_status ===
                                                                'Pending') ||
                                                        (event.status ===
                                                            'Pending' &&
                                                            event.payment_status ===
                                                                'Pending')
                                                    "
                                                    class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                                ></td>
                                                <td
                                                    class="p-4 space-x-2 whitespace-nowrap"
                                                >
                                                    <div
                                                        class="flex flex-col items-center justify-center text-center gap-y-2"
                                                    >
                                                        <button
                                                            @click="
                                                                toggleDetails(
                                                                    event.id
                                                                )
                                                            "
                                                            class="inline-flex items-center text-xs text-white rounded-md bg-gray-500 p-2 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-gray-300 dark:hover:bg-gray-400 dark:focus:ring-gray-200"
                                                        >
                                                            <div
                                                                class="flex items-center space-x-1"
                                                            >
                                                                <svg
                                                                    class="w-3 h-3"
                                                                    :class="{
                                                                        'rotate-180':
                                                                            isDetailsVisible(
                                                                                event.id
                                                                            ),
                                                                    }"
                                                                    aria-hidden="true"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    fill="none"
                                                                    viewBox="0 0 10 6"
                                                                >
                                                                    <path
                                                                        stroke="white"
                                                                        stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="m1 1 4 4 4-4"
                                                                    />
                                                                </svg>
                                                                <span
                                                                    >OTHER
                                                                    DETAILS</span
                                                                >
                                                            </div>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Ensure the detailed row is within the same v-for loop -->
                                            <tr
                                                v-if="
                                                    isDetailsVisible(event.id)
                                                "
                                                :key="'details-' + event.id"
                                                class="bg-gray-50 dark:bg-gray-700"
                                            >
                                                <td colspan="7" class="p-2">
                                                    <table
                                                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-600"
                                                    >
                                                        <thead
                                                            class="bg-gray-100 dark:bg-gray-700"
                                                        >
                                                            <tr>
                                                                <th
                                                                    scope="col"
                                                                    class="p-4 text-xs font-extrabold text-left text-gray-700 uppercase dark:text-gray-400"
                                                                >
                                                                    Address
                                                                </th>
                                                                <th
                                                                    scope="col"
                                                                    class="p-4 text-xs font-extrabold text-left text-gray-700 uppercase dark:text-gray-400"
                                                                >
                                                                    Package
                                                                </th>
                                                                <th
                                                                    scope="col"
                                                                    class="p-4 text-xs font-extrabold text-left text-gray-700 uppercase dark:text-gray-400"
                                                                >
                                                                    Backdrop
                                                                </th>
                                                                <th
                                                                    scope="col"
                                                                    class="p-4 text-xs font-extrabold text-left text-gray-700 uppercase dark:text-gray-400"
                                                                >
                                                                    Suggestion
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td
                                                                    class="p-4 text-sm font-normal bg-white text-gray-900 dark:text-gray-400"
                                                                >
                                                                    {{
                                                                        event.street
                                                                    }},
                                                                    {{
                                                                        event.barangay
                                                                    }},
                                                                    {{
                                                                        event.city
                                                                    }},
                                                                    {{
                                                                        event.province
                                                                    }}
                                                                </td>
                                                                <td
                                                                    class="p-4 text-sm font-normal bg-white text-gray-900 dark:text-gray-400"
                                                                >
                                                                    <div>
                                                                        <strong
                                                                            >Package:</strong
                                                                        >
                                                                        {{
                                                                            event.packagename
                                                                        }}
                                                                    </div>
                                                                    <div>
                                                                        <strong
                                                                            >Price:</strong
                                                                        >
                                                                        {{
                                                                            event.price
                                                                        }}
                                                                    </div>
                                                                    <div>
                                                                        <strong
                                                                            >Number
                                                                            of
                                                                            Shots:</strong
                                                                        >
                                                                        {{
                                                                            event.number_of_shots
                                                                        }}
                                                                    </div>
                                                                </td>
                                                                <td
                                                                    class="p-4 text-sm font-normal bg-white text-gray-900 dark:text-gray-400"
                                                                >
                                                                    <div>
                                                                        <strong
                                                                            >Backdrop
                                                                            Type:</strong
                                                                        >
                                                                        {{
                                                                            event.backdroptype
                                                                        }}
                                                                    </div>
                                                                    <div>
                                                                        <strong
                                                                            >Backdrop
                                                                            Color:</strong
                                                                        >
                                                                        {{
                                                                            event.backdropcolor
                                                                        }}
                                                                    </div>
                                                                </td>
                                                                <td
                                                                    class="p-4 text-sm font-normal bg-white text-gray-900 dark:text-gray-400"
                                                                >
                                                                    {{
                                                                        event.suggestion
                                                                    }}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tr
                                            v-if="
                                                archivedevents.data.length === 0
                                            "
                                        >
                                            <td
                                                colspan="12"
                                                class="p-4 text-center text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400"
                                            >
                                                No records found
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="view === 'active'"
                        class="sticky bottom-0 right-0 items-center z-0 w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700"
                    >
                        <div class="flex items-center mb-4 sm:mb-0">
                            <span
                                class="text-sm font-normal text-gray-500 dark:text-gray-400"
                                >Showing
                                <span
                                    class="font-semibold text-gray-700 dark:text-white"
                                    >{{ activepagination.from }} to
                                    {{ activepagination.to }}</span
                                >
                                of
                                <span
                                    class="font-semibold text-gray-700 dark:text-white"
                                >
                                    {{ activepagination.total }}
                                </span>
                                results</span
                            >
                        </div>
                        <div class="flex items-center space-x-3">
                            <nav>
                                <ul
                                    class="flex items-center -space-x-px h-10 text-base"
                                >
                                    <li>
                                        <button
                                            @click="
                                                activeChangePage(
                                                    activepagination.current_page -
                                                        1
                                                )
                                            "
                                            :disabled="
                                                activepagination.current_page ===
                                                1
                                            "
                                            :class="[
                                                'flex items-center justify-center px-4 h-10 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg',
                                                activepagination.current_page ===
                                                1
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
                                    <li
                                        v-for="page in activepagination.last_page"
                                        :key="page"
                                    >
                                        <button
                                            @click="activeChangePage(page)"
                                            :class="[
                                                'flex items-center justify-center px-4 h-10 leading-tight text-gray-500 border border-gray-300  dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400',
                                                page ===
                                                activepagination.current_page
                                                    ? 'bg-pink-400 text-white border-pink-400'
                                                    : 'hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-700 dark:hover:text-white',
                                            ]"
                                        >
                                            {{ page }}
                                        </button>
                                    </li>
                                    <li>
                                        <button
                                            @click="
                                                activeChangePage(
                                                    activepagination.current_page +
                                                        1
                                                )
                                            "
                                            :disabled="
                                                activepagination.current_page ===
                                                activepagination.last_page
                                            "
                                            :class="[
                                                'flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg ',
                                                activepagination.current_page ===
                                                activepagination.last_page
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

                    <div
                        v-if="view === 'archived'"
                        class="sticky bottom-0 right-0 items-center z-0 w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700"
                    >
                        <div class="flex items-center mb-4 sm:mb-0">
                            <span
                                class="text-sm font-normal text-gray-500 dark:text-gray-400"
                                >Showing
                                <span
                                    class="font-semibold text-gray-700 dark:text-white"
                                    >{{ archivedpagination.from }} to
                                    {{ archivedpagination.to }}</span
                                >
                                of
                                <span
                                    class="font-semibold text-gray-700 dark:text-white"
                                >
                                    {{ archivedpagination.total }}
                                </span>
                                results</span
                            >
                        </div>
                        <div class="flex items-center space-x-3">
                            <nav>
                                <ul
                                    class="flex items-center -space-x-px h-10 text-base"
                                >
                                    <li>
                                        <button
                                            @click="
                                                archivedChangePage(
                                                    archivedpagination.current_page -
                                                        1
                                                )
                                            "
                                            :disabled="
                                                archivedpagination.current_page ===
                                                1
                                            "
                                            :class="[
                                                'flex items-center justify-center px-4 h-10 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg',
                                                archivedpagination.current_page ===
                                                1
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
                                    <li
                                        v-for="page in archivedpagination.last_page"
                                        :key="page"
                                    >
                                        <button
                                            @click="archivedChangePage(page)"
                                            :class="[
                                                'flex items-center justify-center px-4 h-10 leading-tight text-gray-500 border border-gray-300  dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400',
                                                page ===
                                                archivedpagination.current_page
                                                    ? 'bg-pink-400 text-white border-pink-400'
                                                    : 'hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-700 dark:hover:text-white',
                                            ]"
                                        >
                                            {{ page }}
                                        </button>
                                    </li>
                                    <li>
                                        <button
                                            @click="
                                                archivedChangePage(
                                                    archivedpagination.current_page +
                                                        1
                                                )
                                            "
                                            :disabled="
                                                archivedpagination.current_page ===
                                                archivedpagination.last_page
                                            "
                                            :class="[
                                                'flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg ',
                                                archivedpagination.current_page ===
                                                archivedpagination.last_page
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
                </div>
            </div>
        </div>

        <!-- CONFIRMATION MODAL -->
        <ConfirmationModal
            :show="confirmationModal"
            @close="closeConfirmationModal"
        >
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
                            @click="closeConfirmationModal"
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
                        <h3 class="mt-5 mb-8 text-gray-700 dark:text-gray-400">
                            Are you sure you want to cancel this event?
                            <!-- "Cancelling this event will delete your registration and remove it from your list. Are you sure you want to proceed?" -->
                            <!-- By cancelling this event, your reserved slot will be released and may not be available again. Are you sure you want to proceed -->
                            <!-- By cancelling this event, your registration will be removed, and the down payment you made is non-refundable as per our policy. Are you sure you want to continue? -->
                        </h3>
                        <!-- Modal footer -->
                        <div class="mt-8 flex justify-center gap-6">
                            <SecondaryButton
                                class="uppercase px-5"
                                @click="closeConfirmationModal"
                            >
                                No, go back
                            </SecondaryButton>

                            <DangerButton
                                class="uppercase px-5 text-xs"
                                :class="{
                                    'opacity-25': updatingEventStatus,
                                }"
                                :disabled="updatingEventStatus"
                                @click="handleConfirm"
                            >
                                Yes, I want to cancel
                            </DangerButton>
                        </div>
                    </div>
                </div>
            </div>
        </ConfirmationModal>
    </AuthenticatedLayout>
</template>
