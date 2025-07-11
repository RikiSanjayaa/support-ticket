import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/awcodes/filament-*/**/*.blade.php',
        './vendor/guava/filament-*/**/*.blade.php',
        './vendor/bezhansalleh/filament-*/**/*.blade.php',
        './vendor/jeffgreco13/filament-*/**/*.blade.php',
        './vendor/malzariey/filament-*/**/*.blade.php',
        './vendor/ryangjchandler/filament-*/**/*.blade.php',
        './vendor/spatie/laravel-*/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],

    darkMode: 'class'
};
