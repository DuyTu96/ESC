const mix = require('laravel-mix');
const path = require('path');
const mergeManifest = require('./mergeManifest');
const ChunkRenamePlugin = require('webpack-chunk-rename-plugin');

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
    output: {
        chunkFilename: 'dist/js/chunks/[name].js'
    },
    plugins: [
        new ChunkRenamePlugin({
            initialChunksWithEntry: true,
            '/dist/js/user.js': 'dist/js/user.js',
            '/dist/js/admin.js': 'dist/js/admin.js',
            '/dist/js/portal.js': 'dist/js/portal.js',
            '/dist/js/vendor': 'dist/js/vendor.js',
        }),
    ],
    resolve: {
        extensions: ['.js', '.json', '.vue'],
        alias: {
            '~': path.join(__dirname, './resources/js')
        },
    }
});

mix.js('resources/js/user.js', 'dist/js')
    .js('resources/js/admin.js', 'dist/js')
    .js('resources/js/portal.js', 'dist/js')
    .scripts([
        'resources/js/assets/js/date.js',
        'resources/js/assets/js/form.js',
        'resources/js/assets/js/form02.js',
        'resources/js/assets/js/main.js',
        'resources/js/assets/js/stickyfill.min.js'
    ], 'public/dist/js/main.js')
    .extract()
    .mergeManifest();

if (mix.inProduction()) {
    mix.version();
} else {
    mix.sourceMaps();
}
