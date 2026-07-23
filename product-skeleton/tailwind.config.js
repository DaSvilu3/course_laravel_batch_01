import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Enums/**/*.php', // badge colours live in the enums
    ],

    theme: {
        extend: {
            fontFamily: {
                // Cairo reads well in Arabic and covers Latin too, so one
                // family works for both directions.
                sans: ['Cairo', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#eef6ff',
                    100: '#d9ebff',
                    200: '#bcdcff',
                    300: '#8ec6ff',
                    400: '#59a6ff',
                    500: '#3383fb',
                    600: '#1d63f0',
                    700: '#164ddc',
                    800: '#1840b2',
                    900: '#1a3b8c',
                },
            },
        },
    },

    plugins: [forms],
};
