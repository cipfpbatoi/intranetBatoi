<template>
  <div>
    <control-nav dias="28" @click="setDia"></control-nav>
    <control-nav dias="7" @click="setDia"></control-nav>
    <div class="clearfix"></div>
    <p v-if="loading" class="text-muted">Carregant dades...</p>
    <p v-if="msg" class="text-danger">{{ msg }}</p>
    <div>
        <table id="tabla-datos" border="1">
            <tr id="profe-title">
                <th>Dep</th><th>Profesor</th><th v-for="n in 5" :key="`header-${n}`">{{ sumaFecha(n) }}</th><th>Total</th>
            </tr>
            <tr v-for="profe in profesAmbHores" :key="profe.dni">
                <th>{{ departamentoLabel(profe) }}</th>
                <th><a :href="urlHorario(profe.dni)">{{ profe.apellido1}} {{ profe.apellido2 }}, {{ profe.nombre }}</a></th>
                <td v-for="m in 5" :key="`${profe.dni}-${m}`" :class="estadoClase(estadoDia(profe.dni, m))">
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

<script setup>
import axios from 'axios'
import moment from 'moment'
import { computed, onMounted, ref } from 'vue'
import ControlNav from '../utils/ControlNav.vue';
import { withApiAuth } from '../utils/api-auth';

const props = defineProps({
  profes: {
    type: [Array, Object],
    default: () => []
  }
})

const fichajes = ref({})
const estados = ref({})
const fecha = ref(moment())
const msg = ref('')
const loading = ref(false)

const profesList = computed(() => {
  if (Array.isArray(props.profes)) {
    return props.profes
  }

  if (typeof props.profes === 'string' && props.profes.trim() !== '') {
    try {
      const parsed = JSON.parse(props.profes)
      if (Array.isArray(parsed)) {
        return parsed
      }

      if (parsed && typeof parsed === 'object') {
        return Object.values(parsed)
      }
    } catch (error) {
      return []
    }
  }

  if (props.profes && typeof props.profes === 'object') {
    return Object.values(props.profes)
  }

  return []
})

const profesAmbHores = computed(() => profesList.value)

function urlHorario(dni) {
  return '/profesor/' + dni + '/horario'
}

function departamentoLabel(profe) {
  return profe.depcurt || ''
}

function extractHorasPayload(response) {
  if (response?.data?.success && response?.data?.data && typeof response.data.data === 'object') {
    return response.data.data
  }

  if (response?.data && typeof response.data === 'object') {
    return response.data
  }

  return {}
}

function extractResumenPayload(response) {
  if (Array.isArray(response?.data)) {
    return response.data
  }

  if (response?.data?.success && Array.isArray(response.data.data)) {
    return response.data.data
  }

  return []
}

function sumaFecha(dias) {
  return fecha.value.clone().add(dias, 'days').format('YYYY-MM-DD')
}

async function getFichajes() {
  fichajes.value = {}
  estados.value = {}
  msg.value = 'Esperando al servidor ...'
  loading.value = true

  const desde = sumaFecha(1)
  const hasta = sumaFecha(5)
  const rutaHoras = `/api/faltaProfesor/horas/dia]${desde}&dia[${hasta}`

  try {
    const [respHoras, respEstados] = await Promise.all([
      axios.get(rutaHoras, withApiAuth()),
      axios.get('/api/presencia/resumen-rango', withApiAuth({
        params: { desde, hasta }
      })),
    ])

    fichajes.value = extractHorasPayload(respHoras)

    const estadosRows = extractResumenPayload(respEstados)
    estados.value = estadosRows.reduce((map, profesor) => {
      map[profesor.dni] = profesor.days || {}
      return map
    }, {})
    msg.value = ''
  } catch (error) {
    msg.value = 'ERROR del servidor ' + (error.response?.status || '') + ' (' + (error.response?.statusText || '') + ')'
  } finally {
    loading.value = false
  }
}

function setDia(cambio) {
  fecha.value.add(cambio, 'days')
  getFichajes()
}

function muestraHoras(profe, masDias) {
  const dia = sumaFecha(masDias)
  if (fichajes.value[profe] === undefined || fichajes.value[profe][dia] === undefined) {
    return ''
  }

  return formatHoraCurta(fichajes.value[profe][dia].horas)
}

function totalSegons(profe) {
  if (!fichajes.value[profe]) return 0

  const dies = Object.values(fichajes.value[profe])
  let totSecs = 0
  dies.forEach((dia) => {
    totSecs += timeToSecs(dia.horas)
  })

  return totSecs
}

function sumaHoras(profe) {
  const totSecs = totalSegons(profe)
  return totSecs > 0 ? secsToHourMinute(totSecs) : ''
}

function estadoDia(profe, masDias) {
  const dia = sumaFecha(masDias)
  return estados.value[profe]?.[dia]?.status || ''
}

function estadoSemana(profe) {
  const prioridad = {
    'ABSENT': 3,
    'PARTIAL': 2,
    'NO_SALIDA': 1,
    'OK': 0
  }
  let peor = ''
  let puntuacion = -1

  for (let dia = 1; dia <= 5; dia++) {
    const estado = estadoDia(profe, dia)
    const valor = prioridad[estado] ?? -1
    if (valor > puntuacion) {
      peor = estado
      puntuacion = valor
    }
  }

  return peor
}

function estadoClase(status) {
  return {
    'bg-success text-white': status === 'OK',
    'bg-warning': status === 'PARTIAL',
    'bg-danger text-white': status === 'ABSENT',
    'bg-info text-white': status === 'NO_SALIDA'
  }
}

onMounted(() => {
  fecha.value.locale('es')
  fecha.value.subtract(fecha.value.day(), 'days')
  getFichajes()
})

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
