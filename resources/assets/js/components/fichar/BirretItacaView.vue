<template>
  <div>
    <br><label for="dia"> Dia: </label>
    <fecha-picker id="dia" v-model='dia' type="text" name="dia" class="noFlotar date" autofocus></fecha-picker>
    <table class="table" id="horarios">
        <tr>
            <th style="text-align:center">Hores</th><th style="text-align:center">Grup</th>
            <th style="text-align:center">No he marcat birret</th><th style="text-align:center">Estava al centre</th>
            <th style="text-align:center">Justificació</th><th style="text-align:center">Estat</th>
        </tr>
        <tr v-for="(hora,index) in horario">
            <td>{{hora.desde}} - {{hora.hasta}}</td>
            <td>{{hora.idGrupo}}</td>
            <template v-if="hora.estado<2">
                <td><input type="checkbox" v-model="hora.checked"></td>
                <td><span :class="claseIcono(hora.enCentro)"></span></td>
            </template>
            <template v-else>
                <td colspan="2"></td>
            </template>
            <td v-if='index==0' v-bind:rowspan="horario.length"><textarea v-bind:rows="horario.length*2" v-model="hora.justificacion" :readonly="justificada(hora.estado)"></textarea></td>
            <td>{{ estado(hora.estado) }}</td>
        </tr>                  
    </table>
    <div id="botones">
        <button  class="btn btn-success" @click="confirmar">Enviar</button>
    </div>
    <div class="errores">
        <app-msg 
            v-for="(msg,index) in msgs" 
            :key="index" 
            v-bind="msg"
            @close="delMsg(index)">
        </app-msg>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import AppMsg from '../utils/AppMsg.vue';
import FechaPicker from "../utils/FechaPicker";
import { getApiToken, withApiAuth } from '../utils/api-auth';

const token = getApiToken();
const dniNode = document.getElementById('dni');
const idProfesor = dniNode ? dniNode.innerHTML : '';

export default {

    components: {
        FechaPicker,AppMsg
    },
    data() {
        return {
          dia: '',
          horario: [],
          msgs: [],
      }
    },
    computed: {
        longHorario() {
            return horario.length;
        }
    },
    watch: {
        dia: function(val) {
            if (!idProfesor) {
                this.msgs.push({ estate: 'ok', msg: "No s'ha identificat el professor" });
                this.horario = [];
                return;
            }

            axios.get('/api/itaca/' + val + '/' + idProfesor, withApiAuth())
            .then((response) => {
                this.horario = response.data.data;
            }, (error) => {
                this.msgs.push({ estate: 'ok', msg: 'Error: '+error });
                console.log(error);
                this.horario = [];
            });
        }
    },
    methods: {
        justificada(estado) {
            return estado>=2;
        },
        claseIcono(enCentro) {
            return "glyphicon "+(enCentro?"glyphicon-thumbs-up":"glyphicon-thumbs-down");
        },
        estado(queEstado) {
            switch (queEstado) {
                case 0:
                    return 'No comunicada';
                case 1:
                    return 'Pendent';
                case 2: 
                    return 'Justificada';
                case 3:
                    return 'Rebutjada';
                default:
                    return 'Desconegut';
            }
        },
        confirmar: function () {
            if (!token) {
                this.msgs.push({ estate: 'ok', msg: 'No hi ha token API' });
                return;
            }

            let req = {
                url: '/api/itaca',
                method: 'POST',
                data: this.horario,
                ...withApiAuth()
            };
            axios(req).then(response => {
                for (let sesion in response.data.data) {
                    let fila=this.horario.find(linea=>linea.sesion_orden==sesion);
                    if (fila)
                        fila.estado=response.data.data[sesion];
                    else {
                        this.msgs.push({
                            estate: 'Error', 
                            msg: 'Error al poner estado '+response.data.data[sesion]+' a la sesión '+sesion
                        });
                        console.log('Error al poner estado '+response.data.data[sesion]+' a la sesión '+sesion);
                    }
                }
                this.msgs.push({ estate: 'ok', msg: 'Guardat amb exit' });
            }, (error) => {
                this.msgs.push({ estate: 'ok', msg: 'Error: '+error });
                console.log(error);
            });
        },
        delMsg(index) {
            this.msgs.splice(index,1);
        }
    },
}

function diaSemana(date) {
    var dias_semana = ["D", "L", "M", "X", "J", "V", "S"];
    var fechaSel = new Date(date)
    return dias_semana[fechaSel.getDay()];
}
function toEnglish(date) {
    let arrFecha = date.split('-');
    arrFecha = arrFecha.map(dato => (dato.length == 1) ? "0" + dato : dato);
    return arrFecha[2] + '-' + arrFecha[1] + '-' + arrFecha[0];
}
</script>
