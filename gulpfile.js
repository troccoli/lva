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
    mix.browserify('file-browse.js');

    // Versioning
    mix.version(['css/*.css', 'js/*.js']);
});
