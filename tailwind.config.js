const colors = require('tailwindcss/colors')

module.exports = {
    purge: [
        './resources/views/**/*.blade.php',
        './resources/css/**/*.css',
        './resources/js/**/*.vue',

        './vendor/rapidez/core/resources/views/**/*.blade.php',
        './vendor/rapidez/core/resources/css/**/*.css',
        './vendor/rapidez/core/resources/js/**/*.vue',

        './vendor/rapidez/account/resources/views/**/*.blade.php',

        './vendor/rapidez/menu/src/config/menu.php',
    ],
    theme: {
        extend: {
            colors: {
                primary: colors.green[700],
                secondary: colors.gray[400],
            },
            width: {
                '400px': '400px',
                '960px': '960px'
            }
        }
    },
    variants: {
        extend: {
            cursor: ['disabled'],
            display: ['group-hover'],
            opacity: ['disabled'],
        }
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('@tailwindcss/aspect-ratio'),
    ]
}
