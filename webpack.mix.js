const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    // CSS
    .styles([
        'resources/plugins/bootstrap/css/bootstrap.min.css',
        'resources/css/app.css',
        'resources/sass/main.css',
        'resources/sass/dashmix/themes/xsmooth.css',
		'resources/plugins/flatpickr/flatpickr.min.css',
		// 'resources/plugins/select2/select2.min.css'
    ], 'public/css/app.css')
    .copy([
        'resources/plugins/bootstrap/css/bootstrap.min.css.map',
        'resources/sass/dashmix/themes/xsmooth.css.map',
		'resources/plugins/datatables/datatables.css',
		'resources/plugins/datatables/datatables.min.css',
    ], 'public/css')
	.copy('resources/plugins/ckeditor5/ckeditor5.css', 'public/css/ckeditor.css')

    // JS
    .js([
        //'resources/plugins/jquery/jquery-3.6.0.min.js',
        //'resources/plugins/bootstrap/js/bootstrap.bundle.min.js',
        'resources/js/app.js',
        'resources/js/dashmix/app.js',
        'resources/plugins/pusher/pusher.min.js',
		'resources/plugins/flatpickr/flatpickr.min.js',
		'resources/plugins/flatpickr/l10n/ru.js',
		// 'resources/plugins/select2/select2.min.js'
    ], 'public/js/app.js')
    .copy([
        'resources/plugins/bootstrap/js/bootstrap.bundle.min.js.map',
		'resources/plugins/jquery/jquery-3.6.0.min.map',
		'resources/plugins/datatables/datatables.js',
		'resources/plugins/datatables/datatables.min.js',
		'resources/plugins/ckeditor5/ckeditor.js',
		'resources/plugins/ckeditor5/ckeditor.js.map',
    ], 'public/js')
	.copyDirectory([
		'resources/js/dashmix/modules'
	], 'public/js/modules')

	// Pickr
	.copy('resources/plugins/pickr/pickr.min.js', 'public/js/pickr.min.js')
	.copy('resources/plugins/pickr/pickr.min.js.map', 'public/js/pickr.min.js.map')
	.copy('resources/plugins/pickr/classic.min.css', 'public/css/classic.min.css')

    // Media
    .copyDirectory('resources/img/photos', 'public/media/photos')
	.copyDirectory('resources/img/screenshots', 'public/media/screenshots')
	.copyDirectory('resources/favicon', 'public/media/favicons')

	// Разное
	.copy('resources/plugins/datatables/lang/ru/datatables.json', 'public/lang/ru/datatables.json')

	// Данные
	.copyDirectory('database/data', 'public/uploads')

    // Tools
    .browserSync('localhost:8001')
    //.disableNotifications()

    // Options
    .options({
        processCssUrls: true
    });

///////////////////////////////////////////////////////////////////////////////////////////////////
// Front
mix.styles([
	'resources/assets/front/plugins/fontawesome-free/css/all.min.css',
	'resources/assets/front/css/SourceSansPro.css',
	'resources/assets/front/plugins/bootstrap/css/bootstrap.css',
	'resources/assets/front/plugins/select2/css/select2.css',
	'resources/assets/front/plugins/select2-bootstrap4-theme/select2-bootstrap4.css',
	'resources/assets/front/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css',
	'resources/assets/front/plugins/toastr/toastr.min.css',
	'resources/assets/front/plugins/pusher/pusher.min.js',
	'resources/assets/front/css/main.css',
], 'public/assets/front/css/front.css');

mix.scripts([
	'resources/assets/front/plugins/jquery/jquery.min.js',
	'resources/assets/front/plugins/bootstrap/js/bootstrap.bundle.min.js',
	'resources/assets/front/plugins/select2/js/select2.full.js',
	'resources/assets/front/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js',
	'resources/assets/front/plugins/toastr/toastr.min.js',
	'resources/assets/front/plugins/pusher/pusher.min.js',
	'resources/assets/front/js/main.js',
], 'public/assets/front/js/front.js');

mix.copyDirectory('resources/assets/front/plugins/fontawesome-free/webfonts', 'public/assets/front/webfonts');
mix.copyDirectory('resources/assets/googlefonts', 'public/assets/front/fonts');

mix.copy('resources/assets/front/plugins/bootstrap/js/bootstrap.bundle.min.js.map', 'public/assets/front/plugins/bootstrap/js/bootstrap.bundle.min.js.map');
mix.copy('resources/assets/front/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css.map', 'public/assets/front/css/bootstrap-datepicker.css.map');

// Webcam Easy
mix.copy('resources/assets/front/plugins/webcam-easy/webcam-easy.min.js', 'public/js/webcam-easy.min.js')

// Chart.js
mix.copy('resources/plugins/chart.js/chart.min.js', 'public/js/chart.min.js');

// Toastr
mix.copy('resources/assets/front/plugins/toastr/toastr.js.map', 'public/assets/front/js/toastr.js.map');
