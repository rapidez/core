const mix = require('laravel-mix');
const path = require('path');
const BundleTracker = require('webpack-bundle-tracker');

mix
    .setPublicPath('public')
    .js('resources/js/app.js', 'public/js').vue()
    .extract({
        test(module) {
            let moduleName = module.nameForCondition()
            return !moduleName.includes('@appbaseio/reactivesearch-vue')
                && !moduleName.includes('vue-slider-component')
                && moduleName.includes('node_modules')
        }
    })
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
        },
        plugins: [
            new BundleTracker({filename: './webpack-stats.json'})
        ]
    });

if (mix.inProduction()) {
    mix.version();
}
