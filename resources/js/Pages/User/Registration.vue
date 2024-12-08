<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import VueCal from "vue-cal";
import "vue-cal/dist/vuecal.css";
import "../../../css/custom-styles.css";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import NumberInput from "@/Components/NumberInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

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
    region_id: "",
    province_id: "",
    city_id: "",
    barangay_id: "",
    street: "",
    date: "",
    hour: "",
    minute: "",
    ampm: "",
    packageid: String(props.pkg.id || ""),
    packagename: props.pkg.name || "",
    packagesize: "",
    backdroptype: "",
    backdropcolor: "",
    suggestion: "",
    user_id: userId,
});


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

const regions = ref([]);
const provinces = ref([]);
const cities = ref([]);
const barangays = ref([]);

const fetchRegions = async () => {
    try {
        const response = await axios.get("/api/address/regions");
        regions.value = response.data;
        // console.log("Regions:", regions.value);
    } catch (error) {
        console.error("Error fetching regions:", error);
    }
};

const fetchProvinces = async () => {
    if (form.region_id) {
        // console.log('Fetching provinces for region:', form.region);
        const selectedRegion = regions.value.find(region => region.id === form.region_id);
        // console.log('Selected region:', selectedRegion);
        if (selectedRegion) {
            const regionId = selectedRegion.region_id;
            try {
                const response = await axios.get(`/api/address/provinces/${regionId}`);
                const data = response.data;
                provinces.value = data;
                // console.log('Provinces:', provinces.value);
            } catch (error) {
                console.error('Error fetching provinces:', error);
            }
        } else {
            console.log('Selected region not found');
        }
    } else {
        console.log('No region selected');
    }
};

const fetchCities = async () => {
    if (form.province_id) {
        // console.log('Fetching cities for province:', form.province);
        const selectedProvince = provinces.value.find(province => province.id === form.province_id);
        // console.log('Selected province:', selectedProvince);
        if (selectedProvince) {
            const provinceId = selectedProvince.province_id;
            try {
                const response = await axios.get(`/api/address/cities/${provinceId}`);
                const data = response.data;
                cities.value = data;
                // console.log('Cities:', cities.value);
            } catch (error) {
                console.error('Error fetching cities:', error);
            }
        } else {
            console.log('Selected province not found');
        }
    } else {
        console.log('No province selected');
    }
};


const fetchBarangays = async () => {
    if (form.city_id) {
        // console.log('Fetching barangays for cities:', form.city);
        const selectedCity = cities.value.find(city => city.id === form.city_id);
        // console.log('Selected city:', selectedCity);
        if (selectedCity) {
            const cityId = selectedCity.city_id;
            try {
                const response = await axios.get(`/api/address/barangays/${cityId}`);
                const data = response.data;
                barangays.value = data;
                // console.log('Barangay:', barangays.value);
            } catch (error) {
                console.error('Error fetching barangays:', error);
            }
        } else {
            console.log('Selected cities not found');
        }
    } else {
        console.log('No city selected');
    }
};


onMounted(fetchRegions);

watch(() => form.region_id, fetchProvinces);
watch(() => form.province_id, fetchCities);
watch(() => form.city_id, fetchBarangays);

const submit = () => {
    if (validateStep()) {
        console.log("Form details:", form);
        form.post(route("user.event.store"));
    } else {
        console.log("Form validation failed:", form.errors);
    }
};
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
     VUECAL CODES and BACKDROP
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
    splits: [
        { id: 1, class: "booth1", label: "Booth 1" },
        { id: 2, class: "booth2", label: "Booth 2" },
    ],
    events: [],
};

if (props.getEvents && Array.isArray(props.getEvents)) {
    // Split days end times
    const splitEndTimes = {
        1: new Date(0),
        2: new Date(0),
    };

    // splitsAndEvents gets the props.getEvents
    splitsAndEvents.events = props.getEvents

        // filter events that are after the min date
        .filter((event) => {
            const startDateTime = new Date(`${event.date}T${event.time}`);
            return startDateTime > minDate.value;
        })

        // map the events to the splitsAndEvents
        .map((event) => {
            const startDateTime = new Date(`${event.date}T${event.time}`);

            // the end time of the event is 2 hours after the start time
            const endDateTime = new Date(
                startDateTime.getTime() + 2 * 60 * 60 * 1000
            );

            // if the start time is after 10pm, the end time will be the next day
            if (startDateTime.getHours() >= 22) {
                endDateTime.setDate(endDateTime.getDate());
            }

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
            if (startDateTime < splitEndTimes[1]) {
                split = 2;
            }

            splitEndTimes[split] = endDateTime;

            return {
                start: startDateTime,
                end: end,
                title: "Event",
                class: "primary",
                split: split,
            };
        });
}

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

    // filtering events from split 2 or booth 2
    const booth2Events = splitsAndEvents.events.filter(
        (event) => event.split === 2
    );

    // getting the event start and end date/time from booth 2
    return booth2Events.some((event) => {
        const eventStartDateTime = new Date(event.start);
        const eventEndDateTime = new Date(event.end);

        return (
            selectedStartDateTime < eventEndDateTime &&
            selectedEndDateTime > eventStartDateTime
        );
    });
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
    return filterBackdropColors(form.backdroptype);
});

