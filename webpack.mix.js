let mix = require('laravel-mix');                           // If you are new to this then please visit https://laravel.com/docs/5.5/mix
const webpack = require('webpack');

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

var plugin =  'resources/plugins/';

mix.js('resources/js/app.js', 'public/js/app.js')
  .extract(['vue', 'jquery']);
mix.sass('resources/sass/app.scss', 'public/css');

mix.combine([
    plugin + 'moment/moment.min.js',
    plugin + 'toastr/toastr.min.js',

  ],'public/js/custom.js')

  .combine([
    plugin + 'jquery/jquery.min.js',
    plugin + 'moment/moment.min.js',
    plugin + 'toastr/toastr.min.js',
  ],'public/js/bundle.js')
    .webpackConfig({
        devtool: "cheap-module-source-map",
        output: {
            chunkFilename: '[name].js?id=[chunkhash]',
        },
    });

  
if (mix.inProduction()) {                       // In production environtment use versioning
    mix.version();
}

