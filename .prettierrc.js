export default {
    // "wrapAttributes": "force-expand-multiline",
    sortTailwindcssClasses: true,
    sortHtmlAttributes: 'code-guide',
    noPhpSyntaxCheck: true,
    semi: false,
    singleQuote: true,
    overrides: [
        {
            files: ['tailwind.config.js'],
            options: {
                quoteProps: 'preserve'
            }
        }
        // Disabled for now as it messes with attributes and directives
        // {
        //     "files": ["*.blade.php"],
        //     "options": {
        //         "parser": "blade"
        //     }
        // }
    ],
}
