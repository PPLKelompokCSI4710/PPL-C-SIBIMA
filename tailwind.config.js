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
                brand: {
                    primary: 'var(--color-primary)',
                    'primary-dark': 'var(--color-primary-dark)',
                    secondary: 'var(--color-secondary)',
                    'secondary-light': 'var(--color-secondary-light)',
                    accent: 'var(--color-accent)',
                    'accent-light': 'var(--color-accent-light)',
                    success: 'var(--color-success)',
                    'text-primary': 'var(--color-text-primary)',
                    'text-secondary': 'var(--color-text-secondary)',
                    bg: 'var(--color-bg)',
                    white: 'var(--color-white)',
                },
            },
        },
    },

    plugins: [forms],
};