const isBackdropColorDisabled = computed(() => {
    return !form.backdroptype;
});

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
        // if (!form.address) {
        //     form.errors.address = "Address is required.";
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
        // } else if (isDateTimeTaken(form.date, `${form.hour}:${form.minute} ${form.ampm}`)) {
        //     form.errors.date = 'Event date and time is already taken.';
        //     isValid = false;
        // }
        // if (!form.hour && !form.minute && !form.ampm) {
        //     form.errors.time = "Time is required.";
        //     isValid = false;
        // }
    } else if (activeStep.value === 2) {
        if (!form.backdroptype) {
            form.errors.backdroptype = "Backdrop type is required.";
            isValid = false;
        }
        if (!form.backdropcolor) {
            form.errors.backdropcolor = "Backdrop color is required.";
            isValid = false;
        }
        if (!form.suggestion) {
            form.errors.suggestion = "Suggestion is required.";
            isValid = false;
        }
    }
    // else if (activeStep.value === 3) {
    // }
    return isValid;
};

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
            return imagePath;
        }
    }

    // return "./backdrop/default.jpg";
});

const packageSizeNoIDSelected = ref([]);
const selectedPackageDetails = ref({});

// Watch for changes in packagename and update packageid and other attributes accordingly
watch(
    () => form.packagename,
    (newPackagename) => {
        const selectedPackage = props.getAllPackages.find(
            (pkg) => pkg.name === newPackagename
        );
        if (selectedPackage) {
            form.packageid = String(selectedPackage.id);
            packageSizeNoIDSelected.value = [
                selectedPackage.size,
                selectedPackage.size2,
                selectedPackage.size3,
                selectedPackage.size4,
                selectedPackage.size5,
            ].filter((size) => size);
            selectedPackageDetails.value = {
                name: selectedPackage.name,
                description: selectedPackage.description,
                price: selectedPackage.price,
            };
        }
    }
);
</script>

