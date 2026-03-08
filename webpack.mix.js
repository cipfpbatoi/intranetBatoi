const mix = require('laravel-mix');

// JS auxiliar legacy reutilitzable
mix.js('resources/assets/js/ppIntranet.js', 'public/js/ppIntranet.js');

// Vue 2 + SCSS components moderns
mix.js('resources/assets/js/app.js', 'public/js/components/app.js')
    .vue({ version: 2 })
    .sass('resources/assets/sass/app.scss', 'public/css/components/app.css');

// Versionat només dels fitxers reals generats
if (mix.inProduction()) {
    mix.version([
        'public/js/components/app.js',
        'public/css/components/app.css'
    ]);
}
