<template>
  <div>
    <label>{{ title }}:</label>
    <select id='idProfe' :value="value" @input="updateValue($event.target.value)" class="noFlotar">
      <option value="0">-- Selecciona --</option>
      <option v-for="profe in profes" :key="profe.dni"
       :value="profe.dni" >
        {{profe.apellido1}} {{profe.apellido2}}, {{profe.nombre}}
      </option>
    </select>
  </div>
</template>

<script>
    export default {
        props: ['value', 'profes', 'title'],
        methods: {
          updateValue(value) {
            this.$emit('input', value);
          },
        }
    }
</script>
