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
                primary: 'rgb(var(--primary)  / <alpha-value>)', // Text color
                secondary: 'rgb(var(--secondary) / <alpha-value>)', // Text inactive color
                accent: 'rgb(var(--accent) / <alpha-value>)', // Theme color
                enhanced: 'rgb(var(--enhanced) / <alpha-value>)', // Checkout conversion color
                highlight: 'rgb(var(--highlight) / <alpha-value>)' // Background highlight color
            },
            borderColor: {
                DEFAULT: 'rgb(var(--border) / <alpha-value>)',
                border: 'rgb(var(--border) / <alpha-value>)'
            },
        },
        fontFamily: {
            sans: ['Arial', 'sans-serif']
        },
        container: {
            center: true,
            padding: '1.25rem',
        }
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('@tailwindcss/aspect-ratio'),
        require('tailwind-scrollbar-hide'),
    ]
}
