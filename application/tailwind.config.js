import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.*',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            animation: {
                fadeout: 'fadeOut .5s ease-in-out',
            },
            keyframes: {
                fadeOut: {
                    to: { opacity: 0 },
                },
                fadeIn: {
                    to: { opacity: 1 },
                },
            },
            boxShadow: {
                actionSm: '0 0 2.4vh -0.25vh #f97316',
                actionLg: '0 0 3.6vh -0.50vh #f97316',
            }
        },
    },

    plugins: [forms],
};
