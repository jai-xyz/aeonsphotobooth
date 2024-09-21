import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{vue,js,ts,jsx,tsx}',
        './node_modules/flowbite/**/*.js',  
    ],

    theme: {
        extend: {
            fontFamily: {
                'body': ['Lato', 'sans-serif' ],
                 'sans': ['Roboto', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                'xxs': '0.675rem',
            },
            colors: {
                primary: {
                    "50": "#f9f4f4",
                    "100": "#f9e7ed",
                    "200": "#fccce0",
                    "300": "#F8B1CB",
                    "400": "#f2bdd5"
                },
                light: {
                    "50": "#ffffff" 
                },
            }
        },
    },
    plugins: [forms,
        require('flowbite/plugin'),
       
    ],
    // content: [
    //     "./node_modules/flowbite/**/*.js"
    // ]
        // forms,
        // require('daisyui')],
        // daisyui: {
        //     themes: ["emerald"],
        //   },
};
