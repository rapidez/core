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
                    DEFAULT: 'rgb(var(--primary) / <alpha-value>)', // 1st theme color - `bg-primary`
                    text: 'rgb(var(--primary-text) / <alpha-value>)', // Text color that goes onto the 1st theme color - `text-primary-text`
                },
                secondary: {
                    DEFAULT: 'rgb(var(--secondary) / <alpha-value>)', // 2nd theme color - `bg-secondary`
                    text: 'rgb(var(--secondary-text) / <alpha-value>)', // Text color that goes onto the 2nd theme color - `text-secondary-text`
                },
                default: {
                    DEFAULT: 'rgb(var(--default) / <alpha-value>)', // Default text color - `text-default`
                    100: 'rgb(var(--default-100) / <alpha-value>)', // Default light background color - `bg-default-100`
                    110: 'rgb(var(--default-110) / <alpha-value>)', // Default border color - `border-default-110`
                },
                inactive: 'rgb(var(--inactive) / <alpha-value>)', // Inactive less prominent text color - `text-inactive`
            },
            borderColor: 'rgb(var(--default-110) / <alpha-value>)', // Assigning the default border color - `border`
        },
        container: {
            center: true,
            padding: '1.25rem',
        },
    },
    plugins: [tailwindForms, tailwindTypography, tailwindScrollbarHide],
}
