const mix = require('laravel-mix');
const path = require('path');

mix
    .setPublicPath('public')
    .js('resources/js/app.js', 'public/js').vue()
    .extract()
    .postCss('resources/css/app.css', 'public/css', [
        require('tailwindcss'),
    ]).alias({
        'Vendor': path.join(__dirname, 'vendor'),
    }).options({
        terser: {
            extractComments: false,
        }
    }).webpackConfig({
        output: {
            chunkFilename: 'js/[name].js?id=[chunkhash]',
        }
    });

if (mix.inProduction()) {
    mix.version();
}
