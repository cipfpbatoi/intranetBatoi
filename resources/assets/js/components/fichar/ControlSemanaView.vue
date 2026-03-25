<template>
  <div>
    <control-nav dias="28" @click="setDia"></control-nav>
    <control-nav dias="7" @click="setDia"></control-nav>
    <div class="clearfix"></div>
    <p v-if="msg" class="text-danger">{{ msg }}</p>
    <div>
        <table id="tabla-datos" border="1">
            <tr id="profe-title">
                <th>Dep</th><th>Profesor</th><th v-for="n in 5">{{ sumaFecha(n) }}</th><th>Total</th>
            </tr>
            <tr v-for="profe in profesAmbHores" :key="profe.dni">
                <th>{{ departamentoLabel(profe) }}</th>
                <th><a :href="urlHorario(profe.dni)">{{ profe.apellido1}} {{ profe.apellido2 }}, {{ profe.nombre }}</a></th>
                <td v-for="m in 5" :class="estadoClase(estadoDia(profe.dni, m))">
                  {{ muestraHoras(profe.dni, m) }}
                </td>
                <th :class="estadoClase(estadoSemana(profe.dni))">{{ sumaHoras(profe.dni) }}</th>
            </tr>
        </table>
    </div>
    <control-nav dias="7" @click="setDia"></control-nav>
    <control-nav dias="28" @click="setDia"></control-nav>
  </div>
</template>

<script>
import axios from 'axios'
import moment from 'moment'
import ControlNav from '../utils/ControlNav.vue';
import { withApiAuth } from '../utils/api-auth';

export default {
  components: {
    ControlNav
  },
  props: {
    profes: {
      type: [Array, Object],
      default: () => []
    }
  },
  computed: {
    profesList() {
      if (Array.isArray(this.profes)) {
        return this.profes;
      }

      if (typeof this.profes === 'string' && this.profes.trim() !== '') {
        try {
          const parsed = JSON.parse(this.profes);
          if (Array.isArray(parsed)) {
            return parsed;
          }

          if (parsed && typeof parsed === 'object') {
            return Object.values(parsed);
          }
        } catch (error) {
          return [];
        }
      }

      if (this.profes && typeof this.profes === 'object') {
        return Object.values(this.profes);
      }

      return [];
    },
    profesAmbHores() {
      return this.profesList;
    },
  },
  data() {
    return {
      fichajes: {},
      estados: {},
      fecha: '',
      msg: '',
    }
  },
  methods: {
    urlHorario(dni) {
      return '/profesor/'+dni+'/horario'
    },
    departamentoLabel(profe) {
      return profe.depcurt || '';
    },
    getFichajes() {
      this.fichajes = {};
      this.estados = {};
      this.msg = 'Esperando al servidor ...';

      const desde = this.sumaFecha(1);
      const hasta = this.sumaFecha(5);
      const rutaHoras = `/api/faltaProfesor/horas/dia]${desde}&dia[${hasta}`;

      Promise.all([
        axios.get(rutaHoras, withApiAuth()),
        axios.get('/api/presencia/resumen-rango', withApiAuth({
          params: { desde, hasta }
        })),
      ])
        .then(([respHoras, respEstados]) => {
          this.fichajes = this.extractHorasPayload(respHoras);

          const estadosRows = this.extractResumenPayload(respEstados);

          const map = {};
          estadosRows.forEach(p => {
            map[p.dni] = p.days || {};
          });
          this.estados = map;
          this.msg = '';
        })
        .catch(error => {
          this.msg = 'ERROR del servidor '+(error.response?.status || '')+' ('+(error.response?.statusText || '')+')';
        });
    },
    extractHorasPayload(response) {
      if (response?.data?.success && response?.data?.data && typeof response.data.data === 'object') {
        return response.data.data;
      }

      if (response?.data && typeof response.data === 'object') {
        return response.data;
      }

      return {};
    },
    extractResumenPayload(response) {
      if (Array.isArray(response?.data)) {
        return response.data;
      }

      if (response?.data?.success && Array.isArray(response.data.data)) {
        return response.data.data;
      }

      return [];
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
      if (this.fichajes[profe] === undefined || this.fichajes[profe][dia] === undefined) {
        return '';
      }

      return formatHoraCurta(this.fichajes[profe][dia].horas);
    },
    sumaHoras(profe) {
      const totSecs = this.totalSegons(profe);
      return totSecs > 0 ? secsToHourMinute(totSecs) : '';
    },
    estadoDia(profe, masDias) {
      const dia = this.sumaFecha(masDias);
      return this.estados[profe]?.[dia]?.status || '';
    },
    estadoSemana(profe) {
      const prioridad = {
        'ABSENT': 3,
        'PARTIAL': 2,
        'NO_SALIDA': 1,
        'OK': 0
      };
      let peor = '';
      let puntuacion = -1;

      for (let dia = 1; dia <= 5; dia++) {
        const estado = this.estadoDia(profe, dia);
        const valor = prioridad[estado] ?? -1;
        if (valor > puntuacion) {
          peor = estado;
          puntuacion = valor;
        }
      }

      return peor;
    },
    estadoClase(status) {
      return {
        'bg-success text-white': status === 'OK',
        'bg-warning': status === 'PARTIAL',
        'bg-danger text-white': status === 'ABSENT',
        'bg-info text-white': status === 'NO_SALIDA'
      };
    },
    totalSegons(profe) {
      if (!this.fichajes[profe]) return 0;

      let totSecs = 0;
      for (let dia in this.fichajes[profe]) {
        totSecs += timeToSecs(this.fichajes[profe][dia].horas);
      }

      return totSecs;
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

function secsToTime(secs) {
  let hours=parseInt(secs/(60*60));
  secs-=hours*60*60;
  let minutes=parseInt(secs/60);
  secs-=minutes*60;
  return fillZero(hours)+':'+fillZero(minutes)+':'+fillZero(secs);
}

function secsToHourMinute(secs) {
  let hours=parseInt(secs/(60*60));
  secs-=hours*60*60;
  let minutes=parseInt(secs/60);
  return fillZero(hours)+':'+fillZero(minutes);
}

function timeToSecs(time) {
  if (!time) {
    return 0;
  }

  let separatedTime=time.split(':');
  return Number(separatedTime[0] || 0)*60*60
    + Number(separatedTime[1] || 0)*60
    + Number(separatedTime[2] || 0);
}

function formatHoraCurta(time) {
  if (!time) {
    return '';
  }

  const separatedTime = time.split(':');
  return fillZero(separatedTime[0] || 0)+':'+fillZero(separatedTime[1] || 0);
}
</script>
