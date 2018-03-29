<template>
  <div id="tableContainer" class="calendario">
    <table class="table" id="horario">
      <tbody>
        <tr v-for="hora in horas" :key="hora.id">
          <th>{{hora.turno}} {{hora.hora_ini}}-{{hora.hora_fin}}</th>
          <td v-if="estaReservado(hora.codigo)" class="warning">
            {{ reservas[hora.codigo].nomProfe }} ({{ reservas[hora.codigo].observaciones }})
            <span class="hidden idProfe">{{ reservas[hora.codigo].idProfesor }}</span>
            <span class="hidden idReserva">{{ reservas[hora.codigo].id }}</span>
          </td>
          <td v-else>Libre</td>
        </tr>        
      </tbody>
    </table>  
  </div>
</template>

<script>
    export default {
        props: ['horas', 'reservas'],
        methods: {
          idTd(codigo) {
            return 'hora-'+codigo;
          },
          estaReservado(hora) {
            return this.reservas[hora]!=undefined;
          },
        },
    }
</script>