import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            borderRadius: {
                'sm': '0.5rem',      // 8px (was 0.125rem/2px)
                'md': '0.75rem',     // 12px (was 0.375rem/6px)
                'lg': '1rem',        // 16px (was 0.5rem/8px)
                'xl': '1.5rem',      // 24px (was 0.75rem/12px)
                '2xl': '2rem',       // 32px (was 1rem/16px)
                '3xl': '3rem',       // 48px (was 1.5rem/24px)
            },
            colors: {
                'usep': {
                    50: '#fdf2f2',
                    100: '#fde8e8',
                    200: '#fbd5d5',
                    300: '#f8b4b4',
                    400: '#f98080',
                    500: '#f05252',
                    600: '#e02424',
                    700: '#c81e1e',
                    800: '#9a1a1a',
                    900: '#7c2d12',
                    950: '#450a0a',
                },
                'primary': {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444',
                    600: '#dc2626',
                    700: '#b91c1c',
                    800: '#991b1b',
                    900: '#7f1d1d',
                    950: '#450a0a',
                },
                'maroon': {
                    50: '#fdf2f2',
                    100: '#fde8e8',
                    200: '#fbd5d5',
                    300: '#f8b4b4',
                    400: '#f98080',
                    500: '#f05252',
                    600: '#e02424',
                    700: '#c81e1e',
                    800: '#9a1a1a',
                    900: '#7c2d12',
                    950: '#450a0a',
                },
                'burgundy': {
                    50: '#fdf2f2',
                    100: '#fde8e8',
                    200: '#fbd5d5',
                    300: '#f8b4b4',
                    400: '#f98080',
                    500: '#f05252',
                    600: '#e02424',
                    700: '#c81e1e',
                    800: '#9a1a1a',
                    900: '#7c2d12',
                    950: '#450a0a',
                }
            }
        },
    },

    plugins: [forms, typography],
};
