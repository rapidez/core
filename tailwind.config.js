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
                primary: 'var(--primary)',
                secondary: 'var(--secondary)',
            }
        }
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('@tailwindcss/aspect-ratio'),
        require('tailwind-scrollbar-hide'),
    ]
}
