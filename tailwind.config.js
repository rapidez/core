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
                    DEFAULT: 'rgb(var(--secondary) / <alpha-value>)', // Conversion color
                    text: 'rgb(var(--secondary-text) / <alpha-value>)', // Text color that goes onto secondary color
                },
                neutral: 'rgb(var(--neutral) / <alpha-value>)', // Default text color
                inactive: 'rgb(var(--inactive) / <alpha-value>)', // Inactive text color
                highlight: 'rgb(var(--highlight) / <alpha-value>)', // Background highlight color
                border: 'rgb(var(--border) / <alpha-value>)', // Border color
            },
            borderColor: {
                DEFAULT: 'rgb(var(--border) / <alpha-value>)', // Border color default so it gets used when only using border
            },
            zIndex: {
                'header': '100',
                'notifications': '110',
                'slideover': '120',
                'popup': '130'
            }
        },
        container: {
            center: true,
            padding: '1.25rem',
        },
    },
    plugins: [tailwindForms, tailwindTypography, tailwindScrollbarHide],
}
