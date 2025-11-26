
import Vue from 'vue'
window.Vue = Vue;

import ControlDiaView from './components/fichar/ControlDiaView.vue';
import ControlSemanaView from './components/fichar/ControlSemanaView.vue';
import ControlGuardiaView from './components/guardias/ControlGuardiaView.vue';
import ReservasView from './components/reservas/ReservasView.vue';
import BirretItacaView from './components/fichar/BirretItacaView.vue';
import ControlResumenRangoView from './components/fichar/ControlResumenRangoView.vue'

Vue.component('control-dia-view', ControlDiaView);
Vue.component('control-semana-view', ControlSemanaView);
Vue.component('control-guardia-view', ControlGuardiaView);
Vue.component('reservas-view', ReservasView);
Vue.component('birret-itaca-view', BirretItacaView);
<<<<<<< HEAD
=======
Vue.component('control-resumen-rango-view', ControlResumenRangoView)
>>>>>>> laravel10

const app = new Vue({
    el: '#app'
});
