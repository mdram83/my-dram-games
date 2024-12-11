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
                pulseFast: 'pulse .25s linear infinite',
                glitch: 'glitch 0.2s linear infinite',
            },
            keyframes: {
                fadeOut: {
                    to: { opacity: 0 },
                },
                fadeIn: {
                    to: { opacity: 1 },
                },
                glitch: {
                    '0%': {
                        backgroundPosition: '0 0',
                        backgroundSize: '140%',
                        filter: 'hue-rotate(0deg)',
                    },
                    '10%': {
                        backgroundPosition: '2px 0',
                    },
                    '20%': {
                        backgroundPosition: '-2px 0',
                    },
                    '30%': {
                        backgroundPosition: '6px 0',
                    },
                    '40%': {
                        backgroundPosition: '2px 0',
                    },
                    '50%': {
                        backgroundPosition: '-10px 0',
                    },
                    '60%': {
                        backgroundPosition: '-20px 0',
                    },
                    '70%': {
                        backgroundPosition: '0 -12px',
                    },
                    '80%': {
                        backgroundPosition: '-24px -12px',
                    },
                    '81%': {
                        backgroundPosition: '0 0',
                    },
                    '100%': {
                        backgroundPosition: '0 0',
                        backgroundSize: '140%',
                        filter: 'hue-rotate(360deg)',
                    },
                },

            },
            boxShadow: {
                actionSm: '0 0 2.4vh -0.25vh #f97316',
                actionLg: '0 0 3.6vh -0.50vh #f97316',
                actionSmOp: '0 0 2.4vh -0.25vh #06b6d4',
                actionLgOp: '0 0 3.6vh -0.50vh #06b6d4',
            },
        },
    },

    plugins: [forms],
};
