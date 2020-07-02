const mix = require('laravel-mix');
const path = require('path');
const mergeManifest = require('./mergeManifest');

mix.extend('mergeManifest', mergeManifest);
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.webpackConfig({
    resolve: {
        alias: {
            '~': path.join(__dirname, './resources/js')
        }
    },
});

mix.sass('resources/js/assets/scss/app.scss', 'dist/css')
    .styles(['resources/js/assets/css/style.css'], 'public/dist/css/style.css')
    .mergeManifest();

if (mix.inProduction()) {
    mix.version();
} else {
    mix.sourceMaps();
}
