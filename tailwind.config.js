module.exports = {
    content: [
        "./resources/views/**/*.blade.php",
        "./resources/css/**/*.css",
        "./resources/js/**/*.vue",

        "./vendor/rapidez/**/*.blade.php",
        "./vendor/rapidez/**/*.css",
        "./vendor/rapidez/**/*.vue",

        "./config/rapidez.php",
        "./vendor/rapidez/core/config/rapidez.php",
        "./vendor/rapidez/menu/config/menu.php",
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/tailwind.blade.php",
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: "#2FBC85", // Theme color
                    text: "#FFF", // Text color that goes onto primary color
                },
                secondary: {
                    DEFAULT: "#F97316", // Conversion color
                    text: "#FFF", // Text color that goes onto secondary color
                },
                neutral: "#334155", // Default text color
                inactive: "#64748B", // Inactive text color
                highlight: "#F1F5F9", // Background highlight color
                border: "#F1F5F9", // Border color
            },
            borderColor: {
                DEFAULT: "#F1F5F9", // Border color default so it gets used when only using border
            },
        },
        container: {
            center: true,
            padding: "1.25rem",
        },
    },
    plugins: [
        require("@tailwindcss/forms"),
        require("@tailwindcss/typography"),
        require("@tailwindcss/aspect-ratio"),
        require("tailwind-scrollbar-hide"),
    ],
};
