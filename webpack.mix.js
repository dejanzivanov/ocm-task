const mix = require('laravel-mix');

mix
  .js('resources/js/app.js', 'public/js')
  .vue()                        // ← this enables the Vue 2 loader
  .sass('resources/sass/app.scss', 'public/css');
