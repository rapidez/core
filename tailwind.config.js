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
                    DEFAULT: color('--primary', '#2fbc85'),
                    text: color('--primary-text', colors.white),
                },

                secondary: {
                    DEFAULT: color('--secondary', '#202F60'),
                    text: color('--secondary-text', colors.white),
                },

                foreground: {
                    emphasis: color('--foreground-emphasis', colors.gray[800]),
                    DEFAULT: color('--foreground', colors.gray[700]),
                    muted: color('--foreground-muted', colors.gray[500]),
                },

                border: {
                    emphasis: color('--border-emphasis', colors.gray[500]),
                    DEFAULT: color('--border', colors.gray[200]),
                    muted: color('--border-muted', colors.gray[100]),
                },

                background: {
                    emphasis: color('--background-emphasis', colors.gray[200]),
                    DEFAULT: color('--background', colors.gray[100]),
                    muted: color('--background-muted', colors.gray[50]),
                },

                // TODO: check all these colors within the templates and replace
                // neutral: 'rgb(var(--neutral) / <alpha-value>)', // Text
                // inactive: 'rgb(var(--inactive) / <alpha-value>)', // Inactive text
                // highlight: 'rgb(var(--highlight) / <alpha-value>)', // Background highlight
                // enhanced: 'rgb(var(--enhanced) / <alpha-value>)', // Enhanced
                // disabled: 'rgb(var(--disabled) / <alpha-value>)', // Disabled
                // base: 'rgb(var(--base) / <alpha-value>)', // Background
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
