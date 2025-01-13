<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import VueCal from "vue-cal";
import "vue-cal/dist/vuecal.css";
import "../../../css/custom-styles.css";
import "../../../css/user-vuecal.css";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import NumberInput from "@/Components/NumberInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Toast from "@/Components/Toast.vue";
import { Inertia } from "@inertiajs/inertia";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";

const userId = usePage().props.auth.user.id;

const props = defineProps({
    pkg: {
        type: Object,
        default: () => ({}),
    },
    getAllPackages: {
        type: Array,
        required: true,
    },
    getEvents: {
        type: Array,
        required: true,
    },
    backdropTypes: {
        type: Array,
        required: true,
    },

    backdropColors: {
        type: Array,
        required: true,
    },
    auth: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    event: "",
    contactperson: "",
    contactno: "",
    email: "",
    region: "",
    province: "",
    city: "",
    barangay: "",
    street: "",
    zipcode: "",
    date: "",
    hour: "",
    minute: "",
    ampm: "",
    packageid: String(props.pkg.id || ""),
    packagename: props.pkg.name ? `${props.pkg.name} - ${props.pkg.alias}` : "",
    alias: props.pkg.alias || "",
    packagesize: "",
    backdroptype: "",
    backdropcolor: "",
    number_of_shots: "",
    price: "",
    extension: "",
    theme: "",
    suggestion: "",
    images: [],
    user_id: userId,
});

const submitForm = async () => {
    if (validateStep()) {
        try {
            // Show loading indicator
            form.processing = true;

            // Submit the form
            await form.post(route("user.event.store"), {
                onSuccess: () => {
                    console.log("Form submitted successfully");
                },
                onError: (errors) => {
                    console.error("Error submitting form:", errors);
                },
                onFinish: () => {
                    console.log("Form submission finished");
                    // Hide loading indicator
                    form.processing = false;
                },
            });
        } catch (error) {
            console.error("Unexpected error during form submission:", error);
            // Hide loading indicator
            form.processing = false;
        }
    } else {
        console.warn("Form validation failed:", form.errors);
    }
};

/* #############################
     IMAGE HANDLER & PREVIEW
    ############################# */
const handleFileUpload = (event) => {
    form.images = Array.from(event.target.files);
    console.log(form.images);
};
// const handleFileUpload = (event) => {
//     const files = Array.from(event.target.files);
//     form.images = [];

//     files.forEach((file) => {
//         const reader = new FileReader();
//         reader.onload = (e) => {
//             form.images.push({
//                 name: file.name,
//                 preview: e.target.result,
//                 file: file,
//             });
//         };
//         reader.readAsDataURL(file);
//     });
// };
const packageSizes = computed(() => {
    return [
        props.pkg.size,
        props.pkg.size2,
        props.pkg.size3,
        props.pkg.size4,
        props.pkg.size5,
    ].filter((size) => size);
});

/* #############################
        DEPENDED DROPDOWN FOR ADDRESS
    ############################# */
const jsonData = ref({});

onMounted(async () => {
    try {
        const response = await fetch(
            "/philippine_provinces_cities_municipalities_and_barangays_2019v2.json"
        );
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        jsonData.value = await response.json();
    } catch (error) {
        console.error("Error fetching JSON data:", error);
    }
});

// Load regions from JSON data
const regions = computed(() =>
    Object.keys(jsonData.value).map((key) => ({
        id: key,
        name: jsonData.value[key].region_name,
    }))
);

// Computed properties for dependent dropdowns
const provinces = computed(() => {
    if (!form.region) return [];
    const region = regions.value.find((region) => region.name === form.region);
    return region
        ? Object.keys(jsonData.value[region.id].province_list).map((key) => ({
              id: key,
              name: key,
          }))
        : [];
});

const cities = computed(() => {
    if (!form.region || !form.province) return [];
    const region = regions.value.find((region) => region.name === form.region);
    return region
        ? Object.keys(
              jsonData.value[region.id].province_list[form.province]
                  .municipality_list
          ).map((key) => ({ id: key, name: key }))
        : [];
});

const barangays = computed(() => {
    if (!form.region || !form.province || !form.city) return [];
    const region = regions.value.find((region) => region.name === form.region);
    return region
        ? jsonData.value[region.id].province_list[form.province]
              .municipality_list[form.city].barangay_list
        : [];
});

// Watch for changes in region and reset province, city, barangay, and street
watch(
    () => form.region,
    () => {
        form.province = "";
        form.city = "";
        form.barangay = "";
        form.street = "";
    }
);

