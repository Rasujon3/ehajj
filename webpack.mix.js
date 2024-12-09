let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack intlTelInput steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
//for vue js
mix.js('resources/js/app.js', 'public/js').vue()
    .postCss('resources/css/app.css', 'public/css', [
        //
    ]);

mix.styles([
    'public/assets/plugins/bootstrap/css/bootstrap4.min.css',
    'public/assets/stylesheets/homepage_custom.css',
    'public/assets/stylesheets/custom.css',
], 'public/css/front.css')
    .combine([
        'public/assets/scripts/oss_dashboard.js',
        'public/url_webservice/action_info.js',
        'public/assets/scripts/custom.js',
    ], 'public/js/admin.js')
    .version();


mix.babel('public/assets/scripts/home_page.js', 'public/assets/scripts/home_page.min.js')
    .babel('public/assets/modules/signup/identity_verify.js', 'public/assets/modules/signup/identity_verify.min.js')
    .babel('public/assets/scripts/common_form_script.js', 'public/assets/scripts/common_form_script.min.js')
    .babel('public/assets/scripts/sonali_payment.js', 'public/assets/scripts/sonali_payment.min.js')
    .babel('public/assets/scripts/custom.js', 'public/assets/scripts/custom.min.js')
    .minify('public/assets/stylesheets/custom.css')
    .minify('public/assets/plugins/jquery/jquery.min.js')
    .minify('public/assets/plugins/bootstrap/js/bootstrap.bundle.min.js')
    .version();
/*
mix.js([
    'public/assets/plugins/bootstrap/js/bootstrap.bundle.js',
    'public/assets/plugins/jquery/jquery.slim.js',
], 'public/js/front.js')
    .styles([
        'public/assets/plugins/bootstrap/css/bootstrap4.min.css',
        'public/assets/stylesheets/custom.css',
    ], 'public/css/front.css')
    .js([
        'public/assets/dist/css/adminlte.min.css',
        'public/assets/scripts/js/main.js',
        'public/assets/scripts/fastclick.js',
        'public/assets/scripts/jquery.slimscroll.js',
        'public/url_webservice/action_info.js',
    ], 'public/js/admin.js')
    .styles([
        'public/assets/stylesheets/css/main.css',
        'public/assets/stylesheets/custom.css',
        'public/assets/stylesheets/custom_theme.css',
    ], 'public/css/admin.css').version();

// minified file generation with versioning
mix.babel('public/assets/scripts/oss_dashboard.js', 'public/assets/scripts/oss_dashboard.min.js')
    .babel('public/assets/scripts/custom.js', 'public/assets/scripts/custom.min.js')
    .babel('public/assets/scripts/home_page.js', 'public/assets/scripts/home_page.min.js')
    .babel('public/assets/scripts/common_form_script.js', 'public/assets/scripts/common_form_script.min.js')
    .minify('public/assets/plugins/toastr/toastr.css')
    .minify('public/assets/plugins/newsTicker/ticker-style.css')
    .minify('public/assets/plugins/datatable/dataTables.bootstrap.css')
    .minify('public/assets/plugins/datatable/responsive.bootstrap.css')
    .minify('public/assets/plugins/jquery-steps/jquery.steps.css')
    .minify('public/assets/plugins/select2/select2.css')
    .minify('public/assets/scripts/common-component.js')
    .minify('public/assets/plugins/jquery/jquery.js')
    .minify('public/assets/scripts/sweetalert2.all.js')
    .minify('public/assets/plugins/toastr/toastr.js')
    .minify('public/assets/scripts/jquery.validate.js')
    .minify('public/assets/plugins/newsTicker/jquery.ticker.js')
    .minify('public/assets/plugins/datatable/jquery.dataTables.js')
    .minify('public/assets/plugins/datatable/dataTables.bootstrap.js')
    .minify('public/assets/plugins/datatable/dataTables.responsive.js')
    .minify('public/assets/plugins/datatable/responsive.bootstrap.js')
    .minify('public/assets/plugins/jquery-steps/jquery.steps.js')
    .minify('public/assets/plugins/select2/select2.js')
    .version();
*/
