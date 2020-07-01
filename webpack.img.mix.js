const mix = require('laravel-mix');
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
    module: {
        rules: [{
            test: /\.(gif|png|jpe?g|svg)$/i,
            use: [
                'file-loader',
                {
                    loader: 'image-webpack-loader'
                },
            ],
        }]
    }
});

mix.copyDirectory('resources/js/assets/img', 'public/dist/img')
    .mergeManifest();

if (mix.inProduction()) {
    mix.version();
} else {
    mix.sourceMaps();
}