<template>
    <Head title="Registration" />

    <AuthenticatedLayout :auth="auth">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Event Registration
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg px-14 py-6"
                >
                    <form @submit.prevent="submit">
                        <!-- STEP 1: BACKDROP DETAILS -->
                        <div v-if="activeStep === 1">
                            <h1 class="text-center">Step 1: Event Details</h1>

                            <div
                                class="grid grid-cols-1 mt-6 md:grid-cols-3 gap-4"
                            >
                                <div>
                                    <InputLabel
                                        for="event"
                                        value="Title of the Event"
                                    />
                                    <TextInput
                                        id="event"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.event"
                                        required
                                        autofocus
                                        autocomplete="event"
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
                                    />
                                    <TextInput
                                        id="contactperson"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.contactperson"
                                        required
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
                                    />
                                    <NumberInput
                                        id="contactno"
                                        type="text"
                                        class="mt-1 block w-full"
                                        minlength="11"
                                        maxlength="11"
                                        v-model="form.contactno"
                                        required
                                        autocomplete="off"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.contactno"
                                    />
                                </div>

                                <div>
                                    <InputLabel for="date" value="Date" />
                                    <TextInput
                                        id="date"
                                        type="date"
                                        class="mt-1 block w-full"
                                        v-model="form.date"
                                        :required="validateDate"
                                        autocomplete="date"
                                        :min="minInputDate"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.date"
                                    />
                                </div>

                                <div>
                                    <InputLabel for="time" value="Time" />
                                    <div class="flex">
                                        <select
                                            id="hour"
                                            placeholder="Hour"
                                            class="select select-info rounded-e-none border-gray-300 mt-1 block w-full"
                                            v-model="form.hour"
                                            :required="validateDate"
                                        >
                                            <option disabled value="">
                                                Hour
                                            </option>
                                            <option
                                                v-for="hour in hours"
                                                :key="hour"
                                                :value="hour"
                                            >
                                                {{ hour }}
                                            </option>
                                        </select>

                                        <select
                                            id="minute"
                                            class="select select-info rounded-none border-gray-300 mt-1 block w-full"
                                            v-model="form.minute"
                                            :required="validateDate"
                                        >
                                            <option disabled value="">
                                                Minute
                                            </option>
                                            <option
                                                v-for="minute in minutes"
                                                :key="minute"
                                                :value="minute"
                                            >
                                                {{ minute }}
                                            </option>
                                        </select>

                                        <select
                                            id="ampm"
                                            class="select select-info rounded-s-none border-gray-300 mt-1 block w-full"
                                            v-model="form.ampm"
                                            :required="validateDate"
                                        >
                                            <option disabled value="">
                                                AM/PM
                                            </option>
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
                                        <InputError
                                            class="mt-2"
                                            :message="form.errors.hour"
                                        />

                                        <InputError
                                            class="mt-2"
                                            :message="form.errors.minute"
                                        />
                                        <InputError
                                            class="mt-2"
                                            :message="form.errors.ampm"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div
                                class="grid grid-cols-1 mt-6 md:grid-cols-3 gap-4"
                            >
                                <div>
                                    <InputLabel for="region" value="Region" />
                                    <select
                                        v-model="form.region_id"
                                        @change="fetchProvinces"
                                        class="mt-1 block w-full"
                                    >
                                        <option disabled value="">
                                            Select Region
                                        </option>
                                        <option
                                            v-for="region in regions"
                                            :key="region.id"
                                            :value="region.id"
                                        >
                                            {{ region.name }}
                                        </option>
                                    </select>
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.region_id"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="province"
                                        value="Province"
                                    />
                                    <select
                                        v-model="form.province_id"
                                        @change="fetchCities"
                                        class="mt-1 block w-full"
                                    >
                                        <option disabled value="">
                                            Select Province
                                        </option>
                                        <option
                                            v-for="province in provinces"
                                            :key="province.id"
                                            :value="province.id"
                                        >
                                            {{ province.name }}
                                        </option>
                                    </select>
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.province_id"
                                    />
                                </div>

                                <div>
                                    <InputLabel for="city" value="City" />
                                    <select
                                        v-model="form.city_id"
                                        @change="fetchBarangays"
                                        class="mt-1 block w-full"
                                    >
                                        <option disabled value="">
                                            Select City
                                        </option>
                                        <option
                                            v-for="city in cities"
                                            :key="city.id"
                                            :value="city.id"
                                        >
                                            {{ city.name }}
                                        </option>
                                    </select>
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.city_id"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="barangay"
                                        value="Barangay"
                                    />
                                    <select
                                        v-model="form.barangay_id"
                                        class="mt-1 block w-full"
                                    >
                                        <option disabled value="">
                                            Select Barangay
                                        </option>
                                        <option
                                            v-for="barangay in barangays"
                                            :key="barangay.id"
                                            :value="barangay.id"
                                        >
                                            {{ barangay.name }}
                                        </option>
                                    </select>
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.barangay_id"
                                    />
                                </div>

                                <div>
                                    <InputLabel for="street" value="Street" />
                                    <TextInput
                                        id="street"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.street"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.street"
                                    />
                                </div>
                            </div>

                            <br /><br />
                            <div class="row">
                                <div
                                    class="col-12 flex justify-center align-center"
                                >
                                    <VueCal
                                        :events="splitsAndEvents.events"
                                        class="vuecal mx-12"
                                        events-count-on-year-view
                                        today-button
                                        sticky-split-labels
                                        style="
                                            width: 150%;
                                            height: 500px;
                                            max-width: 1500px;
                                        "
                                        :disable-views="['day']"
                                        active-view="month"
                                        timeFormat="hh:mm {AM}"
                                        :min-date="minDate"
                                        :split-days="splitsAndEvents.splits"
                                    >
                                    </VueCal>
                                </div>
                            </div>
                        </div>

                        <!-- STEP 2: EVENT DATE DETAILS -->
                        <!-- DISPLAYS WHEN A PACKAGE IS SELECTED -->
                        <div v-if="activeStep === 2 && props.pkg.id">
                            <h1 class="text-center">
                                Step 2: Backdrop Details
                            </h1>
                            <div
                                class="grid grid-cols-1 mt-6 md:grid-cols-3 gap-4"
                            >
                                <div>
                                    <InputLabel
                                        for="packageid"
                                        value="Package ID"
                                    />
                                    <NumberInput
                                        id="packageid"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.packageid"
                                        :value="props.pkg.id"
                                        required
                                        readonly
                                        autocomplete="off"
                                    />

                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.packageid"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="packagename"
                                        value="Package Name"
                                    />
                                    <TextInput
                                        id="packagename"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.packagename"
                                        :value="props.pkg.name"
                                        required
                                        readonly
                                        autocomplete="off"
                                    />

                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.packagename"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="packagesize"
                                        value="Size"
                                    />
                                    <select
                                        class="select select-info border-gray-300 mt-1 block w-full"
                                        id="packagesize"
                                        name="packagesize"
                                        required
                                        v-model="form.packagesize"
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
                                </div>

                                <div>
                                    <InputLabel
                                        for="backdroptype"
                                        value="Backdrop Type"
                                    />
                                    <select
                                        class="select select-info border-gray-300 mt-1 block w-full"
                                        id="backdroptype"
                                        name="backdroptype"
                                        required
                                        v-model="form.backdroptype"
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
                                        :message="form.errors.backdroptype"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="backdropcolor"
                                        value="Backdrop Color"
                                    />
                                    <select
                                        class="select select-info border-gray-300 mt-1 block w-full"
                                        id="backdropcolor"
                                        name="backdropcolor"
                                        required
                                        v-model="form.backdropcolor"
                                        :disabled="isBackdropColorDisabled"
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
                                        :message="form.errors.backdropcolor"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="suggestion"
                                        value="Suggestions"
                                    />
                                    <TextInput
                                        id="suggestion"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.suggestion"
                                        required
                                        autocomplete="suggestion"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.suggestion"
                                    />
                                </div>

                                <div>
                                    <img
                                        width="500px"
                                        :src="backdropImage"
                                        alt="backdropimage"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- DISPLAYS THERE'S NO PACKAGE SELECTED -->
                        <div v-else-if="activeStep === 2 && !props.pkg.id">
                            <h1 class="text-center">
                                Step 2: Backdrop Details
                            </h1>
                            <div
                                class="grid grid-cols-1 mt-6 md:grid-cols-3 gap-4"
                            >
                                <div>
                                    <InputLabel
                                        for="packageid"
                                        value="Package ID"
                                    />
                                    <NumberInput
                                        id="packageid"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.packageid"
                                        required
                                        readonly
                                        autocomplete="off"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.packageid"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="packagename"
                                        value="Package Name"
                                    />
                                    <select
                                        class="select select-info border-gray-300 mt-1 block w-full"
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
                                            {{ allpkg.name }}
                                        </option>
                                    </select>
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.packagename"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="packagesize"
                                        value="Package Size"
                                    />
                                    <select
                                        class="select select-info border-gray-300 mt-1 block w-full"
                                        id="packagesize"
                                        name="packagesize"
                                        required
                                        v-model="form.packagesize"
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
                                        :message="form.errors.packagesize"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="selectedPackagePrice"
                                        value="Price"
                                    />
                                    <TextInput
                                        id="selectedPackagePrice"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="selectedPackageDetails.price"
                                        readonly
                                        autocomplete="off"
                                    />
                                </div>
                                <div>
                                    <InputLabel
                                        for="backdroptype"
                                        value="Backdrop Type"
                                    />
                                    <select
                                        class="select select-info border-gray-300 mt-1 block w-full"
                                        id="backdroptype"
                                        name="backdroptype"
                                        required
                                        v-model="form.backdroptype"
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
                                        :message="form.errors.backdroptype"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="backdropcolor"
                                        value="Backdrop Color"
                                    />
                                    <select
                                        class="select select-info border-gray-300 mt-1 block w-full"
                                        id="backdropcolor"
                                        name="backdropcolor"
                                        required
                                        v-model="form.backdropcolor"
                                        :disabled="isBackdropColorDisabled"
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
                                        :message="form.errors.backdropcolor"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="suggestion"
                                        value="Suggestions"
                                    />
                                    <TextInput
                                        id="suggestion"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.suggestion"
                                        required
                                        autocomplete="suggestion"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.suggestion"
                                    />
                                </div>

                                <div>
                                    <img
                                        width="500px"
                                        :src="backdropImage"
                                        alt="backdropimage"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- STEP 3: EVENT DETAILS -->
                        <!-- <div v-if="activeStep === 3">
                            <h1 class="text-center">Step 3: Event Details</h1>
                            <div
                                class="grid grid-cols-1 mt-6 md:grid-cols-3 gap-4"
                            ></div>
                        </div> -->

                        <input
                            type="hidden"
                            name="user_id"
                            :value="form.user_id"
                        />

                        <PrimaryButton
                            class="mt-6 flex float-left"
                            @Click="prevStep"
                            v-if="activeStep > 1"
                        >
                            Back
                        </PrimaryButton>

                        <PrimaryButton
                            class="mt-6 flex float-right"
                            v-if="activeStep < 2"
                            @Click="nextStep"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Next >
                        </PrimaryButton>

                        <PrimaryButton
                            class="mt-6 flex float-right"
                            v-if="activeStep === 2"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Register
                        </PrimaryButton>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
