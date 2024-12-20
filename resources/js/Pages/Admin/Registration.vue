<script setup>
import AdminAuthenticatedLayout from "@/Layouts/AdminAuthenticatedLayout.vue";
import Modal from "@/Components/Modal.vue";
import { Head, useForm, router, usePage } from "@inertiajs/vue3";
import AButton from "@/Components/AButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { computed, ref, watch } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { Inertia } from "@inertiajs/inertia";
import Toast from "@/Components/Toast.vue";
import "../../../css/admin-vuecal.css";

const props = defineProps({
    events: {
        type: [Array, Object],
        required: true,
    },
    getEvents: {
        type: Array,
        required: true,
    },
});

console.log(props.getEvents);

/* #############################
            PAGINATION
   ############################# */

const paginationevents = ref(props.events.data);
const currentPage = ref(1);

const pagination = ref({
    total: props.events.total,
    per_page: props.events.per_page,
    current_page: props.events.current_page,
    last_page: props.events.last_page,
    from: props.events.from,
    to: props.events.to,
});

const changePage = (page) => {
    Inertia.get(
        route("event.index", { page }),
        {},
        {
            preserveScroll: true,
            preserveState: true,
        }
    );
};

const activePage = computed(() => {
    const activePageItem = props.events.links.find((link) => link.active);
    return activePageItem
        ? new URL(activePageItem.url).searchParams.get("page")
        : 1;
});

