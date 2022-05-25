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
		'resources/plugins/select2/select2.min.css'
    ], 'public/css/app.css')
    .copy([
        'resources/plugins/bootstrap/css/bootstrap.min.css.map',
        'resources/sass/dashmix/themes/xsmooth.css.map',
		'resources/plugins/datatables/datatables.css',
		'resources/plugins/datatables/datatables.min.css',
    ], 'public/css')
	.copy('resources/plugins/ckeditor5/sample/styles.css', 'public/css/ckeditor.css')

    // JS
    .js([
        //'resources/plugins/jquery/jquery-3.6.0.min.js',
        //'resources/plugins/bootstrap/js/bootstrap.bundle.min.js',
        'resources/js/app.js',
        'resources/js/dashmix/app.js',
        'resources/plugins/pusher/pusher.min.js',
		'resources/plugins/flatpickr/flatpickr.min.js',
		'resources/plugins/flatpickr/l10n/ru.js',
		'resources/plugins/select2/select2.min.js'
    ], 'public/js/app.js')
    .copy([
        'resources/plugins/bootstrap/js/bootstrap.bundle.min.js.map',
		'resources/plugins/jquery/jquery-3.6.0.min.map',
		'resources/plugins/datatables/datatables.js',
		'resources/plugins/datatables/datatables.min.js',
		'resources/plugins/ckeditor5/build/ckeditor.js',
		'resources/plugins/ckeditor5/build/ckeditor.js.map',
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

	// Разное
	.copy('resources/plugins/datatables/lang/ru/datatables.json', 'public/lang/ru/datatables.json')

	// Данные
	.copyDirectory('database/data', 'public/uploads')

    // Tools
    .browserSync('localhost:8000')
    //.disableNotifications()

    // Options
    .options({
        processCssUrls: true
    });


