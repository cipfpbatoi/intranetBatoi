<template>
  <div>
    <control-nav dias="7" @click="setDia"></control-nav>
    <div class="clearfix"></div>
    <p v-if="msg" class="text-danger">{{ msg }}</p>
    <div>
        <table id="tabla-datos" border="1">
            <tr id="profe-title">
                <th>Dep</th><th>Profesor</th><th v-for="n in 5">{{ sumaFecha(n) }}</th><th>Total</th>
            </tr>
            <tr v-for="profe in profes" :key="profe.dni">
                <th>{{ profe.depcurt }}</th>
                <th><a :href="urlHorario(profe.dni)">{{ profe.apellido1}} {{ profe.apellido2 }}, {{ profe.nombre }}</a></th>
                <td v-for="m in 5">{{ muestraHoras(profe.dni, m) }}</td>
                <th>{{ sumaHoras(profe.dni) }}</th>
            </tr>
        </table>
    </div>
    <control-nav dias="7" @click="setDia"></control-nav>
  </div>
</template>

<script>
import axios from 'axios'
import ControlNav from '../utils/ControlNav.vue';

const token=document.getElementById('_token').innerHTML;

export default {
  components: {
    ControlNav
  },
  props: ['profes'],
  data() {
    return {
      fichajes: {},
      fecha: '',
    }
  },
  methods: {
    urlHorario(dni) {
      return '/profesor/'+dni+'/horario'
    },
    getFichajes() {
      this.fichajes={};
      this.msg='Esperando al servidor ...';
      axios.get('/api/faltaProfesor/horas/dia]'+this.sumaFecha(1)+'&dia['+this.sumaFecha(5)+'?api_token='+token)
      .then(resp=>{
        this.fichajes=resp.data.data;
        this.msg='';
      })
      .catch(resp=>this.msg='ERROR del servidor '+resp.status+'('+resp.statusText+')');
    },
    setDia(cambio) {
      this.fecha.add(cambio, 'days');
      this.getFichajes();
    },
    sumaFecha(dias) {
      return this.fecha.clone().add(dias, 'days').format('YYYY-MM-DD');
    },
    muestraHoras(profe, masDias) {
      if (this.fichajes[profe] == undefined || this.fichajes[profe][this.sumaFecha(masDias)] == undefined)
        return '';
      return this.fichajes[profe][this.sumaFecha(masDias)].horas;
    },
    sumaHoras(profe) {
      let totHoras=0;
      for (let ficha in this.fichajes[profe]) {
        totHoras+= timeToSecs(this.fichajes[profe][ficha].horas);
      }
      return secsToTime(totHoras);
    }
  },
  created() {
    this.fecha=moment();
    this.fecha.locale('es');
    this.fecha.subtract(this.fecha.day(), 'days');

    this.getFichajes();
  }
}

function secsToTime(secs) {
  let hours=parseInt(secs/(60*60));
  secs-=hours*60*60;
  let minutes=parseInt(secs/60);
  secs-=minutes*60;
  return fillZero(hours)+':'+fillZero(minutes)+':'+fillZero(secs);
}

function timeToSecs(time) {
  let separatedTime=time.split(':');
  return separatedTime[0]*60*60+separatedTime[1]*60+Number(separatedTime[2]);
}

function fillZero(value, digits=2) {
  let filled="0000000000"+value;
  return filled.substr(filled.length - digits);
}
</script>
