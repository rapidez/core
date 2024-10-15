module.exports = {
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
                    DEFAULT: 'rgb(var(--primary) / <alpha-value>)', // Primary color
                    text: 'rgb(var(--primary-text) / <alpha-value>)', // Primary text color
                },
                secondary: {
                    DEFAULT: 'rgb(var(--secondary) / <alpha-value>)', // Secondary color
                    text: 'rgb(var(--secondary-text) / <alpha-value>)', // Secondary text color
                },
                neutral: 'rgb(var(--neutral) / <alpha-value>)', // Default text color
                enhanced: 'rgb(var(--enhanced) / <alpha-value>)', // Enhanced color
                disabled: 'rgb(var(--disabled) / <alpha-value>)', // Disabled color
                inactive: {
                    DEFAULT: 'rgb(var(--inactive) / <alpha-value>)', // Inactive text color
                },
                base: 'rgb(var(--base) / <alpha-value>)', // Background color
                border: 'rgb(var(--border) / <alpha-value>)', // Border color
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
    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography'), require('tailwind-scrollbar-hide')],
}
