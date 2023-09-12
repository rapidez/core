module.exports = {
    content: [
        './resources/views/**/*.blade.php',
        './resources/css/**/*.css',
        './resources/js/**/*.vue',

        './vendor/rapidez/**/*.blade.php',
        './vendor/rapidez/**/*.css',
        './vendor/rapidez/**/*.vue',

        './config/rapidez.php',
        './vendor/rapidez/core/config/rapidez.php',
        './vendor/rapidez/menu/config/menu.php',
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
        },
        container: {
            center: true,
            padding: '1.25rem',
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('tailwind-scrollbar-hide'),
    ],
}
