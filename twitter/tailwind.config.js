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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'blue': '#1DA1F2',
                'darkblue': '#2795D9',
                'lightblue': '#EFF9FF',
                'dark': '#657786',
                'light': '#AAB8C2',
                'lighter': '#E1E8ED',
                'lightest': '#F5F8FA',
                "primary":"#48C9B0"

              }
            ,
            boxShadow: {
                card: '4px 4px 15px 0 rgba(36, 37, 38, 0.08)',
                dialog: '3px 4px 15px 0 rgba(36, 37, 38, 0.22)',
            },
        },
    },

    plugins: ['tailwindcss/ui'],
};
