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
                // USEP Official Maroon: #D50B00
                'usep': {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444',
                    600: '#D50B00',  // Official USEP Maroon
                    700: '#b30800',
                    800: '#910600',
                    900: '#6f0500',
                    950: '#4a0300',
                },
                'primary': {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444',
                    600: '#D50B00',  // Official USEP Maroon
                    700: '#b30800',
                    800: '#910600',
                    900: '#6f0500',
                    950: '#4a0300',
                },
                'maroon': {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444',
                    600: '#D50B00',  // Official USEP Maroon
                    700: '#b30800',
                    800: '#910600',
                    900: '#6f0500',
                    950: '#4a0300',
                },
                'burgundy': {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444',
                    600: '#D50B00',  // Official USEP Maroon
                    700: '#b30800',
                    800: '#910600',
                    900: '#6f0500',
                    950: '#4a0300',
                }
            }
        },
    },

    plugins: [forms, typography],
};
