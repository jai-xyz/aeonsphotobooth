<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import AButton from "@/Components/AButton.vue";
import UserNavbar from "@/Components/UserNavbar.vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/pagination";
import { Pagination, Navigation } from "swiper/modules";
import "swiper/css/navigation";
import "../../css/custom-styles.css";
import { computed, onMounted } from "vue";
import { ref } from "vue";

const packageExists = computed(() => {
    return props.getAllPackages.some(
        (pkg) => pkg.id === Number(packageData.packageId)
    );
});

const isNotFound = ref(false);

onMounted(() => {
    if (!props.packages || Object.keys(props.packages).length === 0) {
        isNotFound.value = true;
    }
});

// SWIPER
const modules = [Pagination, Navigation];
const onSwiper = (swiper) => {};
const onSlideChange = () => {};

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    auth: Object,
    packages: {
        type: [Array, Object],
        required: true,
    },
    getAllPackages: {
        type: [Array, Object],
        required: true,
    },
});

const packageData = {
    packageId: String(props.packages.id || ""),
    packageName: props.packages.name || "",
    alias: props.packages.alias || "",
    price: props.packages.price || "",
    duration: props.packages.duration || "",
    size: props.packages.size || "",
    size2: props.packages.size2 || "",
    size3: props.packages.size3 || "",
    inclusion: props.packages.inclusion || "",
    note: props.packages.note || "",
    options: props.packages.options || [],
};

// console.log(packageData);
console.log(props.packages);
console.log("Get all ", props.getAllPackages);

