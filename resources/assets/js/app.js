
import Vue from 'vue'
window.Vue = Vue;

import ControlGuardiaView from './components/guardias/ControlGuardiaView.vue';
import ReservasView from './components/reservas/ReservasView.vue';

Vue.component('control-guardia-view', ControlGuardiaView);
Vue.component('reservas-view', ReservasView);

const app = new Vue({
    el: '#app'
});
