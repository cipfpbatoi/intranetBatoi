<template>
  <div>
    <h3>{{ fechaEsp }}</h3>
    <control-nav dias="1" @click="setDia"></control-nav>
    <div class="clearfix"></div>
    <p v-if="msg" class="text-danger">{{ msg }}</p>
    <div>
        <table id="tabla-datos" border="1">
            <tr id="profe-title">
              <th>Departament</th><th>Professorat</th><th>Horari</th><th>Fitxatges</th>
            </tr>
            <tr v-for="profe in sortedProfes" :key="profe.dni" :id="profe.dni">
                <th>{{ profe.departamento }}</th>
                <th>{{ profe.apellido1 }} {{ profe.apellido2 }}, {{ profe.nombre }}</th>
                <td>   
                    <span class="fichaje">{{ horarios[profe.dni] }}</span>
                    <a :href="urlHorario(profe.dni)" class="btn-success btn btn-xs iconButton"><i class="fa fa-table"></i></a>
                </td>
                <td><span class="fichaje" v-html="fichajeByDni(profe.dni)"></span></td>
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

const tokenNode = document.getElementById('_token');
const token = tokenNode ? tokenNode.innerHTML : '';

export default {
  components: {
    ControlNav,FechaPicker
  },
  props: {
    profes: {
      type: Array,
      default: () => []
    },
    horarioInicial: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      fichajes: {},
      horarios: {},
      localProfes: [],
      fecha: '',
      fechaEsp: '',
      msg: '',
    }
  },
  computed: {
    sortedProfes() {
      return [...this.localProfes].sort((a, b) => {
        const aKey = `${a.apellido1 || ''} ${a.apellido2 || ''} ${a.nombre || ''}`.toLowerCase();
        const bKey = `${b.apellido1 || ''} ${b.apellido2 || ''} ${b.nombre || ''}`.toLowerCase();
        return aKey.localeCompare(bKey, 'ca');
      });
    }
  },
  methods: {
    normalizeProfes(input) {
      let source = [];
      if (Array.isArray(input)) {
        source = input;
      } else if (input && typeof input === 'object') {
        source = Object.values(input);
      }

      return source.map(item => ({
        dni: this.normalizeDni(item.dni),
        nombre: item.nombre || '',
        apellido1: item.apellido1 || '',
        apellido2: item.apellido2 || '',
        departamento: item.departamento_label || '',
      })).filter(item => item.dni !== '');
    },
    normalizeDni(value) {
      return String(value || '').trim().toUpperCase();
    },
    fichajeByDni(dni) {
      return this.fichajes[this.normalizeDni(dni)] || '';
    },
    urlHorario(dni) {
      return '/profesor/'+dni+'/horario'
    },
    loadProfesFallback() {
      if (this.localProfes.length > 0 || !token) {
        return;
      }

      axios.get('/api/profesor?api_token=' + token)
        .then(resp => {
          const data = (resp && resp.data && resp.data.data) ? resp.data.data : [];
          // Fallback només per no deixar la taula buida.
          this.localProfes = data.map(item => ({
            dni: this.normalizeDni(item.dni),
            nombre: item.nombre || '',
            apellido1: item.apellido1 || '',
            apellido2: item.apellido2 || '',
            departamento: '',
          }));
        })
        .catch(() => {});
    },
    getFichajes() {
      this.fichajes={};
      this.msg='Esperant resposta del servidor...';
      const queryToken = token ? ('?api_token=' + token) : '';
      axios.get('/api/faltaProfesor/dia=' + this.fecha.format('YYYY-MM-DD') + queryToken)
        .then(resp=>{
          this.msg='Dades rebudes';
          const fichajesMap = {};
          for (var i in resp.data.data) {
            let ficha = resp.data.data[i];
            const key = this.normalizeDni(ficha.idProfesor);
            if (fichajesMap[key]==undefined)
              fichajesMap[key]=ficha.entrada+'->'+ficha.salida;
            else
              fichajesMap[key]+='<br>'+ficha.entrada+'->'+ficha.salida;
          }
          this.fichajes = fichajesMap;
          this.msg='';
        })
        .catch(error => {
          this.msg = "No s'han pogut carregar els fitxatges";
          console.error(error);
        });
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
    this.fecha.locale('ca');
    this.fechaEsp=this.fecha.format('dddd, DD-MMM-YYYY');
    this.localProfes = this.normalizeProfes(this.profes);
    this.horarios=this.horarioInicial;
    this.loadProfesFallback();
    this.getFichajes();
  }
}
</script>
