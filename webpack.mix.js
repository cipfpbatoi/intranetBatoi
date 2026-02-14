const mix = require('laravel-mix');
const path = require('path');
const fs = require('fs');

// 🔧 Constants
const npm = 'node_modules/';
const gentelella = npm + 'gentelella/';
const vendors = path.join(gentelella, 'vendors/');
const plugins = 'plugins/'; // assegura't que apunta bé (ex: 'resources/assets/plugins/')

// 📁 Fonts
mix.copy(`${vendors}font-awesome/fonts/`, 'public/fonts/');
mix.copy(`${vendors}bootstrap/fonts/`, 'public/fonts/');



// 🎨 CSS Legacy (Gentelella + plugins)
mix.styles([
    `${vendors}bootstrap/dist/css/bootstrap.min.css`,
    `${vendors}font-awesome/css/font-awesome.min.css`,
    `${vendors}bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css`,
    `${vendors}bootstrap-daterangepicker/daterangepicker.css`,
    `${plugins}datetimepicker/css/bootstrap-datetimepicker.min.css`,
    `${vendors}datatables.net-bs/css/dataTables.bootstrap.min.css`,
    `${vendors}datatables.net-responsive-bs/css/responsive.bootstrap.min.css`,
    `${vendors}ion.rangeSlider/css/normalize.css`,
    `${vendors}ion.rangeSlider/css/ion.rangeSlider.css`,
    `${vendors}ion.rangeSlider/css/ion.rangeSlider.skinFlat.css`,
    `${vendors}nprogress/nprogress.css`,
    `${vendors}dropzone/dist/min/dropzone.min.css`,
    `${gentelella}build/css/custom.min.css`,
    'resources/assets/css/mycustom.css',
    'resources/assets/css/estilo.css'
], 'public/css/gentelella.css');

// ⚙️ JS Legacy (plugins + inicialitzadors)
mix.scripts([
    `${vendors}jquery/dist/jquery.min.js`,
    `${vendors}bootstrap/dist/js/bootstrap.min.js`,
    `${vendors}bootstrap-progressbar/bootstrap-progressbar.min.js`,
    `${vendors}fastclick/lib/fastclick.js`,
    `${vendors}nprogress/nprogress.js`,
    `${vendors}jQuery-Smart-Wizard/js/jquery.smartWizard.js`,
    `${vendors}jquery.tagsinput/src/jquery.tagsinput.js`,
    `${npm}moment/min/moment.min.js`,
    `${vendors}datatables.net/js/jquery.dataTables.min.js`,
    `${vendors}bootstrap-daterangepicker/daterangepicker.js`,
    `${plugins}datetimepicker/js/bootstrap-datetimepicker.min.js`,
    `${vendors}bootstrap-wysiwyg/js/bootstrap-wysiwyg.min.js`,
    `${vendors}jquery.hotkeys/jquery.hotkeys.js`,
    `${vendors}google-code-prettify/bin/prettify.min.js`,
    `${vendors}autosize/dist/autosize.min.js`,
    `${plugins}datetime-moment/dist/datetime-moment.js`,
    `${vendors}datatables.net-bs/js/dataTables.bootstrap.min.js`,
    `${vendors}datatables.net-responsive/js/dataTables.responsive.min.js`,
    `${vendors}datatables.net-responsive-bs/js/responsive.bootstrap.js`,
    `${vendors}datatables.net-buttons/js/dataTables.buttons.min.js`,
    `${vendors}datatables.net-buttons-bs/js/buttons.bootstrap.min.js`,
    `${vendors}datatables.net-buttons/js/buttons.print.min.js`,
    `${vendors}morris.js/morris.min.js`,
    `${vendors}raphael/raphael.min.js`,
    `${vendors}ion.rangeSlider/js/ion.rangeSlider.min.js`,
    `${vendors}dropzone/dist/min/dropzone.min.js`,
    'resources/assets/js/custom.js',
    'resources/assets/js/init.js'
], 'public/js/gentelella.js');

mix.js('resources/assets/js/ppIntranet.js', 'public/js/ppIntranet.js');



// 🌿 Vue 2 + SCSS components moderns
mix.js('resources/assets/js/app.js', 'public/js/components/app.js')
    .vue({ version: 2 })
    .sass('resources/assets/sass/app.scss', 'public/css/components/app.css');

// ✅ Versionat només dels fitxers reals generats
if (mix.inProduction()) {
    mix.version([
        'public/js/gentelella.js',
        'public/css/gentelella.css',
        'public/js/components/app.js',
        'public/css/components/app.css'
    ]);
}
