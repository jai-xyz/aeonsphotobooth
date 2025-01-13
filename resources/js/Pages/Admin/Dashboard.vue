<script setup>
import AdminAuthenticatedLayout from "@/Layouts/AdminAuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { onMounted, ref, computed } from "vue";
import ApexCharts from "apexcharts";
import "../../../css/custom-styles.css";
import "../../../css/admin-vuecal.css";
import Modal from "@/Components/Modal.vue";

const props = defineProps({
    eventsalelocation: {
        type: [Array, Object],
        required: true,
    },
    eventstatuspending: {
        type: Number,
        required: true,
    },
    eventstatusaccepted: {
        type: Number,
        required: true,
    },
    eventstatuslisted: {
        type: Number,
        required: true,
    },
    eventstatuscomplete: {
        type: Number,
        required: true,
    },
    usercount: {
        type: Number,
        required: true,
    },
    getEvents: {
        type: Array,
        required: true,
    },
});

const salesData = [
    15328, 9245, 14892, 25103, 27150, 12530, 29128, 21285, 18392, 15328, 29245,
    34892,
];

const totalSales = ref(salesData.reduce((acc, val) => acc + val, 0));

const options = {
    chart: {
        height: "100%",
        maxWidth: "100%",
        type: "area",
        fontFamily: "Poppins, sans-serif",
        dropShadow: {
            enabled: false,
        },
        toolbar: {
            show: false,
        },
    },
    tooltip: {
        enabled: true,
        x: {
            show: false,
        },
    },
    fill: {
        type: "gradient",
        gradient: {
            opacityFrom: 0.55,
            opacityTo: 0,
            shade: "#f57da9",
            gradientToColors: ["#f57da9"],
        },
    },
    dataLabels: {
        enabled: false,
    },
    stroke: {
        width: 6,
    },
    grid: {
        show: true,
        strokeDashArray: 4,
        padding: {
            left: 2,
            right: 2,
            top: 0,
        },
    },
    series: [
        {
            name: "Sales",
            data: salesData,
            color: "#f57da9",
        },
    ],
    xaxis: {
        categories: [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
        ],
        labels: {
            show: true,
        },
        axisBorder: {
            show: true,
        },
        axisTicks: {
            show: false,
        },
    },
    yaxis: {
        show: true,

        labels: {
            show: true,

            formatter: function (value) {
                return "₱" + value;
            },
        },
    },
};

const getChartOptions = () => {
    return {
        series: [
            props.eventstatuspending,
            props.eventstatusaccepted,
            props.eventstatuslisted,
        ],
        colors: ["#ffd43a", "#228200", "#158de3"],
        chart: {
            height: 100, // Reduced height
            width: 80, // Adjusted width to fit container
            type: "donut",
        },
        stroke: {
            colors: ["transparent"],
            lineCap: "",
        },
        plotOptions: {
            pie: {
                donut: {
                    labels: {
                        show: false,
                        name: {
                            show: true,
                            fontFamily: "Inter, sans-serif",
                            fontSize: "12px", // Smaller font size
                            offsetY: 20,
                        },
                        total: {
                            showAlways: true,
                            show: true,
                            label: "Unique visitors",
                            fontFamily: "Inter, sans-serif",
                            fontSize: "10px", // Smaller font size
                            formatter: function (w) {
                                const sum = w.globals.seriesTotals.reduce(
                                    (a, b) => {
                                        return a + b;
                                    },
                                    0
                                );
                                return "$" + sum + "k";
                            },
                        },
                        value: {
                            show: true,
                            fontFamily: "Poppins, sans-serif",
                            fontSize: "12px", // Smaller font size
                            offsetY: -20,
                            formatter: function (value) {
                                return value + "k";
                            },
                        },
                    },
                    size: "40%", // Reduced size
                },
            },
        },
        grid: {
            padding: {
                top: -2,
            },
        },
        labels: ["Pending", "Accepted", "Listed"],
        dataLabels: {
            enabled: false,
        },
        legend: {
            show: false,
            position: "bottom",
            fontFamily: "Inter, sans-serif",
        },
    };
};
onMounted(() => {
    const areaChartElement = document.getElementById("area-chart");
    if (areaChartElement && typeof ApexCharts !== "undefined") {
        const areaChart = new ApexCharts(areaChartElement, options);
        areaChart.render();
    }

    const donutChartElement = document.getElementById("donut-chart");
    if (donutChartElement && typeof ApexCharts !== "undefined") {
        const donutChart = new ApexCharts(donutChartElement, getChartOptions());
        donutChart.render();
    }
});

// VUECAL

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

/* #############################
        MODAL DIALOG
   ############################# */
const showDialog = ref(false);
const selectedEvent = ref({});

