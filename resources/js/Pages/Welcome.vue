<script setup>
// import { ref } from "vue";
// import ApplicationLogo from "@/Components/ApplicationLogo.vue";
// import Dropdown from "@/Components/Dropdown.vue";
// import DropdownLink from "@/Components/DropdownLink.vue";
// import NavLink from "@/Components/NavLink.vue";
// import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
// import AButton from "@/Components/AButton.vue";
// import AButtonSecondary from "@/Components/AButtonSecondary.vue";
import { computed, onMounted } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import AButton from "@/Components/AButton.vue";
// const showingNavigationDropdown = ref(false);
import UserNavbar from "@/Components/UserNavbar.vue";
import "../../css/custom-styles.css";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    auth: Object,
    packages: {
        type: [Array, Object],
        required: true,
    },
});
console.log(props.packages);

function formattedAlias(name) {
    return name === "Package A"
        ? "Photo <strong>Standee</strong> in Strips or 4R for 2 hours"
        : name === "Package B"
        ? "Photo <strong>Magnetic</strong> in Strips or 4R for 2 hours"
        : name === "Package C"
        ? "<strong>Polaroid</strong> Style in Photo <strong>Magnet</strong> or 4R for 2 hours"
        : name === "Package D"
        ? "Single shot <strong>Standee</strong> Package for 2 hours"
        : "";
}

function displayLetter(name) {
    return name === "Package A"
        ? "/images/a.png"
        : name === "Package B"
        ? "/images/b.png"
        : name === "Package C"
        ? "/images/c.png"
        : name === "Package D"
        ? "/images/d.png"
        : "";
}

function displayGIF(name) {
    return name === "Photo Man"
        ? "/images/photoman.png"
        : name === "Boomerang Booth"
        ? "/images/boomerang.gif"
        : "";
}

