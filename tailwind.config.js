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
                    active: color('--border-active', colors.slate[800]),
                    emphasis: color('--border-emphasis', colors.slate[400]),
                    DEFAULT: color('--border', colors.slate[300]),
                    muted: color('--border-muted', colors.slate[100]),
                },

                background: {
                    active: color('--background-active', colors.slate[800]),
                    emphasis: color('--background-emphasis', colors.slate[200]),
                    DEFAULT: color('--background', colors.slate[100]),
                    muted: color('--background-muted', colors.slate[50]),
                },
                backdrop: color('--backdrop', 'rgba(0, 0, 0, 0.4)'),
            },
            keyframes: {
                loading: {
                    '0%': { left: '0%', width: '0%' },
                    '50%': { left: '0%', width: '100%' },
                    '100%': { left: '100%', width: '0%' },
                },
            },
            animation: {
                loading: 'loading 1s infinite',
            },
            zIndex: {
                'header': '100',
                'header-autocomplete-overlay': '10',
                'header-autocomplete': '20',
                'header-dropdown': '30',
                'header-minicart': '30',
                'header-autocomplete-button': '30',

                'notifications': '110',

                'slideover': '120',
                'slideover-overlay': '10',
                'slideover-sidebar': '20',

                'popup': '130',
                'popup-actions': '10',

                'cookie': '140',
            },
            textColor: (theme) => ({
                default: theme('colors.foreground'),
                ...theme('colors.foreground'),
            }),
            borderColor: (theme) => ({
                default: theme('colors.border'),
                ...theme('colors.border'),
            }),
            backgroundColor: (theme) => ({
                default: theme('colors.background'),
                ...theme('colors.background'),
            }),
            ringColor: (theme) => ({
                default: theme('colors.border'),
                ...theme('colors.border'),
            }),
            outlineColor: (theme) => ({
                default: theme('colors.border'),
                ...theme('colors.border'),
            }),
        },
        container: {
            center: true,
            padding: '1.25rem',
        },
    },
    plugins: [forms, typography, scrollbarHide],
}