// Watch for changes in province and reset city, barangay, and street
watch(
    () => form.province,
    () => {
        form.city = "";
        form.barangay = "";
        form.street = "";
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

// Watch for changes in city and reset barangay and street
watch(
    () => form.city,
    () => {
        form.barangay = "";
        form.street = "";
    }
);

// time - hour, minute, ampm

const validateDate = (date) => {
    const currentDate = new Date();
    const selectedDate = new Date(date);

    if (selectedDate < currentDate) {
        return false;
    }
    return true;
};

const hours = computed(() => {
    const hoursArray = [];
    for (let hour = 1; hour <= 12; hour++) {
        hoursArray.push(String(hour).padStart(2, "0"));
    }
    return hoursArray;
});

const minutes = computed(() => {
    const minutesArray = [];
    for (let minute = 0; minute < 60; minute += 15) {
        minutesArray.push(String(minute).padStart(2, "0"));
    }
    return minutesArray;
});

const ampmOptions = ref(["AM", "PM"]);

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
    // splits: [
    //     { id: 1, class: "booth1"},
    //     // { id: 1, class: "booth1", label: "Booth 1" },
    //     // { id: 2, class: "booth2", label: "Booth 2" },
    // ],
    splits: [],
    events: [],
};

if (props.getEvents && Array.isArray(props.getEvents)) {
    // Split end times
    const splitEndTimes = {
        1: new Date(0),
        // 2: new Date(0),
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

            // let split = 1;
            // if (startDateTime < splitEndTimes[1]) {
            //     split = 2;
            // } else if (startDateTime < splitEndTimes[1] && startDateTime < splitEndTimes[2]) {
            //     split = 2;
            // }

            let split = 1;

            splitEndTimes[split] = endDateTime;

            let color = "primary";
            if (props.auth.user.id === event.user_id) {
                color = "primary";
            } else {
                color = "secondary";
            }

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
                    start: startDateTime,
                    end: endDateTime,
                    title:
                        props.auth.user.id === event.user_id
                            ? props.getEvents[0].event
                            : "Booked Event",
                    class: color,
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

// Function to check if the selected date and time is taken in both booths
const isDateTimeTaken = (date, hour, minute, ampm) => {
    const checkDate = form.date;

    // time
    const checkHour = form.hour;
    const checkMinute = form.minute;
    const checkAmpm = form.ampm;

    let hour24 = parseInt(checkHour, 10);
    if (checkAmpm.toLowerCase() === "pm" && hour24 !== 12) {
        hour24 += 12;
    } else if (checkAmpm.toLowerCase() === "am" && hour24 === 12) {
        hour24 = 0;
    }

    const checkTime = `${String(hour24).padStart(2, "0")}:${String(
        checkMinute
    ).padStart(2, "0")}:00`;

    // selected of user
    const selectedStartDateTime = new Date(`${checkDate}T${checkTime}`);
    const selectedEndDateTime = new Date(
        selectedStartDateTime.getTime() + 2 * 60 * 60 * 1000
    );

    // // filtering events from both booths
    // const booth1Events = splitsAndEvents.events.filter(
    //     (event) => event.split === 1
    // );
    // const booth2Events = splitsAndEvents.events.filter(
    //     (event) => event.split === 2
    // );

    // // getting the event start and end date/time from both booths
    // const isBooth1Taken = booth1Events.some((event) => {
    //     const eventStartDateTime = new Date(event.start);
    //     const eventEndDateTime = new Date(event.end);

    //     return (
    //         selectedStartDateTime < eventEndDateTime &&
    //         selectedEndDateTime > eventStartDateTime
    //     );
    // });

    // // Check if the selected date and time is taken in booth 2
    // const isBooth2Taken = booth2Events.some((event) => {
    //     const eventStartDateTime = new Date(event.start);
    //     const eventEndDateTime = new Date(event.end);

    //     return (
    //         selectedStartDateTime < eventEndDateTime &&
    //         selectedEndDateTime > eventStartDateTime
    //     );
    // });

    // // Return true only if both booths are taken
    // return isBooth1Taken && isBooth2Taken;

    // filtering events for the single booth
    const boothEvents = splitsAndEvents.events.filter(
        (event) => event.split === 1
    );

    // getting the event start and end date/time for the single booth
    const isBoothTaken = boothEvents.some((event) => {
        const eventStartDateTime = new Date(event.start);
        const eventEndDateTime = new Date(event.end);

        return (
            selectedStartDateTime < eventEndDateTime &&
            selectedEndDateTime > eventStartDateTime
        );
    });

    // Return true if the booth is taken
    return isBoothTaken;
};

/* #############################
                STEPPER
    ############################# */
const nextStep = () => {
    if (validateStep()) {
        activeStep.value++;
    }
};

const prevStep = () => {
    activeStep.value--;
};

const activeStep = ref(1);

const validateStep = () => {
    let isValid = true;
    form.errors = {}; // Clear previous errors

    if (activeStep.value === 1) {
        // if (!form.event) {
        //     form.errors.event = "Event name is required.";
        //     isValid = false;
        // }
        // if (!form.region) {
        //     form.errors.region = "Region is required.";
        //     isValid = false;
        // }
        // if (!form.province) {
        //     form.errors.province = "Province is required.";
        //     isValid = false;
        // }
        // if (!form.city) {
        //     form.errors.city = "City is required.";
        //     isValid = false;
        // }
        // if (!form.barangay) {
        //     form.errors.barangay = "Barangay is required.";
        //     isValid = false;
        // }
        // if (!form.street) {
        //     form.errors.street = "Street is required.";
        //     isValid = false;
        // }
        // if (!form.zipcode) {
        //     form.errors.zipcode = "Zip code is required.";
        //     isValid = false;
        // }
        // if (!form.contactperson) {
        //     form.errors.contactperson = "Contact person is required.";
        //     isValid = false;
        // }
        // if (!form.contactno) {
        //     form.errors.contactno = "Contact number is required.";
        //     isValid = false;
        // }
        // if (!form.email) {
        //     form.errors.email = "Email address is required.";
        //     isValid = false;
        // }
        // if (!form.hour) {
        //     form.errors.hour = "Hour is required.";
        //     isValid = false;
        // }
        // if (!form.minute) {
        //     form.errors.minute = "Minute is required.";
        //     isValid = false;
        // }
        // if (!form.ampm) {
        //     form.errors.ampm = "AMPM is required.";
        //     isValid = false;
        // }
        // if (!form.date) {
        //     form.errors.date = "Date is required.";
        //     isValid = false;
        // } else if (!validateDate(form.date)) {
        //     form.errors.date = "Date must be a future date.";
        //     isValid = false;
        // } else if (
        //     isDateTimeTaken(form.date, form.hour, form.minute, form.ampm)
        // ) {
        //     form.errors.date = "The selected date and time are not available.";
        //     isValid = false;
        // }
        // if (!form.hour && !form.minute && !form.ampm) {
        //     form.errors.time = "Time is required.";
        //     isValid = false;
        // }
    } else if (activeStep.value === 2) {
        // if (!form.packagename) {
        //     form.errors.packagename = "Package name is required.";
        //     isValid = false;
        // }
        // if (!form.packagesize) {
        //     form.errors.packagesize = "Package size is required.";
        //     isValid = false;
        // }
        // if (!form.number_of_shots) {
        //     form.errors.number_of_shots = "Number of shots is required.";
        //     isValid = false;
        // }
        // if (!form.price) {
        //     form.errors.price = "Price is required.";
        //     isValid = false;
        // }
        // if (!form.extension) {
        //     form.errors.extension = "Extension is required.";
        //     isValid = false;
        // }
        // if (!form.backdroptype) {
        //     form.errors.backdroptype = "Backdrop type is required.";
        //     isValid = false;
        // }
        // if (!form.backdropcolor) {
        //     form.errors.backdropcolor = "Backdrop color is required.";
        //     isValid = false;
        // }
        // if (!form.theme) {
        //     form.errors.theme = "Theme is required.";
        //     isValid = false;
        // }
    } else if (activeStep.value === 3) {
    }
    return isValid;
};

/* #############################
    DEPENDENT DROPDOWN FOR BACKDROP TYPES AND COLORS
    ############################# */
const filterBackdropColors = (backdropType) => {
    return props.backdropColors.filter(
        (color) => color.backdroptype_name === backdropType
    );
};

const filteredBackdropColors = computed(() => {
    if (!form.backdroptype) return [];
    return props.backdropColors.filter(
        (color) => color.backdroptype_name === form.backdroptype
    );
});

const backdropImage = computed(() => {
    if (form.backdroptype && form.backdropcolor) {
        const matchingBackdrop = props.backdropColors.find(
            (item) =>
                item.backdroptype_name.toLowerCase() ===
                    form.backdroptype.toLowerCase() &&
                item.color.toLowerCase() === form.backdropcolor.toLowerCase()
        );

        if (matchingBackdrop) {
            const imagePath = `/uploads/backdrop/${form.backdroptype.toLowerCase()}/${
                matchingBackdrop.image
            }`;
            console.log("Image path:", imagePath);
            return imagePath;
        }
    }
    return null;
});

const mockupImage = computed(() => {
    if (form.packagesize && form.number_of_shots) {
        return `/uploads/mockups/${form.packagesize}_${form.number_of_shots}.png`;
    }
});

const packageSizeNoIDSelected = ref([]);

const selectedPackageDetails = ref({
    name: props.pkg.name || "",
    price: props.pkg.price || "",
    number_of_shots: props.pkg.number_of_shots || "",
    extension: props.pkg.extension || "",
    duration: props.pkg.duration || "",
});

const packageOptions = computed(() => {
    const selectedPackage = props.getAllPackages.find(
        (pkg) => pkg.id === parseInt(form.packageid)
    );
    return selectedPackage ? selectedPackage.options : [];
});

const FilteredPackageOptions = computed(() => {
    if (form.packagesize.toLowerCase() === "strips") {
        return packageOptions.value.filter(
            (option) => option.number_of_shots === 4
        );
    }
    return packageOptions.value;
});

// Watch for changes in packagename and update packageid and other attributes accordingly
watch(
    () => form.packagename,
    (newPackagename) => {
        const selectedPackage = props.getAllPackages.find(
            (pkg) => pkg.name === newPackagename
        );
        if (selectedPackage) {
            form.packageid = String(selectedPackage.id);
            form.alias = selectedPackage.alias;
            packageSizeNoIDSelected.value = [
                selectedPackage.size,
                selectedPackage.size2,
                selectedPackage.size3,
                selectedPackage.size4,
                selectedPackage.size5,
            ].filter((size) => size);
            selectedPackageDetails.value = {
                name: selectedPackage.name,
                alias: selectedPackage.alias,
                duration: selectedPackage.duration,
                inclusion: selectedPackage.inclusion,
                price: selectedPackage.price,
                number_of_shots: selectedPackage.number_of_shots,
                extension: selectedPackage.extension,
            };
        }
    }
);

// Watch for changes in package name
watch(
    () => form.packagename,
    () => {
        form.packagesize = "";
    }
);

// Watch for changes in backdroptype
watch(
    () => form.backdroptype,
    () => {
        form.backdropcolor = "";
    }
);

// Watch for changes in packagesize and update number_of_shots accordingly
watch(
    () => form.packagesize,
    () => {
        form.number_of_shots = "";
    }
);

// Watch for changes in packagesize and update number_of_shots accordingly
watch(
    () => form.packagesize,
    () => {
        form.backdroptype = "";
        form.backdropcolor = "";
    }
);

// Watch for changes in number_of_shots and update price and extension accordingly
watch(
    () => form.number_of_shots,
    (newNumberOfShots) => {
        const selectedOption = FilteredPackageOptions.value.find(
            (option) => option.number_of_shots === newNumberOfShots
        );
        if (!selectedOption) {
            form.price = "";
            form.extension = "";
        } else {
            form.price = selectedOption.price;
            form.extension = selectedOption.extension;
        }
    }
);
const showModal = ref(false);

const openModal = () => {
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const handleModalSubmit = () => {
    const date = form.date;
    const hour = form.hour;
    const minute = form.minute;
    const ampm = form.ampm;

    const isTaken = isDateTimeTaken(date, hour, minute, ampm);
    if (isTaken) {
        form.errors.date = "The selected date and time are not available.";
    } else {
        form.errors.date = "";
        closeModal();
    }
};

/* #############################
        CONFIRMATION BACKDROP 
   ############################# */
const confirmationModal = ref(false);

const openConfirmationModal = () => {
    confirmationModal.value = true;
};

const closeConfirmationModal = () => {
    confirmationModal.value = false;
};

const handleConfirm = () => {
    confirmationModal.value = false;
    submitForm();
};
</script>

<template>
    <Head title="Registration" />

    <AuthenticatedLayout :auth="auth">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Event Registration
            </h2>
        </template>
        <Toast />

        <div class="py-12">
            <div class="max-w-[1400px] mx-auto sm:px-18 lg:px-2">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg pb-6"
                >
                    <div class="px-16 pt-8 pb-2">
                        <ol
                            class="flex items-center w-full text-lg font-medium text-center text-gray-500 dark:text-white sm:text-base"
                        >
                            <li
                                :class="[
                                    `flex md:w-full items-center text-primary-800 dark:text-primary-500 sm:after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-300 after:border-1 after:hidden sm:after:inline-block after:mx-6 xl:after:mx-10 dark:after:border-white`,
                                    activeStep === 2 || activeStep === 3
                                        ? 'after:border-primary-800'
                                        : 'after:border-gray-400',
                                ]"
                            >
                                <span
                                    :class="[
                                        `flex items-center justify-center align-center text-lg after:content-['/'] sm:after:hidden after:mx-2`,
                                        activeStep === 2 || activeStep === 3
                                            ? 'after:text-primary-800'
                                            : 'after:text-gray-500',
                                    ]"
                                >
                                    <svg
                                        class="me-2"
                                        aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                        style="width: 20px; height: 20px"
                                    >
                                        <path
                                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"
                                        />
                                    </svg>
                                    Event
                                    <span class="hidden sm:inline-flex sm:ms-2"
                                        >Info</span
                                    >
                                </span>
                            </li>
                            <li
                                :class="[
                                    `flex md:w-full items-center after:content-[''] after:w-full after:h-1 after:border-b  after:border-1 after:hidden sm:after:inline-block after:mx-6 xl:after:mx-10 dark:after:border-white`,
                                    activeStep === 2 || activeStep === 3
                                        ? 'text-primary-800'
                                        : 'text-gray-500',
                                    activeStep === 3
                                        ? 'after:border-primary-800'
                                        : 'after:border-gray-400',
                                ]"
                            >
                                <span
                                    :class="[
                                        `flex items-center justify-center align-center text-lg after:content-['/'] sm:after:hidden after:mx-2`,
                                        activeStep === 3
                                            ? 'after:text-primary-800'
                                            : 'after:text-gray-500',
                                    ]"
                                >
                                    <span
                                        :class="[
                                            'flex items-center justify-center w-5 h-5 me-2 text-sm border text-gray-500 border-gray-500 rounded-full shrink-0 dark:border-primary-500',
                                            activeStep === 2 || activeStep === 3
                                                ? 'hidden'
                                                : '',
                                        ]"
                                    >
                                        2
                                    </span>
                                    <svg
                                        class="me-2 text-primary-800"
                                        aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                        style="width: 20px; height: 20px"
                                        :class="[
                                            activeStep === 2 || activeStep === 3
                                                ? ''
                                                : 'hidden',
                                        ]"
                                    >
                                        <path
                                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"
                                        />
                                    </svg>
                                    Package
                                    <span class="hidden sm:inline-flex sm:ms-2">
                                        Selection</span
                                    >
                                </span>
                            </li>
                            <li
                                :class="[
                                    'flex items-center text-lg',
                                    activeStep === 3
                                        ? 'text-primary-800'
                                        : 'text-gray-500',
                                ]"
                            >
                                <span
                                    :class="[
                                        'flex items-center justify-center w-5 h-5 me-2 text-sm border text-gray-500 border-gray-500 rounded-full shrink-0 dark:border-primary-500',
                                        activeStep === 3 ? 'hidden' : '',
                                    ]"
                                >
                                    3
                                </span>
                                <svg
                                    class="me-2 text-primary-800"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                    style="width: 20px; height: 20px"
                                    :class="[activeStep === 3 ? '' : 'hidden']"
                                >
                                    <path
                                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"
                                    />
                                </svg>

                                Confirmation
                            </li>
                        </ol>
                    </div>
                    <div class="px-14 pt-6">
                        <form @submit.prevent="submitForm">
                            <!-- STEP 1: BACKDROP DETAILS -->
                            <div v-if="activeStep === 1">
                                <div class="row">
                                    <div
                                        class="flex justify-center align-center gap-2 pb-6"
                                    >
                                        <svg
                                            class="w-6 h-6 text-primary-800 group-hover:text-gray-900 dark:group-hover:text-white"
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
                                        <h1
                                            class="text-center text-lg font-bold text-primary-800"
                                        >
                                            Browse Available Dates
                                        </h1>
                                    </div>

                                    <div
                                        class="col-12 flex justify-center align-center"
                                    >
                                        <VueCal
                                            :events="splitsAndEvents.events"
                                            class="vuecal"
                                            events-count-on-year-view
                                            today-button
                                            sticky-split-labels
                                            style="
                                                width: 150%;
                                                height: 615px;
                                                max-width: 1150px;
                                            "
                                            :disable-views="[
                                                'day',
                                                'years',
                                                'year',
                                            ]"
                                            active-view="month"
                                            timeFormat="hh:mm {AM}"
                                            :min-date="minDate"
                                            :split-days="splitsAndEvents.splits"
                                        >
                                        </VueCal>
                                    </div>
                                </div>

                                <div
                                    class="flex justify-center align-center gap-2 py-6"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-6 h-7 text-primary-800 group-hover:text-gray-900 dark:group-hover:text-white"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path
                                            d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"
                                        ></path>
                                        <path d="M9 3v2h6V3"></path>
                                        <path d="M8 10h8"></path>
                                        <path d="M8 14h5"></path>
                                    </svg>
                                    <h1
                                        class="text-center text-lg font-bold text-primary-800"
                                    >
                                        Event Information
                                    </h1>
                                </div>

                                <div
                                    class="grid grid-cols-1 md:grid-cols-3 gap-4"
                                >
                                    <div>
                                        <InputLabel
                                            for="event"
                                            value="Title of the Event"
                                            class="text-pink-900"
                                        />
                                        <TextInput
                                            id="event"
                                            type="text"
                                            class="border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                            v-model="form.event"
                                            autofocus
                                            autocomplete="off"
                                            placeholder="e.g. Aeon's Birthday"
                                        />
                                        <InputError
                                            class="mt-2"
                                            :message="form.errors.event"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel
                                            for="contactperson"
                                            value="Contact Person"
                                            class="text-pink-900"
                                        />
                                        <TextInput
                                            id="contactperson"
                                            type="text"
                                            class="border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                            v-model="form.contactperson"
                                            autocomplete="off"
                                        />
                                        <InputError
                                            class="mt-2"
                                            :message="form.errors.contactperson"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel
                                            for="contactno"
                                            value="Contact Number"
                                            class="text-pink-900"
                                        />
                                        <NumberInput
                                            id="contactno"
                                            type="text"
                                            class="border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                            minlength="11"
                                            maxlength="11"
                                            v-model="form.contactno"
                                            autocomplete="off"
                                        />
                                        <InputError
                                            class="mt-2"
                                            :message="form.errors.contactno"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel
                                            for="email"
                                            value="Email"
                                            class="text-pink-900"
                                        />
                                        <TextInput
                                            id="email"
                                            type="email"
                                            class="border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                            v-model="form.email"
                                            autocomplete="off"
                                        />
                                        <InputError
                                            class="mt-2"
                                            :message="form.errors.email"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel
                                            for="date"
                                            value="Date & Time"
                                            class="text-pink-900"
                                        />
                                        <!-- Button to open modal -->
                                        <PrimaryButton
                                            class="w-full py-2.5 flex items-center justify-center gap-2"
                                            @click="openModal"
                                            type="button"
                                        >
                                            <svg
                                                class="w-6 h-6 text-gray-50 group-hover:text-gray-900 dark:group-hover:text-white"
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
                                            <span>Select Date and Time</span>
                                        </PrimaryButton>
                                    </div>

                                    <!-- Readonly date and time fields -->
                                    <div>
                                        <InputLabel
                                            for="date"
                                            value="*Selected Date & Time displays here."
                                            class="text-green-600"
                                        />

                                        <div class="flex">
                                            <TextInput
                                                id="date"
                                                type="date"
                                                class="border bg-gray-100 rounded-e-none border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                                v-model="form.date"
                                                disabled
                                            />

                                            <TextInput
                                                id="hour"
                                                class="select bg-gray-100 select-info border border-gray-300 text-gray-900 focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                                v-model="form.hour"
                                                disabled
                                            />
                                            <TextInput
                                                id="minute"
                                                class="select bg-gray-100 select-info rounded-none border border-gray-300 text-gray-900 focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                                v-model="form.minute"
                                                disabled
                                            />
                                            <TextInput
                                                id="ampm"
                                                class="select bg-gray-100 select-info rounded-s-none border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                                v-model="form.ampm"
                                                disabled
                                            />
                                        </div>
                                        <InputError
                                            class="mt-2"
                                            :message="form.errors.date"
                                        />
                                    </div>
                                </div>
                                <div
                                    class="flex items-center justify-center mt-4 text-lg text-gray-400"
                                >
                                    <div class="flex items-center w-full">
                                        <div
                                            class="flex-grow border-t border-gray-300"
                                        ></div>
                                        <span
                                            class="font-semibold text-gray-600 mx-2"
                                        >
                                            Event Venue</span
                                        >
                                        <div
                                            class="flex-grow border-t border-gray-300"
                                        ></div>
                                    </div>
                                </div>

                                <div
                                    class="grid grid-cols-1 mt-2 md:grid-cols-3 gap-4"
                                >
                                    <div>
                                        <InputLabel
                                            for="region"
                                            value="Region"
                                            class="text-pink-900"
                                        />
                                        <select
                                            id="region"
                                            v-model="form.region"
                                            class="select select-info border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                        >
                                            <option value="" disabled>
                                                Select Region
                                            </option>
                                            <option
                                                v-for="region in regions"
                                                :key="region.id"
                                                :value="region.name"
                                            >
                                                {{ region.name }}
                                            </option>
                                        </select>
                                        <InputError
                                            :message="form.errors.region"
                                            class="mt-2"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel
                                            for="province"
                                            value="Province"
                                            class="text-pink-900"
                                        />
                                        <select
                                            id="province"
                                            v-model="form.province"
                                            :class="{
                                                'disabled: opacity-50':
                                                    !form.region,
                                            }"
                                            :disabled="!form.region"
                                            class="select select-info border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                        >
                                            <option value="" disabled>
                                                Select Province
                                            </option>
                                            <option
                                                v-for="province in provinces"
                                                :key="province.id"
                                                :value="province.name"
                                            >
                                                {{ province.name }}
                                            </option>
                                        </select>
                                        <InputError
                                            :message="form.errors.province"
                                            class="mt-2"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel
                                            for="city"
                                            value="City/Municipality"
                                            class="text-pink-900"
                                        />
                                        <select
                                            id="city"
                                            v-model="form.city"
                                            :class="{
                                                'disabled: opacity-50':
                                                    !form.province,
                                            }"
                                            :disabled="!form.province"
                                            class="select select-info border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                        >
                                            <option value="" disabled>
                                                Select City/Municipality
                                            </option>
                                            <option
                                                v-for="city in cities"
                                                :key="city.id"
                                                :value="city.name"
                                            >
                                                {{ city.name }}
                                            </option>
                                        </select>
                                        <InputError
                                            :message="form.errors.city"
                                            class="mt-2"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel
                                            for="barangay"
                                            value="Barangay"
                                            class="text-pink-900"
                                        />
                                        <select
                                            id="barangay"
                                            v-model="form.barangay"
                                            class="select select-info border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                            :class="{
                                                'disabled: opacity-50':
                                                    !form.city,
                                            }"
                                            :disabled="!form.city"
                                        >
                                            <option value="" disabled>
                                                Select Barangay
                                            </option>
                                            <option
                                                v-for="barangay in barangays"
                                                :key="barangay"
                                                :value="barangay"
                                            >
                                                {{ barangay }}
                                            </option>
                                        </select>
                                        <InputError
                                            :message="form.errors.barangay"
                                            class="mt-2"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel
                                            for="street"
                                            value="House No., Bldg., Street "
                                            class="text-pink-900"
                                        />
                                        <TextInput
                                            id="street"
                                            type="text"
                                            class="border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                            v-model="form.street"
                                            autocomplete="off"
                                        />
                                        <InputError
                                            class="mt-2"
                                            :message="form.errors.street"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel
                                            for="zipcode"
                                            value="Zip Code"
                                            class="text-pink-900"
                                        />
                                        <TextInput
                                            id="zipcode"
                                            type="text"
                                            class="border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                            v-model="form.zipcode"
                                            autocomplete="off"
                                        />
                                        <InputError
                                            class="mt-2"
                                            :message="form.errors.zipcode"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 2: EVENT DATE DETAILS -->
                            <!-- DISPLAYS WHEN A PACKAGE IS SELECTED -->
                            <div v-if="activeStep === 2 && props.pkg.id">
                                <div
                                    class="flex justify-center align-center gap-2"
                                >
                                    <svg
                                        class="w-7 h-7 transition text-primary-800 group-hover:text-gray-900 dark:group-hover:text-white,"
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
                                    <h1
                                        class="text-center text-lg font-bold text-primary-800"
                                    >
                                        Package and Backdrop Selection
                                    </h1>
                                </div>
                                <div
                                    class="flex gap-12 mx-6 justify-center align-center"
                                >
                                    <div
                                        class="grid grid-rows-1 mt-6 gap-4 w-1/3"
                                    >
                                        <NumberInput
                                            id="packageid"
                                            type="text"
                                            class="mt-1 hidden w-full"
                                            v-model="form.packageid"
                                            :value="props.pkg.id"
                                            required
                                            readonly
                                            autocomplete="off"
                                        />
                                        <!-- <InputError
                                            class="mt-2"
                                            :message="form.errors.packageid"
                                        /> -->
                                        <!-- </div> -->

                                        <div>
                                            <InputLabel
                                                for="packagename"
                                                value="Package Name"
                                                class="text-pink-900"
                                            />
                                            <TextInput
                                                id="packagename"
                                                type="text"
                                                class="border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                                v-model="form.packagename"
                                                :value="props.pkg.name"
                                                required
                                                readonly
                                                autocomplete="off"
                                            />

                                            <InputError
                                                class="mt-2"
                                                :message="
                                                    form.errors.packagename
                                                "
                                            />
                                        </div>

                                        <div>
                                            <InputLabel
                                                for="packagesize"
                                                value="Package Size"
                                                class="text-pink-900"
                                            />
                                            <select
                                                class="select select-info border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                                id="packagesize"
                                                name="packagesize"
                                                required
                                                v-model="form.packagesize"
                                                :class="{
                                                    'disabled: opacity-50':
                                                        !form.packagename,
                                                }"
                                                :disabled="!form.packagename"
                                            >
                                                <option disabled value="">
                                                    Select Size
                                                </option>
                                                <option
                                                    v-for="size in packageSizes"
                                                    :key="size"
                                                    :value="size"
                                                >
                                                    {{ size }}
                                                </option>
                                            </select>
                                            <InputError
                                                class="mt-2"
                                                :message="
                                                    form.errors.packagesize
                                                "
                                            />
                                        </div>

                                        <div>
                                            <InputLabel
                                                for="number_of_shots"
                                                value="Number of shots"
                                                class="text-pink-900"
                                            />
                                            <select
                                                id="number_of_shots"
                                                v-model="form.number_of_shots"
                                                class="border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                                required
                                                :class="{
                                                    'disabled: opacity-50':
                                                        !form.packagesize,
                                                }"
                                                :disabled="!form.packagesize"
                                            >
                                                <option disabled value="">
                                                    Select Number of Shots
                                                </option>
                                                <option
                                                    v-for="option in FilteredPackageOptions"
                                                    :key="
                                                        option.number_of_shots
                                                    "
                                                    :value="
                                                        option.number_of_shots
                                                    "
                                                >
                                                    {{ option.number_of_shots }}
                                                </option>
                                            </select>
                                            <InputError
                                                class="mt-2"
                                                :message="
                                                    form.errors.number_of_shots
                                                "
                                            />
                                        </div>

                                        <div>
                                            <InputLabel
                                                for="price"
                                                value="Price"
                                                class="text-pink-900"
                                            />
                                            <TextInput
                                                id="price"
                                                type="text"
                                                class="border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                                v-model="form.price"
                                                readonly
                                                disabled
                                                autocomplete="off"
                                            />
                                        </div>

                                        <div>
                                            <InputLabel
                                                for="extension"
                                                value="Extension"
                                                class="text-pink-900"
                                            />
                                            <TextInput
                                                id="extension"
                                                type="text"
                                                class="border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                                v-model="form.extension"
                                                readonly
                                                disabled
                                                autocomplete="off"
                                            />
                                        </div>

                                        <div>
                                            <InputLabel
                                                for="backdroptype"
                                                value="Backdrop Type"
                                                class="text-pink-900"
                                            />
                                            <select
                                                class="select select-info border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                                id="backdroptype"
                                                name="backdroptype"
                                                required
                                                v-model="form.backdroptype"
                                                :disabled="
                                                    !form.number_of_shots
                                                "
                                                :class="{
                                                    'disabled: opacity-50':
                                                        !form.number_of_shots,
                                                }"
                                            >
                                                <option disabled value="">
                                                    Select Background Type
                                                </option>
                                                <option
                                                    v-for="type in props.backdropTypes"
                                                    :key="type.id"
                                                    :value="type.name"
                                                >
                                                    {{ type.name }}
                                                </option>
                                            </select>

                                            <InputError
                                                class="mt-2"
                                                :message="
                                                    form.errors.backdroptype
                                                "
                                            />
                                        </div>

                                        <div>
                                            <InputLabel
                                                for="backdropcolor"
                                                value="Backdrop Color"
                                                class="text-pink-900"
                                            />
                                            <select
                                                class="select select-info border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                                id="backdropcolor"
                                                name="backdropcolor"
                                                required
                                                v-model="form.backdropcolor"
                                                :class="{
                                                    'disabled: opacity-50':
                                                        !form.backdroptype,
                                                }"
                                                :disabled="!form.backdroptype"
                                            >
                                                <option disabled value="">
                                                    Select Background Color
                                                </option>
                                                <option
                                                    v-for="color in filteredBackdropColors"
                                                    :key="color.id"
                                                    :value="color.color"
                                                >
                                                    {{ color.color }}
                                                </option>
                                            </select>

                                            <InputError
                                                class="mt-2"
                                                :message="
                                                    form.errors.backdropcolor
                                                "
                                            />
                                        </div>

                                        <div>
                                            <InputLabel
                                                for="theme"
                                                value="Theme/Motif"
                                                class="text-pink-900"
                                            />
                                            <TextInput
                                                id="theme"
                                                type="text"
                                                class="border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                                v-model="form.theme"
                                                required
                                                autocomplete="off"
                                            />
                                            <InputError
                                                class="mt-2"
                                                :message="form.errors.theme"
                                            />
                                        </div>

                                        <div>
                                            <InputLabel
                                                for="suggestion"
                                                value="Suggestions"
                                                class="text-pink-900"
                                            />
                                            <TextInput
                                                id="suggestion"
                                                type="text"
                                                class="border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                                v-model="form.suggestion"
                                                autocomplete="off"
                                            />
                                        </div>

                                        <div>
                                            <InputLabel
                                                for="image"
                                                value="Upload Your Preferred Theme:"
                                                class="text-pink-900"
                                            />
                                            <div
                                                class="flex items-center justify-center w-full"
                                            >
                                                <label
                                                    for="dropzone-file"
                                                    class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600"
                                                >
                                                    <div
                                                        class="flex flex-col items-center justify-center pt-5 pb-6"
                                                    >
                                                        <svg
                                                            class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            fill="none"
                                                            viewBox="0 0 20 16"
                                                        >
                                                            <path
                                                                stroke="currentColor"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"
                                                            />
                                                        </svg>
                                                        <p
                                                            class="mb-2 text-sm text-gray-500 dark:text-gray-400"
                                                        >
                                                            <span
                                                                class="font-semibold"
                                                                >Click to
                                                                upload</span
                                                            >
                                                            or drag and drop
                                                        </p>
                                                        <p
                                                            class="text-xs text-gray-500 dark:text-gray-400"
                                                        >
                                                            PNG, JPG, JPEG only
                                                        </p>
                                                    </div>
                                                    <input
                                                        id="dropzone-file"
                                                        type="file"
                                                        class="hidden"
                                                        multiple
                                                        @change="
                                                            handleFileUpload
                                                        "
                                                    />
                                                </label>
                                            </div>
                                            <InputError
                                                class="mt-2"
                                                :message="form.errors.images"
                                            />

                                            <div
                                                v-if="form.images.length"
                                                class="mt-4"
                                            >
                                                <h4
                                                    class="font-semibold text-gray-700"
                                                >
                                                    Uploaded Files:
                                                </h4>

                                                <ul
                                                    class="list-disc list-inside"
                                                >
                                                    <div
                                                        class="flex gap-2 flex-wrap"
                                                    >
                                                        <li
                                                            v-for="(
                                                                image, index
                                                            ) in form.images"
                                                            :key="index"
                                                            class="text-gray-600 text-sm"
                                                        >
                                                            {{ image.name }}

                                                            <img
                                                                :src="
                                                                    image.preview
                                                                "
                                                                :alt="
                                                                    image.name
                                                                "
                                                                class="w-32 object-cover mt-2"
                                                            />
                                                        </li>
                                                    </div>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="flex flex-col justify-center items-center self-start w-3/5 mt-8"
                                    >
                                        <div
                                            class="w-full border rounded-lg border-primary-200"
                                        >
                                            <h1
                                                class="bg-primary-200 py-2 text-center rounded-tl rounded-tr"
                                            >
                                                Package and Backdrop Preview
                                            </h1>
                                            <!-- If there's no packagesize and number of shots selected, it display a height:100% 
                                            else it absorbs the height of the mockups -->
                                            <div
                                                class="flex justify-center items-center"
                                                :style="{
                                                    height:
                                                        (!form.packagesize &&
                                                            !form.number_of_shots) ||
                                                        !form.packagesize ||
                                                        !form.number_of_shots
                                                            ? '400px'
                                                            : '100%',
                                                }"
                                            >
                                                <!-- If there's no packagesize and number of shots selected, there's no mockups showing
                                                height, and weight of the mockups depends on the packagesize  -->
                                                <div
                                                    v-show="
                                                        form.packagesize &&
                                                        form.number_of_shots
                                                    "
                                                    class="mx-auto my-6"
                                                    :style="{
                                                        backgroundImage:
                                                            backdropImage
                                                                ? `url(${backdropImage})`
                                                                : 'none',
                                                        backgroundColor:
                                                            backdropImage
                                                                ? 'transparent'
                                                                : '#d3d3d3',
                                                        backgroundSize: 'cover',
                                                        backgroundPosition:
                                                            'center',
                                                        width:
                                                            form.packagesize.toLowerCase() ===
                                                            'strips'
                                                                ? '300px'
                                                                : form.packagesize.toLowerCase() ===
                                                                  '4r'
                                                                ? '550px'
                                                                : form.packagesize.toLowerCase() ===
                                                                  'polaroid size'
                                                                ? '550px'
                                                                : '300px',
                                                        height:
                                                            form.packagesize.toLowerCase() ===
                                                            'strips'
                                                                ? '450px'   
                                                                : form.packagesize.toLowerCase() ===
                                                                  '4r'
                                                                ? '415px'
                                                               : form.packagesize.toLowerCase() ===
                                                                  'polaroid size'
                                                                ? '415px'
                                                                : '450px',
                                                        position: 'relative',
                                                        boxShadow:
                                                            '0 4px 8px rgba(0, 0, 0, .5)',
                                                    }"
                                                >
                                                    <img
                                                        :src="mockupImage"
                                                        v-if="mockupImage"
                                                        style="
                                                            width: 100%;
                                                            height: 100%;
                                                            object-fit: fill;
                                                            position: absolute;
                                                            top: 0;
                                                            left: 0;
                                                            boxshadow: '0 4px 8px rgba(0, 0, 0, 0.1)';
                                                            margin: auto;
                                                        "
                                                    />
                                                </div>
                                            </div>
                                            <div
                                                v-show="
                                                    form.packagesize.toLowerCase() ===
                                                        '4r' &&
                                                    form.number_of_shots == 2
                                                "
                                                class="flex align-center pb-4 justify-center font-bold text-center"
                                            >
                                                Picture placement may change
                                                depending on the design and
                                                layout.
                                            </div>
                                        </div>
                                        <div
                                            class="mt-8 border rounded-lg border-primary-200 w-full"
                                        >
                                            <h1
                                                class="bg-primary-200 py-2 text-center rounded-tl rounded-tr"
                                            >
                                                Add-ons
                                            </h1>
                                            <div class="p-4">
                                                <!-- New content here -->
                                                <p></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- DISPLAYS THERE'S NO PACKAGE SELECTED -->
                            <div v-else-if="activeStep === 2 && !props.pkg.id">
                                <div
                                    class="flex justify-center align-center gap-2"
                                >
                                    <svg
                                        class="w-7 h-7 text-primary-800 group-hover:text-gray-900 dark:group-hover:text-white,"
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
                                    <h1
                                        class="text-center text-lg font-bold text-primary-800"
                                    >
                                        Package and Backdrop Selection
                                    </h1>
                                </div>
                                <div
                                    class="flex gap-12 justify-center align-center"
                                >
                                    <div
                                        class="grid grid-rows-1 mt-6 gap-4 w-1/3 self-start"
                                    >
                                        <!-- <div> -->
                                        <!-- <InputLabel
                                            for="packageid"
                                            value="Package ID"
                                        /> -->
                                        <NumberInput
                                            id="packageid"
                                            type="text"
                                            class="mt-1 hidden w-full"
                                            v-model="form.packageid"
                                            required
                                            readonly
                                            autocomplete="off"
                                        />
                                        <!-- <InputError
                                            class="mt-2"
                                            :message="form.errors.packageid"
                                        /> -->
                                        <!-- </div> -->

                                        <div>
                                            <InputLabel
                                                for="packagename"
                                                value="Package Name"
                                                class="text-pink-900"
                                            />
                                            <select
                                                class="select select-info border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                                id="packagename"
                                                name="packagename"
                                                required
                                                placeholder="Select Package"
                                                v-model="form.packagename"
                                            >
                                                <option disabled value="">
                                                    Select Package
                                                </option>
                                                <option
                                                    v-for="allpkg in props.getAllPackages"
                                                    :key="allpkg.id"
                                                    :value="allpkg.name"
                                                >
                                                    {{ allpkg.name }} -
                                                    {{ allpkg.alias }}
                                                </option>
                                            </select>
                                            <InputError
                                                class="mt-2"
                                                :message="
                                                    form.errors.packagename
                                                "
                                            />
                                        </div>

                                        <div>
                                            <InputLabel
                                                for="packagesize"
                                                value="Package Size"
                                                class="text-pink-900"
                                            />
                                            <select
                                                class="select select-info border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                                id="packagesize"
                                                name="packagesize"
                                                required
                                                v-model="form.packagesize"
                                                :class="{
                                                    'disabled: opacity-50':
                                                        !form.packagename,
                                                }"
                                                :disabled="!form.packagename"
                                            >
                                                <option disabled value="">
                                                    Select Size
                                                </option>
                                                <option
                                                    v-for="size in packageSizeNoIDSelected"
                                                    :key="size"
                                                    :value="size"
                                                >
                                                    {{ size }}
                                                </option>
                                            </select>
                                            <InputError
                                                class="mt-2"
                                                :message="
                                                    form.errors.packagesize
                                                "
                                            />
                                        </div>
                                        <div>
                                            <InputLabel
                                                for="number_of_shots"
                                                value="Number of shots"
                                                class="text-pink-900"
                                            />
                                            <select
                                                id="number_of_shots"
                                                v-model="form.number_of_shots"
                                                class="border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                                required
                                                :class="{
                                                    'disabled: opacity-50':
                                                        !form.packagesize,
                                                }"
                                                :disabled="!form.packagesize"
                                            >
                                                <option disabled value="">
                                                    Select Number of Shots
                                                </option>
                                                <option
                                                    v-for="option in FilteredPackageOptions"
                                                    :key="
                                                        option.number_of_shots
                                                    "
                                                    :value="
                                                        option.number_of_shots
                                                    "
                                                >
                                                    {{ option.number_of_shots }}
                                                </option>
                                            </select>
                                            <InputError
                                                class="mt-2"
                                                :message="
                                                    form.errors.number_of_shots
                                                "
                                            />
                                        </div>

                                        <div>
                                            <InputLabel
                                                for="price"
                                                value="Price"
                                                class="text-pink-900"
                                            />
                                            <TextInput
                                                id="price"
                                                type="text"
                                                class="border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                                v-model="form.price"
                                                readonly
                                                disabled
                                                autocomplete="off"
                                            />
                                            <InputError
                                                class="mt-2"
                                                :message="form.errors.price"
                                            />
                                        </div>

                                        <div>
                                            <InputLabel
                                                for="extension"
                                                value="Extension"
                                                class="text-pink-900"
                                            />
                                            <TextInput
                                                id="extension"
                                                type="text"
                                                class="border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                                v-model="form.extension"
                                                readonly
                                                disabled
                                                autocomplete="off"
                                            />
                                            <InputError
                                                class="mt-2"
                                                :message="form.errors.extension"
                                            />
                                        </div>

                                        <div>
                                            <InputLabel
                                                for="backdroptype"
                                                value="Backdrop Type"
                                                class="text-pink-900"
                                            />
                                            <select
                                                class="select select-info border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                                id="backdroptype"
                                                name="backdroptype"
                                                required
                                                v-model="form.backdroptype"
                                                :disabled="
                                                    !form.number_of_shots
                                                "
                                                :class="{
                                                    'disabled: opacity-50':
                                                        !form.number_of_shots,
                                                }"
                                            >
                                                <option disabled value="">
                                                    Select Background Type
                                                </option>
                                                <option
                                                    v-for="type in props.backdropTypes"
                                                    :key="type.id"
                                                    :value="type.name"
                                                >
                                                    {{ type.name }}
                                                </option>
                                            </select>

                                            <InputError
                                                class="mt-2"
                                                :message="
                                                    form.errors.backdroptype
                                                "
                                            />
                                        </div>

                                        <div>
                                            <InputLabel
                                                for="backdropcolor"
                                                value="Backdrop Color"
                                                class="text-pink-900"
                                            />
                                            <select
                                                class="select select-info border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                                id="backdropcolor"
                                                name="backdropcolor"
                                                required
                                                v-model="form.backdropcolor"
                                                :disabled="!form.backdroptype"
                                                :class="{
                                                    'disabled: opacity-50':
                                                        !form.backdroptype,
                                                }"
                                            >
                                                <option disabled value="">
                                                    Select Background Color
                                                </option>
                                                <option
                                                    v-for="color in filteredBackdropColors"
                                                    :key="color.id"
                                                    :value="color.color"
                                                >
                                                    {{ color.color }}
                                                </option>
                                            </select>

                                            <InputError
                                                class="mt-2"
                                                :message="
                                                    form.errors.backdropcolor
                                                "
                                            />
                                        </div>

                                        <div>
                                            <InputLabel
                                                for="theme"
                                                value="Theme/Motif"
                                                class="text-pink-900"
                                            />
                                            <TextInput
                                                id="theme"
                                                type="text"
                                                class="border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                                v-model="form.theme"
                                                required
                                                autocomplete="off"
                                            />
                                            <InputError
                                                class="mt-2"
                                                :message="form.errors.theme"
                                            />
                                        </div>

                                        <div>
                                            <InputLabel
                                                for="suggestion"
                                                value="Suggestions"
                                                class="text-pink-900"
                                            />
                                            <TextInput
                                                id="suggestion"
                                                type="text"
                                                class="border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-600 dark:focus:border-primary-600"
                                                v-model="form.suggestion"
                                                autocomplete="off"
                                            />
                                        </div>

                                        <div>
                                            <InputLabel
                                                for="image"
                                                value="Upload Your Preferred Theme:"
                                                class="text-pink-900"
                                            />
                                            <div
                                                class="flex items-center justify-center w-full"
                                            >
                                                <label
                                                    for="dropzone-file"
                                                    class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600"
                                                >
                                                    <div
                                                        class="flex flex-col items-center justify-center pt-5 pb-6"
                                                    >
                                                        <svg
                                                            class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            fill="none"
                                                            viewBox="0 0 20 16"
                                                        >
                                                            <path
                                                                stroke="currentColor"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"
                                                            />
                                                        </svg>
                                                        <p
                                                            class="mb-2 text-sm text-gray-500 dark:text-gray-400"
                                                        >
                                                            <span
                                                                class="font-semibold"
                                                                >Click to
                                                                upload</span
                                                            >
                                                            or drag and drop
                                                        </p>
                                                        <p
                                                            class="text-xs text-gray-500 dark:text-gray-400"
                                                        >
                                                            PNG, JPG, JPEG only
                                                        </p>
                                                    </div>
                                                    <input
                                                        id="dropzone-file"
                                                        type="file"
                                                        class="hidden"
                                                        multiple
                                                        @change="
                                                            handleFileUpload
                                                        "
                                                    />
                                                </label>
                                            </div>
                                            <InputError
                                                class="mt-2"
                                                :message="form.errors.images"
                                            />

                                            <div
                                                v-if="form.images.length"
                                                class="mt-4"
                                            >
                                                <h4
                                                    class="font-semibold text-gray-700"
                                                >
                                                    Uploaded Files:
                                                </h4>
                                                <ul
                                                    class="list-disc list-inside"
                                                >
                                                    <li
                                                        v-for="(
                                                            image, index
                                                        ) in form.images"
                                                        :key="index"
                                                        class="text-gray-600 text-sm"
                                                    >
                                                        {{ image.name }}
                                                        <img
                                                            :src="image.preview"
                                                            :alt="image.name"
                                                            class="w-32 h-32 object-cover mt-2"
                                                        />
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="flex flex-col justify-center items-center self-start w-3/5 mt-8"
                                    >
                                        <div
                                            class="w-full border rounded-lg border-primary-200"
                                        >
                                            <h1
                                                class="bg-primary-200 py-2 text-center rounded-tl rounded-tr"
                                            >
                                                Package and Backdrop Preview
                                            </h1>
                                            <!-- If there's no packagesize and number of shots selected, it display a height:100% 
                                            else it absorbs the height of the mockups -->
                                            <div
                                                class="flex justify-center items-center"
                                                :style="{
                                                    height:
                                                        (!form.packagesize &&
                                                            !form.number_of_shots) ||
                                                        !form.packagesize ||
                                                        !form.number_of_shots
                                                            ? '400px'
                                                            : '100%',
                                                }"
                                            >
                                                <!-- If there's no packagesize and number of shots selected, there's no mockups showing
                                                height, and weight of the mockups depends on the packagesize  -->
                                                <div
                                                    v-show="
                                                        form.packagesize &&
                                                        form.number_of_shots
                                                    "
                                                    class="mx-auto my-6"
                                                    :style="{
                                                        backgroundImage:
                                                            backdropImage
                                                                ? `url(${backdropImage})`
                                                                : 'none',
                                                        backgroundColor:
                                                            backdropImage
                                                                ? 'transparent'
                                                                : '#d3d3d3',
                                                        backgroundSize: 'cover',
                                                        backgroundPosition:
                                                            'center',
                                                         width:
                                                            form.packagesize.toLowerCase() ===
                                                            'strips'
                                                                ? '300px'
                                                                : form.packagesize.toLowerCase() ===
                                                                  '4r'
                                                                ? '550px'
                                                                : form.packagesize.toLowerCase() ===
                                                                  'polaroid size'
                                                                ? '550px'
                                                                : '300px',
                                                        height:
                                                            form.packagesize.toLowerCase() ===
                                                            'strips'
                                                                ? '450px'   
                                                                : form.packagesize.toLowerCase() ===
                                                                  '4r'
                                                                ? '415px'
                                                               : form.packagesize.toLowerCase() ===
                                                                  'polaroid size'
                                                                ? '415px'
                                                                : '450px',
                                                        position: 'relative',
                                                        boxShadow:
                                                            '0 4px 8px rgba(0, 0, 0, .5)',
                                                    }"
                                                >
                                                    <img
                                                        :src="mockupImage"
                                                        v-if="mockupImage"
                                                        style="
                                                            width: 100%;
                                                            height: 100%;
                                                            object-fit: fill;
                                                            position: absolute;
                                                            top: 0;
                                                            left: 0;
                                                            boxshadow: '0 4px 8px rgba(0, 0, 0, 0.1)';
                                                            margin: auto;
                                                        "
                                                    />
                                                </div>
                                            </div>
                                            <div
                                                v-show="
                                                    form.packagesize.toLowerCase() ===
                                                        '4r' &&
                                                    form.number_of_shots == 3 &&
                                                    form.number_of_shots == 4
                                                "
                                                class="flex align-center pb-4 justify-center font-bold text-center"
                                            >
                                                Picture placement may change
                                                depending on the design and
                                                layout.
                                            </div>
                                        </div>
                                        <div
                                            class="mt-8 border rounded-lg border-primary-200 w-full"
                                        >
                                            <h1
                                                class="bg-primary-200 py-2 text-center rounded-tl rounded-tr"
                                            >
                                                Add-ons
                                            </h1>
                                            <div class="p-4">
                                                <!-- New content here -->
                                                <p></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 3: EVENT DETAILS -->
                            <div v-if="activeStep === 3">
                                <div
                                    class="flex justify-center align-center gap-2"
                                >
                                    <svg
                                        class="w-6 h-6 text-primary-800 group-hover:text-gray-900 dark:group-hover:text-white"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor"
                                        viewBox="0 0 18 20"
                                    >
                                        <path
                                            d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2ZM7 2h4v3H7V2Zm5.7 8.289-3.975 3.857a1 1 0 0 1-1.393 0L5.3 12.182a1.002 1.002 0 1 1 1.4-1.436l1.328 1.289 3.28-3.181a1 1 0 1 1 1.392 1.435Z"
                                        />
                                    </svg>
                                    <h1
                                        class="text-center text-lg font-bold text-primary-800"
                                    >
                                        Details Confirmation
                                    </h1>
                                </div>

                                <div class="mx-6 mt-6">
                                    <div class="">
                                        <p class="text-gray-600">
                                            Title of the Event:
                                            <strong class="text-gray-800">{{
                                                form.event
                                            }}</strong>
                                        </p>
                                        <p class="text-gray-600 mt-1">
                                            Contact Person:
                                            <strong class="text-gray-800">
                                                {{ form.contactperson }}</strong
                                            >
                                        </p>
                                        <p class="text-gray-600 mt-1">
                                            Email:
                                            <strong class="text-gray-800">
                                                {{ form.email }}</strong
                                            >
                                        </p>
                                        <p class="text-gray-600 mt-1">
                                            Contact Number:
                                            <strong class="text-gray-800">
                                                {{ form.contactno }}</strong
                                            >
                                        </p>
                                    </div>
                                    <div class="">
                                        <p class="text-gray-600">
                                            Exact time of photo booth session:
                                            <strong class="text-gray-800">
                                                {{
                                                    form.hour +
                                                    ":" +
                                                    form.minute +
                                                    " " +
                                                    form.ampm +
                                                    ", " +
                                                    formatDate(form.date)
                                                }}</strong
                                            >
                                        </p>
                                        <p class="text-gray-600 mt-1">
                                            Location of the Event:
                                            <strong class="text-gray-800">
                                                {{
                                                    form.street +
                                                    ", " +
                                                    form.barangay +
                                                    ", " +
                                                    form.city +
                                                    ", " +
                                                    form.province
                                                }}
                                            </strong>
                                        </p>
                                        <p class="text-gray-600 mt-1">
                                            Theme:
                                            <strong class="text-gray-800">
                                                {{ form.theme }}</strong
                                            >
                                        </p>
                                        <p class="text-gray-600 mt-1">
                                            Suggestion:
                                            <strong class="text-gray-800">
                                                {{ form.suggestion }}</strong
                                            >
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-col mt-8 mb-20">
                                    <div class="overflow-x-auto">
                                        <div
                                            class="inline-block min-w-full align-middle"
                                        >
                                            <div class="overflow-hidden shadow">
                                                <table
                                                    class="min-w-full divide-y divide-gray-200 table-fixed border-b border-gray-200 dark:divide-gray-600"
                                                >
                                                    <thead
                                                        class="border-t border-gray-200"
                                                    >
                                                        <tr>
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
                                                                Duration
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
                                                                Number of shots
                                                            </th>
                                                            <th
                                                                scope="col"
                                                                class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400"
                                                            >
                                                                Amount
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody
                                                        class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                                                    >
                                                        <tr>
                                                            <td
                                                                class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400"
                                                            >
                                                                <div
                                                                    class="text-base font-semibold text-gray-900 dark:text-white"
                                                                >
                                                                    {{
                                                                        form.packagename
                                                                    }}
                                                                </div>
                                                                <div
                                                                    class="text-sm font-normal text-gray-500 dark:text-gray-400"
                                                                >
                                                                    {{
                                                                        selectedPackageDetails.alias
                                                                    }}
                                                                </div>
                                                            </td>
                                                            <td
                                                                class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400"
                                                            >
                                                                <div
                                                                    class="text-base font-semibold text-gray-700 dark:text-white"
                                                                >
                                                                    {{
                                                                        form.backdroptype
                                                                    }}
                                                                </div>
                                                                <div
                                                                    class="text-sm font-normal text-gray-500 dark:text-gray-400"
                                                                >
                                                                    {{
                                                                        form.backdropcolor
                                                                    }}
                                                                </div>
                                                            </td>

                                                            <td
                                                                class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                                            >
                                                                {{
                                                                    selectedPackageDetails.duration
                                                                }}
                                                            </td>

                                                            <td
                                                                class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                                            >
                                                                ₱{{
                                                                    form.extension
                                                                }}
                                                                / per hour
                                                            </td>
                                                            <td
                                                                class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                                            >
                                                                {{
                                                                    form.number_of_shots
                                                                }}
                                                            </td>
                                                            <td
                                                                class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                                            >
                                                                ₱{{
                                                                    form.price
                                                                }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input
                                type="hidden"
                                name="user_id"
                                :value="form.user_id"
                            />

                            <PrimaryButton
                                class="mt-6 flex float-left"
                                @Click="prevStep"
                                v-if="activeStep === 2 || activeStep === 3"
                                type="button"
                            >
                                Back
                            </PrimaryButton>

                            <PrimaryButton
                                class="mt-6 flex float-right"
                                v-if="activeStep < 3"
                                @Click="nextStep"
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                                type="button"
                            >
                                Next
                            </PrimaryButton>

                            <PrimaryButton
                                class="mt-6 flex float-right"
                                v-if="activeStep === 3"
                                type="button"
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                                @click="openConfirmationModal()"
                            >
                                <span v-if="form.processing"
                                    >Submitting...</span
                                >
                                <span v-else>Submit</span>
                            </PrimaryButton>
                        </form>
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
                        <h3 class="mt-5 mb-8 text-gray-700 dark:text-gray-400">
                            Are you sure you want to submit? Please review your
                            details before submitting.
                        </h3>
                        <!-- Modal footer -->
                        <div class="mt-8 flex justify-center gap-6">
                            <SecondaryButton
                                class="uppercase px-5"
                                @click="closeConfirmationModal"
                            >
                                No, go back
                            </SecondaryButton>

                            <PrimaryButton
                                class="uppercase bg-danger px-10"
                                :class="{
                                    'opacity-25': form.processing,
                                }"
                                :disabled="form.processing"
                                @click="submitForm"
                            >
                                <span v-if="form.processing"
                                    >Submitting...</span
                                >
                                <span v-else>Submit</span>
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
            </div>
        </ConfirmationModal>

        <div
            v-if="showModal"
            class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75"
        >
            <div class="bg-white p-6 rounded-lg shadow-lg w-[400px]">
                <h2 class="text-xl font-semibold mb-4">Select Date and Time</h2>
                <div class="mb-4">
                    <InputLabel
                        for="modalDate"
                        value="Date"
                        class="text-pink-900"
                    />
                    <TextInput
                        id="modalDate"
                        type="date"
                        class="border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                        v-model="form.date"
                        :min="minInputDate"
                    />
                </div>
                <div class="mb-4">
                    <InputLabel
                        for="modalTime"
                        value="Time"
                        class="text-pink-900"
                    />
                    <div class="flex">
                        <select
                            id="modalHour"
                            class="select select-info rounded-e-none border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                            v-model="form.hour"
                        >
                            <option disabled value="">Hour</option>
                            <option
                                v-for="hour in hours"
                                :key="hour"
                                :value="hour"
                            >
                                {{ hour }}
                            </option>
                        </select>
                        <select
                            id="modalMinute"
                            class="select select-info rounded-none border border-gray-300 text-gray-900 focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                            v-model="form.minute"
                        >
                            <option disabled value="">Minute</option>
                            <option
                                v-for="minute in minutes"
                                :key="minute"
                                :value="minute"
                            >
                                {{ minute }}
                            </option>
                        </select>
                        <select
                            id="modalAmpm"
                            class="select select-info rounded-s-none border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                            v-model="form.ampm"
                        >
                            <option disabled value="">AM/PM</option>
                            <option
                                v-for="ampm in ampmOptions"
                                :key="ampm"
                                :value="ampm"
                            >
                                {{ ampm }}
                            </option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <InputError class="mt-2" :message="form.errors.hour" />
                        <InputError
                            class="mt-2"
                            :message="form.errors.minute"
                        />
                        <InputError class="mt-2" :message="form.errors.ampm" />
                    </div>
                </div>
                <InputError class="mt-2" :message="form.errors.date" />

                <div class="mt-4 flex justify-end">
                    <PrimaryButton
                        @click="handleModalSubmit"
                        :class="{
                            'disabled: opacity-50':
                                !form.ampm ||
                                !form.hour ||
                                !form.minute ||
                                !form.date,
                        }"
                        :disabled="
                            !form.ampm ||
                            !form.hour ||
                            !form.minute ||
                            !form.date
                        "
                        >Submit</PrimaryButton
                    >
                    <SecondaryButton @click="closeModal" class="ml-2"
                        >Cancel</SecondaryButton
                    >
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
