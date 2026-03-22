
import Vue from 'vue'
window.Vue = Vue;

import ControlSemanaView from './components/fichar/ControlSemanaView.vue';
import ControlGuardiaView from './components/guardias/ControlGuardiaView.vue';
import ReservasView from './components/reservas/ReservasView.vue';
import ControlResumenRangoView from './components/fichar/ControlResumenRangoView.vue'

Vue.component('control-semana-view', ControlSemanaView);
Vue.component('control-guardia-view', ControlGuardiaView);
Vue.component('reservas-view', ReservasView);
Vue.component('control-resumen-rango-view', ControlResumenRangoView);

const app = new Vue({
    el: '#app'
});
