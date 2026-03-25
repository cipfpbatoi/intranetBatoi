
import Vue from 'vue'
window.Vue = Vue;

import ReservasView from './components/reservas/ReservasView.vue';

Vue.component('reservas-view', ReservasView);

const app = new Vue({
    el: '#app'
});
