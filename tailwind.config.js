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
                primary: 'var(--primary)', // Text color
                secondary: 'var(--secondary)', // Text inactive color
                accent: 'var(--accent)', // Theme color
                enhanced: 'var(--enhanced)', // Checkout conversion color
                highlight: 'var(--highlight)' // Background highlight color
            },
            borderColor: {
                DEFAULT: 'var(--border)'
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