watch(
    () => props.events,
    (newPackages) => {
        paginationevents.value = newPackages.data;
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

/* #############################
        UPDATE EVENT STATUS
   ############################# */
const updatingEventStatus = ref(false);

const form = useForm({
    id: "",
    user_id: "",
    status: "",
    originalStatus: "",
});

const updateEventStatus = (event) => {
    form.id = event.id;
    form.user_id = event.user_id;
    form.status = event.status;
    form.originalStatus = event.status;
    updatingEventStatus.value = true;
};

const updateStatus = () => {
    form.patch(route("event.update", { event: form.id }), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    updatingEventStatus.value = false;
    form.reset();
};

const statusOptions = ["Pending", "Accept", "Decline"];

const isSameStatus = computed(() => {
    return form.status === form.originalStatus;
});

/* #############################
            SEARCH
   ############################# */
let search = ref(usePage().props.search),
    pageNumber = ref(1);

let eventUrl = computed(() => {
    let url = new URL(route("event.index"));
    url.searchParams.append("page", pageNumber.value);

    if (search.value) {
        url.searchParams.append("search", search.value);
    }

    return url;
});

watch(
    () => eventUrl.value,
    (updatedEventUrl) => {
        router.visit(updatedEventUrl, {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        });
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

function formatTime(dateStr) {
    const date = new Date(dateStr);
    let hours = date.getHours();
    const minutes = date.getMinutes();
    const ampm = hours >= 12 ? "PM" : "AM";
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    const minutesStr = minutes < 10 ? "0" + minutes : minutes;
    return `${hours}:${minutesStr} ${ampm}`;
}

let view = ref("table");

/* #############################
            VUECAL CODES
    ############################# */

// MIN DATES FOR INPUT DATE
const minInputDate = computed(() => {
    const date = new Date();
    date.setDate(date.getDate() + 1);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");
    return `${year}-${month}-${day}`;
});

// MIN DATES VUECAL
const minDate = computed(() => {
    const date = new Date();
    date.setDate(date.getDate());
    return date;
});

// SPLIT DAYS AND EVENTS VUECAL
const splitsAndEvents = {
    splits: [],
    events: [],
};

if (props.getEvents && Array.isArray(props.getEvents)) {
    // Split end times
    const splitEndTimes = {
        1: new Date(0),
    };

    // Filter events that are after the min date
    splitsAndEvents.events = props.getEvents
        .filter((event) => {
            const startDateTime = new Date(`${event.date}T${event.time}`);
            return startDateTime > minDate.value;
        })
        .flatMap((event) => {
            const startDateTime = new Date(`${event.date}T${event.time}`);
            const endDateTime = new Date(
                startDateTime.getTime() + 2 * 60 * 60 * 1000
            );

            // If the start time is after 10pm, the end time will be the next day
            if (startDateTime.getHours() >= 22) {
                endDateTime.setDate(endDateTime.getDate());
            }

            // Add 1-hour allowance before and after the event
            const allowanceStartDateTime = new Date(
                startDateTime.getTime() - 1 * 60 * 60 * 1000
            );
            const allowanceEndDateTime = new Date(
                endDateTime.getTime() + 1 * 60 * 60 * 1000
            );

            const endHours = String(endDateTime.getHours()).padStart(2, "0");
            const endMinutes = String(endDateTime.getMinutes()).padStart(
                2,
                "0"
            );
            const end = `${endDateTime.getFullYear()}-${String(
                endDateTime.getMonth() + 1
            ).padStart(2, "0")}-${String(endDateTime.getDate()).padStart(
                2,
                "0"
            )} ${endHours}:${endMinutes}`;

            let split = 1;

            const event_uid = event.id;
            splitEndTimes[split] = endDateTime;

            return [
                {
                    start: allowanceStartDateTime,
                    end: startDateTime,
                    title: "",
                    class: "allowance",
                    split: split,
                    background: true,
                },
                {
                    id: event_uid,
                    start: startDateTime,
                    end: endDateTime,
                    title: event.event,
                    class: "primary",
                    split: split,
                    // background: true,
                },
                {
                    start: endDateTime,
                    end: allowanceEndDateTime,
                    title: "",
                    class: "allowance",
                    split: split,
                    background: true,
                },
            ];
        });
}

// MODAL
const showDialog = ref(false);
const selectedEvent = ref({});

const onEventClick = (event) => {
    if (event.title) {
        const eventId = event.id; // Use the title (which is the event ID) to find the event
        selectedEvent.value = props.getEvents.find((e) => e.id === eventId);
        console.log("Selected Events: ", selectedEvent.value);
        showDialog.value = true;
    }
};
</script>

<template>
    <Head title="Events" />

    <Toast />

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
                                        >Events</span
                                    >
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1
                        class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white"
                    >
                        All events
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
                                    aria-hidden="true"
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
                                class="ps-10 pr-10 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Search for events"
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
                    <div class="flex items-center align-center">
                        <button
                            @click="view = 'table'"
                            class="rounded-e-none flex justify-center align-center px-4 py-2 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest transition ease-in-out duration-150"
                            :class="{
                                'bg-primary-500 text-white focus:outline-none focus:ring-none focus:ring-0':
                                    view === 'table',
                                'text-gray-500  bg-primary-700':
                                    view !== 'table',
                            }"
                        >
                            Table
                        </button>
                        <button
                            @click="view = 'calendar'"
                            class="rounded-s-none flex justify-center align-center px-4 py-2 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest transition ease-in-out duration-150"
                            :class="{
                                'bg-primary-500 text-white focus:outline-none focus:ring-none focus:ring-0':
                                    view === 'calendar',
                                'text-gray-500 bg-primary-700':
                                    view !== 'calendar',
                            }"
                        >
                            Calendar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col mb-20">
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden shadow">
                        <div v-if="view === 'table'">
                            <table
                                class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600"
                            >
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400"
                                        >
                                            Event Title
                                        </th>
                                        <th
                                            scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400"
                                        >
                                            Date & Time
                                        </th>
                                        <th
                                            scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400"
                                        >
                                            Contact Person
                                        </th>
                                        <th
                                            scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400"
                                        >
                                            Address
                                        </th>
                                        <th
                                            scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400"
                                        >
                                            Package
                                        </th>

                                        <th
                                            scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400"
                                        >
                                            Backdrop
                                        </th>
                                        <th
                                            scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400"
                                        >
                                            Suggestion
                                        </th>
                                        <th
                                            scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400"
                                        >
                                            Attachment/s
                                        </th>
                                        <th
                                            scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400"
                                        >
                                            Status
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
                                        v-for="event in events.data"
                                        :key="event.id"
                                        class="hover:bg-gray-100 dark:hover:bg-gray-700"
                                    >
                                        <td
                                            class="p-4 text-sm font-normal text-gray-700 whitespace-nowrap dark:text-gray-400"
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
                                                {{ formatDate(event.date) }}
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
                                                {{ event.contactperson }}
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
                                            class="max-w-sm p-4 text-base font-normal text-gray-900 xl:max-w-[250px] dark:text-gray-400"
                                        >
                                            {{ event.street }},
                                            {{ event.barangay }},
                                            {{ event.city }},
                                            {{ event.province }}
                                        </td>
                                        <td
                                            class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                        >
                                            <div
                                                class="text-base font-semibold text-gray-900 dark:text-white"
                                            >
                                                {{ event.packagename }}
                                            </div>
                                            <div
                                                class="text-sm font-normal text-gray-600 dark:text-white"
                                            >
                                                {{ event.packagesize }} -
                                                {{ event.number_of_shots }}
                                                shots - ₱{{ event.price }}
                                            </div>
                                        </td>

                                        <td
                                            class="p-4 text-sm font-normal text-gray-600 whitespace-nowrap dark:text-gray-400"
                                        >
                                            <div
                                                class="text-base font-semibold text-gray-900 dark:text-white"
                                            >
                                                {{ event.backdroptype }}
                                            </div>
                                            <div
                                                class="text-sm font-normal text-gray-600 dark:text-gray-400"
                                            >
                                                {{ event.backdropcolor }}
                                            </div>
                                        </td>
                                        <td
                                            class="max-w-sm p-4 overflow-hidden text-base font-normal text-gray-600 xl:max-w-xs dark:text-gray-400"
                                        >
                                            {{ event.suggestion }}
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
                                                    'bg-green-7 text-green-77  font-semibold me-2 px-2.5 py-0.5 rounded-full dark:bg-green-7 dark:text-green-77':
                                                        event.status ===
                                                        'Accept',
                                                    'bg-red-7 text-red-77 font-semibold me-2 px-2.5 py-0.5 rounded-full dark:bg-red-7900 dark:text-red-77':
                                                        event.status ===
                                                        'Decline',
                                                }"
                                                class="px-2 py-1 rounded"
                                            >
                                                {{
                                                    event.status
                                                        ? event.status ===
                                                          "Accept"
                                                            ? "Accepted"
                                                            : event.status ===
                                                              "Decline"
                                                            ? "Declined"
                                                            : event.status ===
                                                              "Pending"
                                                            ? "Pending"
                                                            : ""
                                                        : ""
                                                }}
                                            </span>
                                        </td>
                                        <td
                                            class="p-4 space-x-2 whitespace-nowrap"
                                        >
                                            <button
                                                type="button"
                                                @click="
                                                    () =>
                                                        updateEventStatus(event)
                                                "
                                                id="updateProductButton"
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
                                            <!-- <button
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
                                        </button> -->
                                        </td>
                                    </tr>
                                    <!-- <tr v-if="events == 0">
                                <td
                                    colspan="12"
                                    class="text-center bg-gray-300"
                                >
                                    No events booked so far
                                </td>
                            </tr> -->
                                    <tr v-if="events.data.length === 0">
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
                v-if="view === 'table'"
                class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700"
            >
                <div class="flex items-center mb-4 sm:mb-0">
                    <span
                        class="text-sm font-normal text-gray-500 dark:text-gray-400"
                        >Showing
                        <span
                            class="font-semibold text-gray-700 dark:text-white"
                            >{{ pagination.from }} to {{ pagination.to }}</span
                        >
                        of
                        <span
                            class="font-semibold text-gray-700 dark:text-white"
                        >
                            {{ pagination.total }}
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
                                        changePage(pagination.current_page - 1)
                                    "
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
                            <li
                                v-for="page in pagination.last_page"
                                :key="page"
                            >
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
                                    @click="
                                        changePage(pagination.current_page + 1)
                                    "
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
            <div
                v-if="view === 'calendar'"
                class="flex justify-center align-center"
            >
                <VueCal
                    :events="splitsAndEvents.events"
                    class="vuecal"
                    events-count-on-year-view
                    today-button
                    sticky-split-labels
                    style="width: 150%; height: 655px; max-width: 1400px"
                    :disable-views="['day', 'years', 'year']"
                    active-view="month"
                    timeFormat="hh:mm {AM}"
                    :min-date="minDate"
                    :split-days="splitsAndEvents.splits"
                    :on-event-click="onEventClick"
                >
                </VueCal>
            </div>
        </div>

        <Modal :show="showDialog" @close="showDialog = false">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                <div
                    class="flex items-start justify-between p-5 border-b rounded-t dark:border-gray-700"
                >
                    <div
                        class="text-xl font-semibold dark:text-white flex justify-center align-center"
                    >
                        Event Details
                    </div>
                    <button
                        type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white"
                        @click="showDialog = false"
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
                <div class="flex align-center">
                    <div class="m-6">
                        <p class="text-gray-600">
                            Title of the Event:
                            <strong class="text-gray-800">{{
                                selectedEvent.event
                            }}</strong>
                        </p>
                        <p class="text-gray-600 mt-1">
                            Contact Person:
                            <strong class="text-gray-800">
                                {{ selectedEvent.contactperson }}</strong
                            >
                        </p>
                        <p class="text-gray-600 mt-1">
                            Email:
                            <strong class="text-gray-800">
                                {{ selectedEvent.email }}</strong
                            >
                        </p>
                        <p class="text-gray-600 mt-1">
                            Contact Number:
                            <strong class="text-gray-800">
                                {{ selectedEvent.contactno }}</strong
                            >
                        </p>
                        <p class="text-gray-600 mt-1">
                            Exact time of photo booth session:
                            <strong class="text-gray-800">
                                {{
                                    formatTime(
                                        selectedEvent.date +
                                            "T" +
                                            selectedEvent.time
                                    ) +
                                    " " +
                                    formatDate(selectedEvent.date)
                                }}</strong
                            >
                        </p>
                        <p class="text-gray-600 mt-1">
                            Location of the Event:
                            <strong class="text-gray-800">
                                {{
                                    selectedEvent.street +
                                    ", " +
                                    selectedEvent.barangay +
                                    ", " +
                                    selectedEvent.city +
                                    ", " +
                                    selectedEvent.province
                                }}
                            </strong>
                        </p>

                        <p class="text-gray-600 mt-1">
                            Package:
                            <strong class="text-gray-800">
                                {{
                                    selectedEvent.packagename +
                                    " - " +
                                    selectedEvent.alias
                                }}
                            </strong>
                        </p>

                        <p class="text-gray-600 mt-1">
                            Package Details:
                            <strong class="text-gray-800">
                                {{
                                    "₱" +
                                    selectedEvent.price +
                                    " - " +
                                    selectedEvent.packagesize +
                                    " - " +
                                    selectedEvent.number_of_shots +
                                    " shots"
                                }}
                            </strong>
                        </p>

                        <p class="text-gray-600 mt-1">
                            Backdrop Details:
                            <strong class="text-gray-800">
                                {{
                                    selectedEvent.backdroptype +
                                    " - " +
                                    selectedEvent.backdropcolor
                                }}</strong
                            >
                        </p>

                        <p class="text-gray-600 mt-1">
                            Theme:
                            <strong class="text-gray-800">
                                {{ selectedEvent.theme }}</strong
                            >
                        </p>
                        <p class="text-gray-600 mt-1">
                            Suggestion:
                            <strong class="text-gray-800">
                                {{ selectedEvent.suggestion }}</strong
                            >
                        </p>
                    </div>
                </div>
            </div>
        </Modal>

        <Modal :show="updatingEventStatus" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Are you sure you want to update the event status?
                </h2>

                <div class="mt-6">
                    <InputLabel for="status" value="Status" class="sr-only" />

                    <select
                        class="select select-info border-gray-300 mt-1 block w-full"
                        id="status"
                        name="status"
                        required
                        v-model="form.status"
                    >
                        <option disabled value="">Select Status</option>
                        <option
                            v-for="status in statusOptions"
                            :key="status"
                            :value="status"
                        >
                            {{ status }}
                        </option>
                    </select>

                    <InputError :message="form.errors.status" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal">
                        Cancel
                    </SecondaryButton>

                    <PrimaryButton
                        class="ms-3"
                        :class="{
                            'opacity-25': form.processing || isSameStatus,
                        }"
                        :disabled="form.processing || isSameStatus"
                        @click="updateStatus"
                    >
                        Update Status
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AdminAuthenticatedLayout>
</template>
