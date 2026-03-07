const mix = require('laravel-mix');
const path = require('path');

// 🔧 Constants
const npm = 'node_modules/';
const gentelella = path.join(npm, 'gentelella');
const gentelellaVendors = path.join(gentelella, 'vendors');
const plugins = 'plugins/'; // ex: 'resources/assets/plugins/'

// 🎨 Bloc legacy (Bootstrap + Gentelella)
const legacyCss = [
    `${gentelellaVendors}/bootstrap/dist/css/bootstrap.min.css`,
    `${gentelellaVendors}/font-awesome/css/font-awesome.min.css`,
    `${gentelellaVendors}/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css`,
    `${gentelellaVendors}/bootstrap-daterangepicker/daterangepicker.css`,
    `${plugins}datetimepicker/css/bootstrap-datetimepicker.min.css`,
    `${gentelellaVendors}/datatables.net-bs/css/dataTables.bootstrap.min.css`,
    `${gentelellaVendors}/datatables.net-responsive-bs/css/responsive.bootstrap.min.css`,
    `${gentelellaVendors}/ion.rangeSlider/css/normalize.css`,
    `${gentelellaVendors}/ion.rangeSlider/css/ion.rangeSlider.css`,
    `${gentelellaVendors}/ion.rangeSlider/css/ion.rangeSlider.skinFlat.css`,
    `${gentelellaVendors}/nprogress/nprogress.css`,
    `${gentelellaVendors}/dropzone/dist/min/dropzone.min.css`,
    `${gentelella}/build/css/custom.min.css`,
    'resources/assets/css/mycustom.css',
    'resources/assets/css/estilo.css'
];

const legacyJs = [
    `${gentelellaVendors}/jquery/dist/jquery.min.js`,
    `${gentelellaVendors}/bootstrap/dist/js/bootstrap.min.js`,
    `${gentelellaVendors}/bootstrap-progressbar/bootstrap-progressbar.min.js`,
    `${gentelellaVendors}/fastclick/lib/fastclick.js`,
    `${gentelellaVendors}/nprogress/nprogress.js`,
    `${gentelellaVendors}/jQuery-Smart-Wizard/js/jquery.smartWizard.js`,
    `${gentelellaVendors}/jquery.tagsinput/src/jquery.tagsinput.js`,
    `${npm}moment/min/moment.min.js`,
    `${gentelellaVendors}/datatables.net/js/jquery.dataTables.min.js`,
    `${gentelellaVendors}/bootstrap-daterangepicker/daterangepicker.js`,
    `${plugins}datetimepicker/js/bootstrap-datetimepicker.min.js`,
    `${gentelellaVendors}/bootstrap-wysiwyg/js/bootstrap-wysiwyg.min.js`,
    `${gentelellaVendors}/jquery.hotkeys/jquery.hotkeys.js`,
    `${gentelellaVendors}/google-code-prettify/bin/prettify.min.js`,
    `${gentelellaVendors}/autosize/dist/autosize.min.js`,
    `${plugins}datetime-moment/dist/datetime-moment.js`,
    `${gentelellaVendors}/datatables.net-bs/js/dataTables.bootstrap.min.js`,
    `${gentelellaVendors}/datatables.net-responsive/js/dataTables.responsive.min.js`,
    `${gentelellaVendors}/datatables.net-responsive-bs/js/responsive.bootstrap.js`,
    `${gentelellaVendors}/datatables.net-buttons/js/dataTables.buttons.min.js`,
    `${gentelellaVendors}/datatables.net-buttons-bs/js/buttons.bootstrap.min.js`,
    `${gentelellaVendors}/datatables.net-buttons/js/buttons.print.min.js`,
    `${gentelellaVendors}/morris.js/morris.min.js`,
    `${gentelellaVendors}/raphael/raphael.min.js`,
    `${gentelellaVendors}/ion.rangeSlider/js/ion.rangeSlider.min.js`,
    `${gentelellaVendors}/dropzone/dist/min/dropzone.min.js`,
    'resources/assets/js/custom.js'
];

// 📁 Fonts
mix.copy(`${gentelellaVendors}/font-awesome/fonts/`, 'public/fonts/');
mix.copy(`${gentelellaVendors}/bootstrap/fonts/`, 'public/fonts/');

// 🎨 CSS Legacy (Gentelella + plugins)
mix.styles(legacyCss, 'public/css/gentelella.css');

// ⚙️ JS Legacy (plugins + inicialitzadors)
mix.scripts(legacyJs, 'public/js/gentelella.js');

// JS auxiliar legacy reutilitzable
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
