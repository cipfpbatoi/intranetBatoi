
/**
 * First we will load all of this project's JavaScript dependencies which
 * js Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

// Lo comento para no cargarlo todo
//require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Example.vue'));
Vue.component('control-dia-view', require('./components/fichar/ControlDiaView.vue'));
Vue.component('control-semana-view', require('./components/fichar/ControlSemanaView.vue'));
Vue.component('control-guardia-view', require('./components/guardias/ControlGuardiaView.vue'));
Vue.component('reservas-view', require('./components/reservas/ReservasView.vue'));
Vue.component('birret-itaca-view', require('./components/fichar/BirretItacaView.vue'));
//Vue.component('horas-table', require('./components/HorasTable.vue'));

const app = new Vue({
    el: '#app'
});
