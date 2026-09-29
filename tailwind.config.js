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
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Ink — глубокий сине-стальной, институциональное доверие
                // (не generic Tailwind indigo/violet).
                ink: {
                    50: '#EFF3FB',
                    100: '#DCE5F5',
                    200: '#B9CCEB',
                    300: '#8FAEDD',
                    400: '#5F8BC9',
                    500: '#3D6CAE',
                    600: '#2C5590',
                    700: '#234374',
                    800: '#1C3660',
                    900: '#16283F',
                    950: '#0D1826',
                },
                // Violet — основной акцентный цвет бренда (CTA, ссылки,
                // выделения) — светлый, энергичный, в духе референса.
                violet: {
                    50: '#F4F2FF',
                    100: '#EAE5FF',
                    200: '#D3C9FF',
                    300: '#B3A0FF',
                    400: '#9575FF',
                    500: '#7C5CFA',
                    600: '#6640E0',
                    700: '#5231B8',
                    800: '#402790',
                    900: '#332073',
                },
                // Coral оставлен как второстепенный тёплый акцент
                // (специальные бейджи/подсветки), не основной CTA-цвет.
                coral: {
                    50: '#FFF3EE',
                    100: '#FFE4D6',
                    200: '#FFC7AD',
                    300: '#FFA179',
                    400: '#FF7A47',
                    500: '#F0592A',
                    600: '#D2401A',
                    700: '#AC3216',
                    800: '#862919',
                    900: '#6E2518',
                },
            },
            boxShadow: {
                soft: '0 1px 2px 0 rgb(13 24 38 / 0.04), 0 4px 16px -4px rgb(13 24 38 / 0.08)',
                card: '0 1px 3px 0 rgb(13 24 38 / 0.06), 0 8px 24px -8px rgb(13 24 38 / 0.12)',
            },
        },
    },

    plugins: [forms],
};
