<template>
  <div>
    <control-nav dias="7" @click="setDia"></control-nav>
    <div class="clearfix"></div>
    <p v-if="msg" class="text-danger">{{ msg }}</p>
    <div>
        <table id="table-guardias" border="1">
            <tr id="hora-title"><th>Hora</th><th v-for="n in 5">{{ sumaFecha(n) }}</th></tr>
            <tr v-for="hora in horas" :key="hora.codigo">
                <th>{{ hora.turno }} {{ hora.hora_ini }}-{{ hora.hora_fin }}</th>
                <td v-for="dia in dias">
                  <template v-if="profesGuardia[hora.codigo] && profesGuardia[hora.codigo][dia]">
                    <div v-for="profesor in profesGuardia[hora.codigo][dia]">
                      <control-guardia-item :ficha="fichajes[hora.codigo][dia][profesor.dni]">
                        {{ profesor.nombre }}
                      </control-guardia-item>
                    </div>
                  </template>
                </td>
            </tr>
        </table>
    </div>
    <control-nav dias="7" @click="setDia"></control-nav>
  </div>
</template>

<script>
import axios from 'axios'
import ControlGuardiaItem from './ControlGuardiaItem.vue';
import ControlNav from '../utils/ControlNav.vue';

const tokenNode = document.getElementById('_token');
const token = tokenNode ? tokenNode.innerHTML : '';

export default {
  components: {
    ControlGuardiaItem,ControlNav
  },
  props: ['horas', 'profesGuardia', 'dias'],
  data() {
    return {
      fichajes: {},
      fecha: '',
      msg: ''
    }
  },  
  methods: {
    borraFichajes() {
      for (let i in this.horas) {
        let hora = this.horas[i].codigo;
        this.fichajes[hora]={};
        for (let j in this.dias) {
          let dia = this.dias[j]
          this.fichajes[hora][dia]={};
        }
      }
    },
    getGuardias() {
      this.borraFichajes();
      this.msg='Esperando al servidor ...';
      const params = {
        desde: this.sumaFecha(1),
        hasta: this.sumaFecha(5),
      };
      if (token) {
        params.api_token = token;
      }
      axios.get('/api/guardia/range', {
        params
      })
      .then(resp=>{
        // Organizamos los datos en un objeto de horas->días->profes
        for (let i in resp.data.data) {
          let guardia=resp.data.data[i];
          // Para obtener el día restamos el de la guardia-1 a la fecha actual
          // y buscamos en el array de días la entrada de ese índice
          let dia=this.dias[0-this.fecha.diff(guardia.dia, 'days')];
          this.fichajes[guardia.hora][dia][guardia.idProfesor]={
            realizada: guardia.realizada,
            obs_personal: guardia.obs_personal,
            observaciones: guardia.observaciones
          }
        }
        this.msg='';
      })
      .catch(resp=>this.msg='ERROR del servidor '+resp.status+'('+resp.statusText+')');
    },
    sumaFecha(dias) {
      return this.fecha.clone().add(dias, 'days').format('YYYY-MM-DD');
    },
    setDia(cambio) {
      this.fecha.add(cambio, 'days');
      this.getGuardias();
    },
  },
  created() {
    this.fecha=moment();
    this.fecha.locale('es');
    this.fecha.subtract(this.fecha.day(), 'days');

    this.getGuardias();
  }
}
</script>