function getInclusionLines(inclusion) {
    if (!inclusion) {
        return [];
    }
    const svgIcon = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="inline-block w-5 h-5 text-gray-500 align-middle" fill="#1f2937" width="20" height="20">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15l-4-4 1.41-1.41L11 14.17l5.59-5.59L18 10l-7 7z"/>
        </svg>
    `;
    const lines = inclusion
        .split("\n")
        .map(
            (line) =>
                `<span class="pb-2 inline-block text-sm align-middle">${svgIcon} ${line}</span>`
        );
    return lines;
}
</script>

<template>
    <Head title="Packages" />
    <UserNavbar :auth="auth" :can-login="true" :can-register="true" />
    <div
        class="min-h-screen flex justify-center items-center bg-cover bg-center"
        style="background-image: url('/images/packagebg.png')"
    >
        <div
            class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-1 xxs:m-4 xs:m-10 sm:m-12 md:m-14 lg:grid-cols-2 lg:mx-8 xl:mx-20 2xl:mx-40"
        >
            <div class="relative self-center">
                <div
                    class="text-7xl xxs:text-5xl xs:text-6xl font-extrabold text-[#333535]"
                >
                    {{ packageData.packageName }}
                </div>
                <div
                    class="font-light 2xl:text-xl xl:text-xl lg:text-xl md:text-xl sm:text-xl xs:text-xl text-base text-gray-700"
                >
                    {{ packageData.alias }}
                </div>
                <div class="font-bold text-xl text-gray-500 pt-10 pb-4">
                    INCLUSIONS:
                </div>
                <div class="flex me-8">
                    <div>
                        <div
                            v-for="line in getInclusionLines(
                                packageData.inclusion
                            )"
                            :key="line"
                            class="items-center space-x-2"
                            v-html="line"
                        ></div>
                    </div>
                </div>
            </div>
            <div
                class="relative flex flex-col xxs:mt-4 xs:mt-6 xs:w-full sm:mt-6 sm:w-full md:mt-8 md:w-full lg:w-full 2xl:w-[700px] xs-xs:w-[310px]"
            >
                <div
                    class="relative py-6 bg-primary-600 shadow-sm overflow-hidden rounded-xl x"
                >
                    <div class="ps-6 pb-6 max-w-[250px]">
                        <div class="font-light text-xl text-white">
                            Save more
                        </div>
                        <div class="font-bold text-xl text-white">
                            With Goodplans.
                        </div>
                        <div class="font-light text-sm pt-4 text-white">
                            Lorem ipsum, dolor sit amet consectetur adipisicing
                            elit. Velit facilis illo quod doloremque nihil
                            doloribus id
                        </div>
                    </div>
                    <div>
                        <div class="bg-primary-100">
                            <div class="p-4">
                                <swiper
                                    :modules="modules"
                                    :space-between="50"
                                    navigation
                                    :pagination="{ clickable: true }"
                                    @swiper="onSwiper"
                                    @slideChange="onSlideChange"
                                >
                                    <swiper-slide>
                                        <img
                                            :src="
                                                packageData.packageName ===
                                                'Package A'
                                                    ? '/uploads/slider/photostandee-4r-4shot-2.png'
                                                    : packageData.packageName ===
                                                      'Package B'
                                                    ? '/uploads/slider/magnetic-4r.png'
                                                    : packageData.packageName ===
                                                      'Package C'
                                                    ? '/uploads/slider/polaroid.png'
                                                    : packageData.packageName ===
                                                      'Package D'
                                                    ? '/uploads/slider/single-shot-magnetic.png'
                                                    : ''
                                            "
                                            alt="slide1"
                                            width="auto"
                                            class="mx-auto flex align-center justify-center rounded-lg"
                                        />
                                    </swiper-slide>
                                    <swiper-slide
                                        v-if="
                                            packageData.packageName ===
                                                'Package A' ||
                                            packageData.packageName ===
                                                'Package B'
                                        "
                                    >
                                        <img
                                            :src="
                                                packageData.packageName ===
                                                'Package A'
                                                    ? '/uploads/slider/photostandee-4r-4shot.png'
                                                    : packageData.packageName ===
                                                      'Package B'
                                                    ? '/uploads/slider/magnetic-strips.png'
                                                    : ''
                                            "
                                            alt="slide1"
                                            width="auto"
                                            class="mx-auto flex align-center justify-center rounded-lg"
                                        />
                                    </swiper-slide>

                                    <swiper-slide
                                        v-if="
                                            packageData.packageName ===
                                            'Package A'
                                        "
                                    >
                                        <img
                                            :src="
                                                packageData.packageName ===
                                                'Package A'
                                                    ? '/uploads/slider/photostandee-1.png'
                                                    : ''
                                            "
                                            alt="slide1"
                                            width="auto"
                                            class="mx-auto flex align-center justify-center rounded-lg"
                                        />
                                    </swiper-slide>

                                    <swiper-slide
                                        v-if="
                                            packageData.packageName ===
                                            'Package A'
                                        "
                                    >
                                        <img
                                            :src="
                                                packageData.packageName ===
                                                'Package A'
                                                    ? '/uploads/slider/photostandee-strips.png'
                                                    : ''
                                            "
                                            alt="slide1"
                                            width="auto"
                                            class="mx-auto flex align-center justify-center rounded-lg"
                                        />
                                    </swiper-slide>
                                    ...
                                </swiper>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="font-light text-gray-700 self-ends w-full flex flex-col justify-center align-center"
                >
                    <div
                        v-if="
                            packageData.options &&
                            packageData.options.length > 0
                        "
                        class="text-[#1f2937] flex py-2 mx-auto xs:flex xxs:block"
                    >
                        <strong class="text-4xl self-end font-bold"
                            >₱{{ packageData.options[0].price }}</strong
                        ><span class="self-end text-lg"
                            >/{{ packageData.duration }}</span
                        >
                        <div
                            class="flex flex-col mx-auto xxs:text-sm xs:text-sm lg:text-sm"
                        >
                            <div class="ps-2">NOTE: {{ packageData.note }}</div>
                            <div class="ps-2 self-start">
                                EXTENSION: ₱{{
                                    packageData.options[0].extension
                                }}
                                / hours
                            </div>
                        </div>
                    </div>

                    <AButton
                        class="w-full shadow-lg flex justify-center align-center"
                        :href="`/event/registration/${packageData.packageId}`"
                    >
                        Book Now
                    </AButton>
                </div>
                <div
                    :class="{
                        'flex justify-between xxs:block xs:flex':
                            packageData.packageName !== 'Package A',
                        'flex justify-end':
                            packageData.packageName === 'Package A',
                    }"
                >
                    <AButton
                        v-if="
                            packageData.packageName === 'Package B' ||
                            packageData.packageName === 'Package C' ||
                            packageData.packageName === 'Package D'
                        "
                        class="xxs:w-full w-48 xs:w-48 rounded-s-full rounded-e-full shadow-lg flex justify-between align-end mt-4"
                        :href="`/package/${Number(packageData.packageId) - 1}`"
                    >
                        <div class="rounded-full bg-white p-1 self-start">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 448 512"
                                class="w-4 h-4 rotate-180"
                                fill="#f095b6"
                            >
                                <path
                                    d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"
                                />
                            </svg>
                        </div>
                        {{
                            (() => {
                                if (Array.isArray(props.getAllPackages)) {
                                    const nextPackage =
                                        props.getAllPackages.find(
                                            (pkg) =>
                                                pkg.id ===
                                                Number(packageData.packageId) -
                                                    1
                                        );
                                    if (nextPackage) {
                                        return nextPackage.name === "Package A"
                                            ? "Package A"
                                            : nextPackage.name === "Package B"
                                            ? "Package B"
                                            : nextPackage.name === "Package C"
                                            ? "Package C"
                                            : "";
                                    }
                                }
                                return "";
                            })()
                        }}
                    </AButton>
                    <AButton
                        v-if="
                            packageData.packageName === 'Package A' ||
                            packageData.packageName === 'Package B' ||
                            packageData.packageName === 'Package C'
                        "
                        class="xxs:w-full w-48 xs:w-48 rounded-s-full rounded-e-full shadow-lg flex justify-between align-start mt-4"
                        :href="`/package/${Number(packageData.packageId) + 1}`"
                    >
                        {{
                            (() => {
                                if (Array.isArray(props.getAllPackages)) {
                                    const nextPackage =
                                        props.getAllPackages.find(
                                            (pkg) =>
                                                pkg.id ===
                                                Number(packageData.packageId) +
                                                    1
                                        );
                                    if (nextPackage) {
                                        return nextPackage.name === "Package B"
                                            ? "Package B"
                                            : nextPackage.name === "Package C"
                                            ? "Package C"
                                            : nextPackage.name === "Package D"
                                            ? "Package D"
                                            : "";
                                    }
                                }
                                return "";
                            })()
                        }}
                        <div class="rounded-full bg-white p-1 self-end">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 448 512"
                                class="w-4 h-4"
                                fill="#f095b6"
                            >
                                <path
                                    d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"
                                />
                            </svg>
                        </div>
                    </AButton>
                </div>
            </div>
        </div>
    </div>
</template>