function getInclusionLines(inclusion, name) {
    if (!inclusion) {
        return [];
    }
    const svgIcon = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="inline-block w-4 h-4 text-gray-500 align-middle" fill="#1f2937" >
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15l-4-4 1.41-1.41L11 14.17l5.59-5.59L18 10l-7 7z"/>
        </svg>
    `;
    const lines = inclusion
        .split("\n")
        .map(
            (line) =>
                `<span class="pb-1 inline-block text-xs align-middle">${svgIcon} ${line}</span>`
        );
    return lines ? lines.slice(0, 5) : "";
}

// onMounted(() => {
//   const script = document.createElement('script');
//   script.src = "https://static.elfsight.com/platform/platform.js";
//   script.async = true;
//   document.body.appendChild(script);
// });
</script>

<template>
    <Head title="Welcome" />
    <Head>
        <link rel="icon" href="/images/favicon.ico" type="image/x-icon" />
    </Head>

    <div>
        <div class="min-h-screen bg-[#f2f2f2]">
            <UserNavbar :auth="auth" :can-login="true" :can-register="true" />

            <!-- BANNER -->
            <div class="flex justify-center items-center mx-auto">
                <img
                    src="/images/bannerbgnew.png"
                    alt="banner image"
                    class="h-full object-cover xs:min-h-[500px] xxs:min-h-[320px]"
                />
            </div>

            <!-- PACKAGES -->
            <div class="relative px-6 mx-auto bg-[#f2f2f2] 2xl:mx-16">
                <div
                    class="col-span-full text-start font-bold text-4xl text-gray-800 mb-6 font-oswald"
                >
                    Rates and Packages
                </div>
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-2 me-2"
                >
                    <div
                        v-for="pkg in packages"
                        :key="pkg?.id"
                        class="flex justify-center"
                    >
                        <div
                            v-if="pkg"
                            class="relative w-full sm:w-full md:w-full lg:w-full 2xl:w-[400px] pkg-shadow bg-white shadow-sm overflow-hidden rounded-tr-xl rounded-br-xl py-2"
                        >
                            <img
                                class="absolute top-0 right-0"
                                :src="displayLetter(pkg.name)"
                                v-if="displayLetter(pkg.name)"
                                :class="
                                    displayLetter(pkg.name) == '/images/b.png'
                                        ? 'w-12 mt-1 mr-4'
                                        : displayLetter(pkg.name) ==
                                          '/images/d.png'
                                        ? 'w-16 mt-2 mr-2'
                                        : 'w-16 mt-5 mr-4'
                                "
                                alt="letter image"
                            />
                            <div class="px-4 pt-8 pb-6">
                                <strong class="text-lg">{{ pkg.name }}</strong>
                            </div>
                            <div class="px-4 pb-6">
                                <i
                                    class="text-gray-800 text-lg tracking-wider leading-4"
                                >
                                    <div
                                        v-html="formattedAlias(pkg.name)"
                                    ></div>
                                </i>
                            </div>

                            <div class="px-4 pb-4">
                                <div>
                                    <div
                                        v-for="line in getInclusionLines(
                                            pkg.inclusion,
                                            pkg.name
                                        )"
                                        :key="line"
                                        class="items-center space-x-2"
                                        v-html="line"
                                    ></div>
                                    <div
                                        v-if="
                                            pkg.inclusion &&
                                            pkg.inclusion.split('\n').length > 4
                                        "
                                        class="text-primary-800 cursor-pointer"
                                    >
                                        <Link :href="`/package/${pkg.id}`">
                                            More details...
                                        </Link>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="border border-dashed border-gray-300 mx-1"
                            ></div>

                            <div
                                v-if="pkg.options && pkg.options.length > 0"
                                class="text-lg px-4 pt-4"
                            >
                                ₱<strong>{{ pkg.options[0].price }}</strong
                                >/{{ pkg.duration }}
                            </div>

                            <div class="px-4">
                                <AButton
                                    class="w-full my-4 mx-auto shadow-lg flex justify-center align-center"
                                    :href="`/event/registration/${pkg.id}`"
                                >
                                    Book Now
                                </AButton>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="flex flex-col justify-center items-center gap-4 pt-4 col-span-full"
                >
                    <div
                        class="col-span-full text-center font-bold text-xl text-primary-800 font-oswald py-2"
                    >
                        Exciting Packages Coming Soon!
                    </div>
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-2 mx-6"
                    >
                        <!-- -->
                        <div
                            class="w-full sm:w-full md:w-full lg:w-full 2xl:w-[400px] shadow-sm overflow-hidden rounded-xl"
                        >
                            <img
                                class=""
                                src="/images/boomerang.gif"
                                alt="boomerang gif"
                            />
                        </div>
                        <div
                            class="w-full sm:w-full md:w-full lg:w-full 2xl:w-[400px] shadow-sm overflow-hidden rounded-xl"
                        >
                            <img
                                class=""
                                src="/images/boomerang.gif"
                                alt="boomerang gif"
                            />
                        </div>
                    </div>

                    <div
                        class="flex flex-col justify-center items-center gap-4 py-4"
                    >
                        <div class="flex items-center gap-x-4">
                            <div class="flex items-center gap-2">
                                <img
                                    class="w-16"
                                    src="/images/dti.png"
                                    alt="DTI logo"
                                />
                            </div>

                            <div class="text-center">
                                <span> We Are Legitimate! </span>
                                <div
                                    class="flex align-center gap-x-2 justify-center"
                                >
                                    <span class="text-xs">DTI Registered</span>
                                    <span class="text-xs">BIR Registered</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <img
                                    class="w-16"
                                    src="/images/bir.png"
                                    alt="BIR logo"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SPECIAL PACKAGES & ADD-ONS -->
            <img
                src="/images/ribbon.png"
                alt="ribbon image"
                class="h-full w-full object-fill bg-[#f2f2f2]"
            />
            <div
                class="relative mx-auto py-8"
                style="
                    background: url('/images/board.jpeg');
                    background-size: cover;
                    background-position: center;
                    background-repeat: no-repeat;
                    margin-left: 10px;
                    margin-right: 10px;
                    border-radius: 16px;
                "
            >
                <div
                    class="col-span-full text-end font-bold text-4xl text-white mb-2 px-6 font-oswald 2xl:mx-16"
                >
                    Special Packages and Add-ons
                </div>
                <div
                    class="relative flex justify-center items-center rounded-lg 2xl:mx-16"
                >
                    <div
                        class="relative grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-10 m-8"
                    >
                        <div class="relative flex items-center justify-center">
                            <img
                                src="/images/polaroid1.png"
                                class="w-full sm:w-[360px] md:w-full lg:w-full 2xl:w-[400px] drop-shadow-xl"
                                alt="Polaroid 1"
                            />
                            <div
                                class="absolute pb-6 text-center font-oswald text-xl text-gray-700"
                                style="transform: rotate(-9deg)"
                            >
                                special backdrop designs
                                <p class="pt-auto text-sm px-8 text-gray-600">
                                    starry night backdrop, grass wall backdrop,
                                    backdrop styling, personalized tarpaulin
                                    backdrop
                                </p>
                            </div>
                        </div>
                        <div class="relative flex items-center justify-center">
                            <img
                                src="/images/polaroid2.png"
                                class="w-full sm:w-[360px] md:w-full lg:w-full 2xl:w-[400px] drop-shadow-xl"
                                alt="Polaroid 2"
                            />
                            <div
                                class="absolute pb-6 text-center xxs:px-8 font-oswald text-xl text-gray-700"
                                style="transform: rotate(7deg)"
                            >
                                print out designs and accessories
                                <p class="text-sm px-8 text-gray-600">
                                    guest books, message boards, additional
                                    print outs
                                </p>
                            </div>
                        </div>
                        <div class="relative flex items-center justify-center">
                            <img
                                src="/images/polaroid3.png"
                                class="w-full sm:w-[360px] md:w-full lg:w-full 2xl:w-[400px] drop-shadow-xl"
                                alt="Polaroid 3"
                            />
                            <div
                                class="absolute pb-6 text-center font-oswald text-xl text-gray-700"
                                style="transform: rotate(-4deg)"
                            >
                                event add-ons
                                <p class="text-sm px-8 text-gray-600">
                                    flashdrive, photo/message display stand, red
                                    carpet
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="flex justify-center text-sm bg-white rounded-lg shadow-lg p-2 xxs-xs:mx-4 xs-sm:mx-4 sm-md:mx-8 md-lg:mx-16 lg-xl:mx-40 xl-2xl:mx-64 mx-96"
                >
                    Note: Please send us your desired design, additional charges
                    may apply for complicated ones.
                </div>
            </div>

            <!-- REVIEWS AND GOOGLE MAPS -->
            <div class="flex flex-col min-h-100">
                <!-- Main content -->
                <div class="flex-grow">
                    <!-- Section with background image -->
                    <div
                        class="relative flex flex-col items-center bg-white mt-4"
                    >
                        <img
                            id="background"
                            class="absolute inset-0 w-full h-full object-cover"
                            src="images/finalbanner4.png"
                            alt=""
                        />
                        <div
                            class="relative col-span-full font-light text-4xl font-oswald my-8 text-center"
                        >
                            <span class="text-blue-500">Facebook</span> Reviews
                            <p class="text-lg">We've got their trust</p>
                            <div
                                class="relative flex flex-col items-center mt-4"
                            >
                                <iframe
                                    class="rounded-3xl drop-shadow-xl xs:h-[200px] sm:w-[500px] sm:h-[225px] md:w-[600px] md:h-[250px] lg:w-[800px] lg:h-[350px] xl:w-[1000px] xl:h-[450px] 2xl:w-[1100px] 2xl:h-[500px]"
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1182.764288940067!2d120.92650159495922!3d14.365145408701999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397d49c7bd107c3%3A0x628349f6e9f16df5!2sAeon&#39;s%20Photobooth!5e0!3m2!1sen!2sph!4v1735768675678!5m2!1sen!2sph"
                                    allowfullscreen=""
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                ></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div
                    class="relative mx-auto pb-8 bg-gradient-to-tr from-primary-200 to-primary-800 w-full"
                >
                    <div
                        class="relative flex justify-center items-center rounded-lg 2xl:mx-16"
                    >
                        <div
                            class="relative grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-10 m-8"
                        >
                            <div
                                class="relative flex items-center justify-center"
                            >
                                <img
                                    src="/images/logo.png"
                                    class="w-full sm:w-[360px] md:w-full lg:w-full 2xl:w-[400px] drop-shadow-xl"
                                />
                                <div class="text-xs">
                                    <!-- Make your event unforgettable with our expert photo booth services! With 7+ years of experience and over 2,000 events covered, we bring fun, creativity, and cherished memories to every celebration. Explore our packages and book us today! -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
            </div>
        </div>
    </div>

    <!-- <script type="text/javascript">!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});</script>
<script type="text/javascript">window.Beacon('init', 'decd8946-c894-47ad-8efa-9714bc2e8eec')</script> -->
</template>
