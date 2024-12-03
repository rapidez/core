import colors from 'tailwindcss/colors'
import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'
import scrollbarHide from 'tailwind-scrollbar-hide'

function color(variable, fallback) {
    return 'color-mix(in srgb, var(' + variable + ', ' + fallback + ') calc(100% * <alpha-value>), transparent)'
}

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
                    DEFAULT: color('--primary', '#2FBC85'),
                    text: color('--primary-text', colors.white),
                },

                secondary: {
                    DEFAULT: color('--secondary', '#202F60'),
                    text: color('--secondary-text', colors.white),
                },

                conversion: {
                    DEFAULT: color('--conversion', colors.green[500]),
                    text: color('--conversion-text', colors.white),
                },

                foreground: {
                    emphasis: color('--foreground-emphasis', colors.slate[900]),
                    DEFAULT: color('--foreground', colors.slate[800]),
                    muted: color('--foreground-muted', colors.slate[600]),
                },

                border: {
                    emphasis: color('--border-emphasis', colors.slate[500]),
                    DEFAULT: color('--border', colors.slate[200]),
                    muted: color('--border-muted', colors.slate[100]),
                },

                background: {
                    emphasis: color('--background-emphasis', colors.slate[200]),
                    DEFAULT: color('--background', colors.slate[100]),
                    muted: color('--background-muted', colors.slate[50]),
                },
            },
            zIndex: {
                header: '100',
                notifications: '110',
                slideover: '120',
                popup: '130',
            },
            textColor: (theme) => theme('colors.foreground'),
            borderColor: (theme) => ({
                default: theme('colors.border'),
                ...theme('colors.border'),
            }),
            backgroundColor: (theme) => theme('colors.background'),
        },
        container: {
            center: true,
            padding: '1.25rem',
        },
    },
    plugins: [forms, typography, scrollbarHide],
}
