<template>
<div id="app">
            <br><label for="dia"> Dia: </label>
            <fecha-picker id="dia" v-model='dia' type="text" name="dia" class="noFlotar date" autofocus @change='elige'></fecha-picker>
            <button @click="elige">Tria data:</button>
            <table class="table" id="horarios">
                <tr><th style="text-align:center">Hores</th><th style="text-align:center">Grup</th>
                    <th style="text-align:center">No he marcat birret</th><th style="text-align:center">Estava en el centre</th>
                    <th style="text-align:center">Justificació</th><th style="text-align:center">Estat</th></tr>
                    <tr v-for="(hora,key,index) in horario">
                        <td>{{hora.desde}} - {{hora.hasta}}</td>
                        <td>{{hora.idGrupo}}</td>
                        <template v-if="hora.estado<2">
                            <td><input type="checkbox" v-model="hora.checked"></td>
                            <td>
                                <i v-if="hora.enCentro" class="fa fa-check"></i>
                                <i v-else class="fa fa-times"></i>
                            </td>
                            <td v-if='index==0' v-bind:rowspan='Object.keys(horario).length'><textarea v-bind:rows='Object.keys(horario).length*2' v-model="hora.justificacion" ></textarea></td>
                            <td><span v-if="hora.estado==1">Pendent</span>
                                <span v-if="hora.estado==0">No comunicada</span>
                            </td>
                            
                        </template>
                        <template v-else-if="hora.estado==2">
                            <td colspan="2"></td>
                            <td>{{hora.justificacion}}</td>
                            <td>Justificada</td>
                        </template>  
                        <template v-else>
                            <td colspan="2"></td>
                            <td>{{hora.justificacion}}</td>
                            <td>Rebutjada</td>
                        </template> 
                    </tr>                  
                </table>
            <div id="botones">
                <button  class="btn btn-success" @click="confirmar">Enviar</button>
            </div>
            <div class="errores"></div>
        </div>
</template>
<script>
import axios from 'axios'

const token=document.getElementById('_token').innerHTML;
const idProfesor = document.getElementById('dni').innerHTML;

export default {
    
  components: {
    'fecha-picker': require('../utils/FechaPicker.vue'),
  },
  data() {
    return {
      dia: '',
      horario: [],
    }
  },
  methods: {
    elige: function () {
            var diaF = this.dia;
            //axios.get('http://intranet.my/api/horario/dia_semana='+diaSemana(diaF)+'&idProfesor='+idProfesor+'&ocupacion=null&modulo!TU02CF?api_token='+token).then((response) => {
            axios.get('/api/itaca/' + diaF + '/' + idProfesor + '?api_token=' + token).then((response) => {
                this.horario = response.data.data;
            }, (error) => {
                console.log(error);
            });
        },
        confirmar: function () {
            let req = {
                url: '/api/itaca?api_token=' + token,
                method: 'POST',
                data: this.horario
            };
            axios(req).then(response => {
                console.log(response.data.content);
                alert('Guardat amb exit');
            }, (error) => {
                console.log(error);
            });
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
