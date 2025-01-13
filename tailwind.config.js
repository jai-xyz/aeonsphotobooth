import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.{vue,js,ts,jsx,tsx}",
        "./node_modules/flowbite/**/*.js",
    ],

    theme: {
        extend: {
            fontFamily: {
                body: ["Poppins", "sans-serif"],
                sans: ["Poppins", ...defaultTheme.fontFamily.sans],
                oswald: ["Oswald", "sans-serif"],
            },
            fontWeight: {
                hairline: 100,
                thin: 200,
                light: 300,
                normal: 400,
                medium: 500,
                semibold: 600,
                bold: 700,
                extrabold: 800,
                black: 900,
            },
            fontSize: {
                xxs: "0.675rem",
            },
            colors: {
                primary: {
                    50: "#f9f4f4",
                    100: "#f9e7ed",
                    200: "#fccce0",
                    300: "#F8B1CB",
                    400: "#f2bdd5",
                    500: "#f2bed1",
                    600: "#f095b6",
                    700: "#f28ab0",
                    800: "#f57da9",
                },
                light: {
                    50: "#ffffff",
                },
                yellow: {
                    7: "rgb(240 187 51/0.2)",
                    77: "#BC9021"
                },
                green: {
                    7: "rgb(62 201 114/0.2)",
                    77: "#239F52"
                },
                red: {
                    7: "rgb(201 62 62/0.2)",
                    77: "#9F2323"
                },
                blue: {
                    7: "rgb(63 128 202/0.2)",
                    77: "#23449F"
                }
            },
            screens: {
                xxs: "320px",
                "xxs-xs": { min: "320px", max: "474px" },
                xs: "475px",
                "xs-sm": { min: "475px", max: "639px" },
                sm: "640px",
                "sm-md": { min: "640px", max: "767px" },
                md: "768px",
                "md-lg": { min: "760px", max: "1023px" },
                lg: "1024px",
                "lg-xl": { min: "1024px", max: "1279px" },
                xl: "1280px",
                "xl-2xl": { min: "1280px", max: "1535px" },
                "2xl": "1536px",
            },
        },
    },
    plugins: [forms, require("flowbite/plugin")({
        charts:true,
    }),
]
  
};
