
/**
 * First we will load all of this project's JavaScript dependencies which
 * js Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

// Lo comento para no cargarlo todo
//require('./bootstrap');

//window.Vue = require('vue');
import Vue from 'vue'
window.Vue = Vue;
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import ControlDiaView from './components/fichar/ControlDiaView.vue';
import ControlSemanaView from './components/fichar/ControlSemanaView.vue';
import ControlGuardiaView from './components/guardias/ControlGuardiaView.vue';
import ReservasView from './components/reservas/ReservasView.vue';
import BirretItacaView from './components/fichar/BirretItacaView.vue';
import ControlResumenDiaView from './components/fichar/ControlResumenDiaView.vue';

Vue.component('control-dia-view', ControlDiaView);
Vue.component('control-semana-view', ControlSemanaView);
Vue.component('control-guardia-view', ControlGuardiaView);
Vue.component('reservas-view', ReservasView);
Vue.component('birret-itaca-view', BirretItacaView);
Vue.component('control-resumen-dia-view', ControlResumenDiaView);

const app = new Vue({
    el: '#app'
});
