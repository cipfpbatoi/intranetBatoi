let mix = require('laravel-mix');
let packages = 'node_modules/';
let gentelella = packages+'gentelella/';
let vendors = gentelella+'vendors/';
let plugins = 'plugins/';

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
// NO va, de https://laracasts.com/discuss/channels/elixir/referenceerror-is-not-defined-using-mix
/*mix.autoload({
   jquery: ['$', 'window.jQuery']
});
*/

mix.copy(vendors+'font-awesome/fonts/','public/fonts/');
mix.copy(vendors+'bootstrap/fonts/','public/fonts/');
mix.copy(gentelella+'build/js/custom.js','resources/assets/js/');

mix.combine([
    vendors+'bootstrap/dist/css/bootstrap.css',
    vendors+'font-awesome/css/font-awesome.css',
//    vendors+'pnotify/dist/pnotify.css',
//    vendors+'select2/dist/css/select2.css',
    vendors+'nprogress/nprogress.css',
    vendors+'bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min',
    
    

// de Natxo
    vendors+'bootstrap-daterangepicker/daterangepicker.css',
    plugins+'datetimepicker/css/bootstrap-datetimepicker.min.css',
    vendors+'datatables.net-bs/css/dataTables.bootstrap.min.css',
    vendors+'datatables.net-responsive-bs/css/responsive.bootstrap.min.css',
    
    //Now lets add the custom css
    vendors+'nprogress/nprogress.css',
    gentelella+'build/css/custom.css',//this is the default from gentelella
    'resources/assets/css/mycustom.css' //All your custom css can go here

],'public/css/app.css');


mix.combine([
    vendors+'/jquery/dist/jquery.js',
    vendors+'bootstrap/dist/js/bootstrap.min.js',
//    vendors+'pnotify/dist/pnotify.js',
    vendors+'bootstrap-progressbar/bootstrap-progressbar.min.js',
    vendors+'fastclick/lib/fastclick.js',
//    vendors+'select2/dist/js/select2.js',
    vendors+'nprogress/nprogress.js',
    vendors+'jquery.tagsinput/src/jquery.tagsinput.js',
//    vendors+'icheck/icheck.js',
// Cosas de Natxo
    packages+'moment/moment.js',
    vendors+'datatables.net/js/jquery.dataTables.min.js',
    vendors+'bootstrap-daterangepicker/daterangepicker.js',
    plugins+'datetimepicker/js/bootstrap-datetimepicker.min.js',

// Actas
    vendors+'bootstrap-wysiwyg/js/bootstrap-wysiwyg.min.js',
    vendors+'jquery.hotkeys/jquery.hotkeys.js',
    vendors+'google-code-prettify/src/prettify.js',
    vendors+'autosize/dist/autosize.min.js',

    
    plugins+'datetime-moment/dist/datetime-moment.js', 
    vendors+'datatables.net-responsive-bs/js/dataTables.bootstrap.min.js',
    vendors+'datatables.net-responsive/js/dataTables.responsive.min.js',
    vendors+'datatables.net-responsive-bs/js/responsive.bootstrap.js',
    
    vendors+'datatables.net-buttons/js/dataTables.buttons.min.js',
    vendors+'datatables.net-buttons-bs/js/buttons.bootstrap.min.js',
    vendors+'datatables.net-buttons/js/buttons.print.min.js',
    vendors+'morris.js/morris.min.js',
    vendors+'raphael/raphael.min.js',
    vendors+'nprogress/nprogress.js',

    //lets add custom scripts
    'resources/assets/js/custom.js',//this default from gentelella
    'resources/assets/js/init.js' //initialize other plugins here
],'public/js/app.js');

mix.js('resources/assets/js/app.js', 'public/js/components')
   .sass('resources/assets/sass/app.scss', 'public/css/components');
