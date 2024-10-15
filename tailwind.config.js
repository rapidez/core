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
                    DEFAULT: 'rgb(var(--primary) / <alpha-value>)', // Theme color
                    text: 'rgb(var(--primary-text) / <alpha-value>)', // Text color that goes onto primary color
                },
                secondary: {
                    DEFAULT: 'rgb(var(--secondary) / <alpha-value>)', // Secondary theme color
                    text: 'rgb(var(--secondary-text) / <alpha-value>)', // Text color that goes onto secondary color
                },
                neutral: {
                    DETAULT: 'rgb(var(--neutral) / <alpha-value>)', // Default text color
                    light: 'rgb(var(--light) / <alpha-value>)', // Lighter text color
                },
                base: {
                    100: 'rgb(var(--base-100) / <alpha-value>)' // Background color
                },
                enhanced: 'rgb(var(--enhanced) / <alpha-value>)', // Conversion related color
                border: 'rgb(var(--border) / <alpha-value>)', // Border color
                disabled: 'rgb(var(--disabled) / <alpha-value>)', // Color that is used for disabled components
            },
            borderColor: {
                DEFAULT: 'rgb(var(--border) / <alpha-value>)', // Border color default so it gets used when only using border
            },
        },
        container: {
            center: true,
            padding: '1.25rem',
        },
    },
    plugins: [tailwindForms, tailwindTypography, tailwindScrollbarHide],
}
