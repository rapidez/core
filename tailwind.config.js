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
                    DEFAULT: 'var(--primary)', // Text color
                    100: 'var(--primary-100)' // Light color
                },
                secondary: 'var(--secondary)', // Inactive text color
                accent: 'var(--accent)', // Theme color
                enhanced: 'var(--enhanced)', // Checkout conversion color
            }
        },
        borderColor: {
            DEFAULT: 'var(--border)'
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
