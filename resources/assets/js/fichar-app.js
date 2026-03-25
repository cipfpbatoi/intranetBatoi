import Vue from 'vue';

import ControlResumenRangoView from './components/fichar/ControlResumenRangoView.vue';
import ControlSemanaView from './components/fichar/ControlSemanaView.vue';

window.Vue = Vue;

Vue.component('control-semana-view', ControlSemanaView);
Vue.component('control-resumen-rango-view', ControlResumenRangoView);

if (document.getElementById('app')) {
    new Vue({
        el: '#app',
    });
}
