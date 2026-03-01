<template>
  <div>
    <br><h4 class="centrado">RESERVAR RECURSOS</h4><br>

    <recursos-select v-model="espacio" :espacios="espacios" @change="getReservas"></recursos-select>

    <div id="gestion" v-if="espacio!=0">
      <br><label for="dia"> Día: </label>
      <fecha-picker v-model="fechaIni" :fechaIni="fechaAct(-1)" :fechaFin="fechaAct()"></fecha-picker>

      <input v-model="fechaIni" class="noFlotar date" @change="getReservas" autofocus />

      <horas-table :horas="horas" :reservas="reservas"></horas-table>

        <horas-select v-model="horaIni" :horas="horas" title="Desde hora" ini-val="0"></horas-select>
        <horas-select v-model="horaFin" :horas="horas" title="Hasta hora" :ini-val="horaIni"></horas-select>
        <profes-select v-model="idProfesor" :horas="profes" title="Professor" ></profes-select>

      <br><label for="dia"> Observaciones: </label>
        <input v-model="observaciones" type="text" class="noFlotar" />
        <div v-if="esDirecc">
          <br><label for="dia_fin"> Todos los <span id="nom_dia_fin"></span> hasta el día: </label>
          <fecha-picker v-model="fechaFin" :fechaIni="FechaIni" :fechaFin="fechaAct(365)"></fecha-picker>

          <input v-model="fechaFin" class="noFlotar date" />         
        </div>
        <div id="botones">
          <input id="reservar" class="btn btn-danger" type="button" value="Reservar">
          <input id="liberar" class="btn btn-success" type="button" value="Liberar">
        </div>
        <div class="errores"></div>
      </div>

    </div>
</template>

<script>
import axios from 'axios'
import { withApiAuth } from '../utils/api-auth';
const ruta='/api/reserva';

const rolDirecc=2;
const rolNode = document.getElementById('rol');
const esDirecc=((Number(rolNode ? rolNode.innerHTML : 0)%rolDirecc)==0);
const dniNode = document.getElementById('dni');
const idProfesor = dniNode ? dniNode.innerHTML : '';

const maxDiasReserva=30;

    export default {
        components: {
          'profes-select': require('./ProfesSelect.vue'),
          'recursos-select': require('./RecursosSelect.vue'),
          'horas-select': require('./HorasSelect.vue'),
          'horas-table': require('./HorasTable.vue'),
          'fecha-picker': require('../utils/FechaPicker.vue'),
        },
        props: ['horas', 'espacios','profes'],
        data() {
          return {
            reservas: [],
            fechaIni: '',
            fechaFin: '',
            horaIni: '0',
            horaFin: '0',
            espacio: '0',
          }
        },
        computed: {
          esDirecc() {
            return (document.getElementById('rol').innerHTML%rolDirecc)==0;
          },
        },
        methods: {
          async getReservas() {
            console.log('pido '+(this.fechaIni=='' || this.espacio=='0'));
            if (this.fechaIni=='' || this.espacio=='0') {
              this.reservas=[];
              return;
            }
            const params = {
              idEspacio: this.espacio,
              dia: this.fechaIni,
            };

            try {
              const resp = await axios.get(ruta, withApiAuth({ params }));
              this.reservas = resp.data.data;
            } catch (errorModern) {
              console.error(errorModern);
              this.reservas = [];
            }
          },
          idTd(codigo) {
            return 'hora-'+codigo;
          },
          estaReservado(hora) {
            return this.reservas[hora]!=undefined;
          },
          fechaAct(dias=maxDiasReserva) {
            let fecha=new Date();
            fecha.setDate(fecha.getDate()+dias);
            return fecha.toISOString().substr(0,10);
          }
        },
    }
</script>
