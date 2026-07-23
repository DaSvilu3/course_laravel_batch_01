import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    // Theme is toggled by adding/removing `.dark` on <html>. The boot script
    // in the layout heads sets it before first paint so there is no flash.
    darkMode: 'class',

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
                    50: '#eef4ff',
                    100: '#dae6ff',
                    200: '#bcd3ff',
                    300: '#8eb6ff',
                    400: '#598eff',
                    500: '#3366ff',
                    600: '#1f47f5',
                    700: '#1836e1',
                    800: '#1a30b6',
                    900: '#1c2f8f',
                    950: '#151d54',
                },
                // Cool neutral surface scale used across the dark theme.
                ink: {
                    50: '#f6f7f9',
                    100: '#eceef2',
                    200: '#d5dae1',
                    300: '#b0b9c6',
                    400: '#8492a4',
                    500: '#647087',
                    600: '#4e586d',
                    700: '#3f4759',
                    800: '#2a2f3d',
                    900: '#1a1e29',
                    950: '#11131b',
                },
            },
            boxShadow: {
                // Soft, layered shadows for the "floating card" SaaS look.
                soft: '0 1px 2px 0 rgb(16 24 40 / 0.04), 0 1px 3px 0 rgb(16 24 40 / 0.08)',
                lift: '0 12px 32px -12px rgb(16 24 40 / 0.20), 0 4px 12px -6px rgb(16 24 40 / 0.12)',
                glow: '0 0 0 1px rgb(51 102 255 / 0.10), 0 12px 40px -12px rgb(51 102 255 / 0.45)',
            },
            backgroundImage: {
                'grid-light':
                    'radial-gradient(circle at 1px 1px, rgb(16 24 40 / 0.06) 1px, transparent 0)',
                'grid-dark':
                    'radial-gradient(circle at 1px 1px, rgb(255 255 255 / 0.05) 1px, transparent 0)',
            },
            keyframes: {
                'fade-up': {
                    '0%': { opacity: '0', transform: 'translateY(12px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-8px)' },
                },
            },
            animation: {
                'fade-up': 'fade-up 0.5s cubic-bezier(0.16, 1, 0.3, 1) both',
                float: 'float 6s ease-in-out infinite',
            },
        },
    },

    plugins: [forms],
};
