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

mix.js('resources/js/admin/app.js', 'public/admin/js/app.js')
    .js('resources/eladmin/js/lib/jquery/jquery.min.js', 'public/admin/js/app.js')
    .js('resources/eladmin/js/lib/sticky-kit-master/dist/sticky-kit.min.js', 'public/admin/js/app.js')
    .js('resources/eladmin/js/sidebarmenu.js', 'public/admin/js/app.js')
    .js('resources/eladmin/js/jquery.slimscroll.js', 'public/admin/js/app.js')
    .js('resources/eladmin/js/scripts.js', 'public/admin/js/app.js');

mix.js('resources/js/auth/app.js', 'public/auth/js/app.js')
    .js('resources/eladmin/js/lib/jquery/jquery.min.js', 'public/auth/js/app.js')
    .js('resources/eladmin/js/lib/sticky-kit-master/dist/sticky-kit.min.js', 'public/auth/js/app.js')
    .js('resources/eladmin/js/sidebarmenu.js', 'public/auth/js/app.js')
    .js('resources/eladmin/js/jquery.slimscroll.js', 'public/auth/js/app.js')
    .js('resources/eladmin/js/scripts.js', 'public/auth/js/app.js');

mix.sass('resources/sass/admin/app.scss', 'public/admin/css/app.css');

mix.less('resources/eladmin/style.less', 'public/admin/css/eladmin.css');

mix.js('resources/js/front/app.js', 'public/front/js/app.js');

mix.sass('resources/sass/front/app.scss', 'public/front/css/app.css');
