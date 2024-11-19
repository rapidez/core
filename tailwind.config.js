import tailwindForms from '@tailwindcss/forms'
import tailwindTypography from '@tailwindcss/typography'
import tailwindScrollbarHide from 'tailwind-scrollbar-hide'

export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/css/**/*.css',
        './resources/js/**/*.vue',

        './vendor/rapidez/**/*.blade.php',
        './vendor/rapidez/**/*.css',
        './vendor/rapidez/**/*.vue',

        './config/rapidez/frontend.php',
        './vendor/rapidez/core/config/rapidez/frontend.php',
        './config/rapidez/menu.php',
        './vendor/rapidez/menu/config/rapidez/menu.php',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/tailwind.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: 'rgb(var(--primary) / <alpha-value>)',
                    text: 'rgb(var(--primary-text) / <alpha-value>)',
                },
                secondary: {
                    DEFAULT: 'rgb(var(--secondary) / <alpha-value>)',
                    text: 'rgb(var(--secondary-text) / <alpha-value>)',
                },
                enhanced: {
                    DEFAULT: 'rgb(var(--enhanced) / <alpha-value>)',
                    text: 'rgb(var(--enhanced-text) / <alpha-value>)',
                },
                neutral: {
                    DEFAULT: 'rgb(var(--neutral) / <alpha-value>)',
                    100: 'rgb(var(--neutral-100) / <alpha-value>)',
                    110: 'rgb(var(--neutral-110) / <alpha-value>)',
                },
                inactive: 'rgb(var(--inactive) / <alpha-value>)',
            },
            borderColor: {
                DEFAULT: 'rgb(var(--neutral-110) / <alpha-value>)',
            },
        },
        container: {
            center: true,
            padding: '1.25rem',
        },
    },
    plugins: [tailwindForms, tailwindTypography, tailwindScrollbarHide],
}
