import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    maroon: '#7A0C2E',
                    'maroon-hover': '#630824',
                    'maroon-dark': '#4D051B',
                    cream: '#FAF7F2',
                    'cream-card': '#F5EFE6',
                    'cream-border': '#EAE0D5',
                },
            },
        },
    },

    plugins: [forms],
};