const onEventClick = (event) => {
    if (event.title) {
        const eventId = event.id; // Use the title (which is the event ID) to find the event
        selectedEvent.value = props.getEvents.find((e) => e.id === eventId);
        showDialog.value = true;
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AdminAuthenticatedLayout>
        <div class="px-4 pt-6">
            <div
                class="grid w-full grid-cols-1 gap-4 mb-4 xl:grid-cols-3 2xl:grid-cols-3"
            >
                <!-- EVENTS -->
                <div
                    class="flex items-start justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800"
                >
                    <div class="w-2/3">
                        <h3
                            class="font-semibold uppercase text-sm text-gray-500 dark:text-gray-400"
                        >
                            Events
                        </h3>
                        <span
                            class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white"
                            >{{ eventstatuscomplete }}</span
                        >

                        <span
                            class="flex items-center mr-1.5 text-xs text-green-500 dark:text-green-400"
                        >
                            <svg
                                class="w-4 h-4"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg"
                                aria-hidden="true"
                            >
                                <path
                                    clip-rule="evenodd"
                                    fill-rule="evenodd"
                                    d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0110 17z"
                                ></path>
                            </svg>
                            4%
                            <span class="text-gray-500 ms-1.5">
                                Since last month
                            </span>
                        </span>
                    </div>
                    <div class="w-1/3" id="new-products-chart">
                        <div class="flex items-start justify-end">
                            <div
                                class="bg-primary-800 flex items-center justify-center p-1.5 rounded-md"
                            >
                                <svg
                                    class="w-8 text-primary-50 dark:text-gray-400 flex items-center justify-center"
                                    fill="currentColor"
                                    viewBox="0 0 24 22"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M5 5a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1 2 2 0 0 1 2 2v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a2 2 0 0 1 2-2ZM3 19v-7a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Zm6.01-6a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm2 0a1 1 0 1 1 2 0 1 1 0 0 1-2 0Zm6 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm-10 4a1 1 0 1 1 2 0 1 1 0 0 1-2 0Zm6 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm2 0a1 1 0 1 1 2 0 1 1 0 0 1-2 0Z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- USERS -->
                <div
                    class="flex items-start justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800"
                >
                    <div class="w-2/3">
                        <h3
                            class="font-semibold uppercase text-sm text-gray-500 dark:text-gray-400"
                        >
                            Users
                        </h3>
                        <span
                            class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white"
                            >{{ usercount }}</span
                        >

                        <span
                            class="flex items-center mr-1.5 text-xs text-green-500 dark:text-green-400"
                        >
                            <svg
                                class="w-4 h-4"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg"
                                aria-hidden="true"
                            >
                                <path
                                    clip-rule="evenodd"
                                    fill-rule="evenodd"
                                    d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0110 17z"
                                ></path>
                            </svg>
                            4%
                            <span class="text-gray-500 ms-1.5">
                                Since last month
                            </span>
                        </span>
                    </div>
                    <div class="w-1/3" id="new-products-chart">
                        <div class="flex items-start justify-end">
                            <div
                                class="bg-primary-800 flex items-center justify-center p-1.5 rounded-md"
                            >
                                <svg
                                    class="w-8 text-primary-50 dark:text-gray-400 flex items-center justify-center"
                                    fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 640 512"
                                >
                                    <path
                                        fill="currentColor"
                                        d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192l42.7 0c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0L21.3 320C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7l42.7 0C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3l-213.3 0zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352l117.3 0C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7l-330.7 0c-14.7 0-26.7-11.9-26.7-26.7z"
                                    />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- EVENT STATUS -->

                <div
                    class="flex items-start justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800"
                >
                    <div class="w-2/3">
                        <h3
                            class="font-semibold uppercase text-sm text-gray-500 dark:text-gray-400"
                        >
                            REAL-TIME Event status
                        </h3>
                        <!-- PENDING -->
                        <span
                            class="flex justify-start items-center text-xs mb-1 text-green-500 dark:text-green-400"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                width="16"
                                height="16"
                                fill="#ffd43a"
                            >
                                <rect x="4" y="5" width="16" height="16" />
                            </svg>
                            <span class="text-gray-500 ms-1.5">
                                Pending
                                <strong class="text-gray-800">{{
                                    eventstatuspending >= 1
                                        ? " - " + eventstatuspending
                                        : ""
                                }}</strong></span
                            >
                        </span>
                        <!-- ACCEPTED -->
                        <span
                            class="flex justify-start items-center text-xs mb-1 text-green-500 dark:text-green-400"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                width="16"
                                height="16"
                                fill="#228200"
                            >
                                <rect x="4" y="5" width="16" height="16" />
                            </svg>

                            <span class="text-gray-500 ms-1.5">
                                Accepted
                                <strong class="text-gray-800">
                                    {{
                                        eventstatusaccepted >= 1
                                            ? " - " + eventstatusaccepted
                                            : ""
                                    }}
                                </strong></span
                            >
                        </span>
                        <!-- LISTED -->
                        <span
                            class="flex justify-start items-center text-xs text-green-500 dark:text-green-400"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                width="16"
                                height="16"
                                fill="#158de3"
                            >
                                <rect x="4" y="5" width="16" height="16" />
                            </svg>
                            <span class="text-gray-500 ms-1.5">
                                Listed
                                <strong class="text-gray-800">
                                    {{
                                        eventstatuslisted >= 1
                                            ? " - " + eventstatuslisted
                                            : ""
                                    }}
                                </strong></span
                            >
                        </span>
                    </div>
                    <div class="w-1/3">
                        <div class="flex items-start justify-end">
                            <div id="donut-chart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- REVENUE -->
            <div class="grid gap-4 xl:grid-cols-3 mb-4 2xl:grid-cols-3">
                <!-- Main widget -->
                <div
                    class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800"
                >
                    <div class="flex justify-between pb-4 mb-4">
                        <div class="flex items-center">
                            <div
                                class="bg-primary-800 flex items-center justify-center p-2 rounded-md me-3"
                            >
                                <svg
                                    class="w-8 text-primary-50 dark:text-gray-400 flex items-center justify-center"
                                    fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 576 512"
                                >
                                    <path
                                        d="M64 64C28.7 64 0 92.7 0 128L0 384c0 35.3 28.7 64 64 64l448 0c35.3 0 64-28.7 64-64l0-256c0-35.3-28.7-64-64-64L64 64zm64 320l-64 0 0-64c35.3 0 64 28.7 64 64zM64 192l0-64 64 0c0 35.3-28.7 64-64 64zM448 384c0-35.3 28.7-64 64-64l0 64-64 0zm64-192c-35.3 0-64-28.7-64-64l64 0 0 64zM288 160a96 96 0 1 1 0 192 96 96 0 1 1 0-192z"
                                    />
                                </svg>
                            </div>

                            <div>
                                <h5
                                    class="leading-none text-2xl font-bold text-gray-900 dark:text-white pb-1"
                                >
                                    ₱{{ totalSales }}
                                </h5>
                                <p
                                    class="font-semibold uppercase text-sm text-gray-500 dark:text-gray-400"
                                >
                                    Total sales
                                </p>
                            </div>
                        </div>
                        <div>
                            <span
                                class="bg-green-100 text-green-800 text-xs font-medium inline-flex items-center px-2.5 py-1 rounded-md dark:bg-green-900 dark:text-green-300"
                            >
                                <svg
                                    class="w-2.5 h-2.5 me-1.5"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 10 14"
                                >
                                    <path
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13V1m0 0L1 5m4-4 4 4"
                                    />
                                </svg>
                                42.5%
                            </span>
                        </div>
                    </div>

                    <div id="area-chart"></div>
                    <div
                        class="grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between"
                    >
                        <div
                            class="flex justify-between items-center pt-5"
                        ></div>
                    </div>
                </div>
                <!--Tabs widget -->

                <div
                    class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800"
                >
                    <div
                        class="items-center justify-between pb-4 sm:flex dark:border-gray-700"
                    >
                        <div class="w-full mb-4 sm:mb-0">
                            <h3
                                class="font-semibold uppercase text-sm text-gray-500 dark:text-gray-400"
                            >
                                Sales by location
                            </h3>
                        </div>
                    </div>
                    <div
                        class="w-full flex justify-between items-center pb-2 border-b border-gray-200"
                    >
                        <div
                            class="w-1/2 font-semibold uppercase text-xs text-gray-800 dark:text-gray-400"
                        >
                            Top Province
                        </div>
                        <div
                            class="w-1/2 text-right font-semibold uppercase text-xs text-gray-800 dark:text-gray-400"
                        >
                            Revenue
                        </div>
                    </div>
                    <div
                        class="w-full flex justify-between items-center py-2 border-b border-gray-200"
                        v-for="salelocation in eventsalelocation"
                        :key="salelocation.id"
                    >
                        <div
                            class="w-1/2 text-sm text-gray-800 dark:text-gray-400"
                        >
                            {{ salelocation.province }}
                        </div>
                        <div
                            class="w-1/2 text-sm text-right text-gray-800 dark:text-gray-400"
                        >
                            ₱{{ salelocation.total_price }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-4">
                <div
                    class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800"
                >
                    <div class="w-full sm:mb-0">
                        <h3
                            class="font-semibold uppercase text-2xl text-gray-500 dark:text-gray-400"
                        >
                            LISTED EVENTS
                        </h3>
                    </div>
                    <VueCal
                        :events="splitsAndEvents.events"
                        class="vuecal mt-4 responsive"
                        events-count-on-year-view
                        today-button
                        sticky-split-labels

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
        </div>
    </AdminAuthenticatedLayout>

                            <!-- style="
                            width: 100%;
                            height: 655px;
                       
                        " -->
</template>
