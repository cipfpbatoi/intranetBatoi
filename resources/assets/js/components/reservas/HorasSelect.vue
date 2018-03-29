<template>
  <div>
    <label>{{ title }}:</label>
    <select :value="value" @input="updateValue($event.target.value)" class="noFlotar">
      <option value="0">-- Selecciona --</option>
      <option v-for="hora in horas" :key="hora.codigo"
       :value="hora.codigo" :disabled="hora.codigo<iniVal">
        {{hora.turno}} {{hora.hora_ini}}-{{hora.hora_fin}}
      </option>
    </select>
  </div>
</template>

<script>
    export default {
        props: ['value', 'horas', 'title', 'iniVal'],
        methods: {
          updateValue(value) {
            this.$emit('input', value);
          },
        }
    }
</script>
