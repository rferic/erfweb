const mix = require('laravel-mix');

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

mix.js('resources/js/admin/app.js', 'public/admin/js/app.js');

mix.js('resources/js/auth/app.js', 'public/auth/js/app.js');

mix.sass('resources/vue-argon/src/assets/scss/argon.scss', 'public/admin/css/argon.css');

mix.sass('resources/sass/admin/app.scss', 'public/admin/css/app.css');

mix.js('resources/js/front/app.js', 'public/front/js/app.js');

mix.sass('resources/sass/front/app.scss', 'public/front/css/app.css');
