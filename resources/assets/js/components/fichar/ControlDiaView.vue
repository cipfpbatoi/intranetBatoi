<template>
  <div>
    <h3>{{ fechaEsp }}</h3>
    <control-nav dias="1" @click="setDia"></control-nav>
    <div class="clearfix"></div>
    <p v-if="msg" class="text-danger">{{ msg }}</p>
    <div>
        <table id="tabla-datos" border="1">
            <tr id="profe-title">
              <th>Dep</th><th>Profesor</th><th>Horario</th><th>Fichajes</th>
            </tr>
            <tr v-for="profe in profes" :key="profe.dni" :id="profe.dni">
                <th>{{ profe.departamento }}</th>
                <th>{{ profe.apellido1 }} {{ profe.apellido2 }}, {{ profe.nombre }}</th>
                <td>   
                    <span class="fichaje">{{ horarios[profe.dni] }}</span>
                    <a :href="urlHorario(profe.dni)" class="btn-success btn btn-xs iconButton"><i class="fa fa-table"></i></a>
                </td>
                <td><span class="fichaje" v-html="fichajes[profe.dni]"></span></td>
            </tr>
        </table>
    </div>
    <control-nav dias="1" @click="setDia"></control-nav>
  </div>  
</template>

<script>
import axios from 'axios'
import ControlNav from '../utils/ControlNav.vue';
import FechaPicker from "../utils/FechaPicker";

const token=document.getElementById('_token').innerHTML;

export default {
  components: {
    ControlNav,FechaPicker
  },
  props: ['profes', 'horarioInicial'],
  data() {
    return {
      fichajes: {},
      horarios: {},
      fecha: '',
      fechaEsp: '',
      msg: '',
    }
  },
  methods: {
    urlHorario(dni) {
      return '/profesor/'+dni+'/horario'
    },
    getFichajes() {
      this.fichajes={};
      this.msg='Esperando al servidor ...';
      axios.get('/api/faltaProfesor/dia='+this.fecha.format('YYYY-MM-DD')+'?api_token='+token)
        .then(resp=>{
          this.msg='Datos recibidos';
          for (var i in resp.data.data) {
            let ficha = resp.data.data[i];
            if (this.fichajes[ficha.idProfesor]==undefined)
              this.fichajes[ficha.idProfesor]=ficha.entrada+'->'+ficha.salida;
            else
              this.fichajes[ficha.idProfesor]+='<br>'+ficha.entrada+'->'+ficha.salida;
          }
          this.msg='';
        })
        .catch(resp=>console.error(resp));
    },
    getHorario() {
      this.horarios={};
      axios.get('/api/horariosDia/'+this.fecha.format('YYYY-MM-DD')+'?api_token='+token)
        .then(resp=>this.horarios=resp.data.data)
        .catch(resp=>console.error(resp));
    },
    setDia(cambio) {
      // Si es viernes el siguiente que sea el lunes
      if (cambio == 1 && this.fecha.day() == 5)
        cambio = 3;
      // Si es lunes el anterior que sea el viernes
      else if (cambio == -1 && this.fecha.day() == 1)
        cambio = -3;
      this.fecha.add(cambio, 'days');
      this.fechaEsp=this.fecha.format('dddd, DD-MMM-YYYY');
      this.getHorario();
      this.getFichajes();
    },
  },
  mounted() {
    this.fecha = moment();  
    this.fecha.locale('es');
    this.fechaEsp=this.fecha.format('dddd, DD-MMM-YYYY');

    this.horarios=this.horarioInicial;
    this.getFichajes();
  }
}
</script>
