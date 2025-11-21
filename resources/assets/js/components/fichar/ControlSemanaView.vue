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
                <td v-for="m in 5" :class="estadoClase(estadoDia(profe.dni, m))">
                  {{ muestraHoras(profe.dni, m) }}
                </td>
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
      msg: '',
    }
  },
  methods: {
    urlHorario(dni) {
      return '/profesor/'+dni+'/horario'
    },
    getFichajes() {
      this.fichajes = {};
      this.msg = 'Esperando al servidor ...';

      const desde = this.sumaFecha(1);
      const hasta = this.sumaFecha(5);

      axios.get('/api/presencia/resumen-rango?desde='+desde+'&hasta='+hasta+'&api_token='+token)
        .then(resp => {
          const map = {};
          resp.data.forEach(p => {
            map[p.dni] = p.days || {};
          });
          this.fichajes = map;
          this.msg = '';
        })
        .catch(error => {
          this.msg = 'ERROR del servidor '+(error.response?.status || '')+' ('+(error.response?.statusText || '')+')';
        });
    },
    setDia(cambio) {
      this.fecha.add(cambio, 'days');
      this.getFichajes();
    },
    sumaFecha(dias) {
      return this.fecha.clone().add(dias, 'days').format('YYYY-MM-DD');
    },
    muestraHoras(profe, masDias) {
      const dia = this.sumaFecha(masDias);
      const datos = this.fichajes[profe]?.[dia];
      if (!datos) return '';

      const minutos = (datos.covered_docencia_minutes || 0) + (datos.covered_altres_minutes || 0);
      return minsToTime(minutos);
    },
    sumaHoras(profe) {
      if (!this.fichajes[profe]) return '';
      let totMinutos = 0;
      for (let dia in this.fichajes[profe]) {
        const datos = this.fichajes[profe][dia];
        totMinutos += (datos.covered_docencia_minutes || 0) + (datos.covered_altres_minutes || 0);
      }
      return minsToTime(totMinutos);
    },
    estadoDia(profe, masDias) {
      const dia = this.sumaFecha(masDias);
      return this.fichajes[profe]?.[dia]?.status || '';
    },
    estadoClase(status) {
      return {
        'bg-success text-white': status === 'OK',
        'bg-warning': status === 'PARTIAL',
        'bg-danger text-white': status === 'ABSENT',
        'bg-info text-white': status === 'NO_SALIDA'
      };
    }
  },
  created() {
    this.fecha=moment();
    this.fecha.locale('es');
    this.fecha.subtract(this.fecha.day(), 'days');

    this.getFichajes();
  }
}

function fillZero(value, digits=2) {
  let filled="0000000000"+value;
  return filled.substr(filled.length - digits);
}

function minsToTime(mins){
  const hours = Math.floor(mins / 60);
  const minutes = mins % 60;
  return fillZero(hours)+':'+fillZero(minutes);
}
</script>
