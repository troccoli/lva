var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function (mix) {
    // Styles
    mix.sass('app.scss');
    mix.sass('data-management.scss');

    // Javascript
    mix.browserify('app.js');
    mix.browserify('confirm-delete.js');

    // Versioning
    mix.version([
        // Stylesheets
        'css/app.css',
        'css/data-management.css',
        // Javascripts
        'js/app.js',
        'js/confirm-delete.js'
    ]);
});
