<template>
      <select id="recurso" :value="value" @input="updateValue($event.target.value)">
        <option value="0">-- Selecciona --</option>
        <option v-for="espacio in espacios" :value="espacio.aula">{{ espacio.descripcion }}</option>
      </select>
</template>

<script>
    export default {
        props: ['value', 'espacios'],
        mounted() {
          console.log('ok rec')
        },
        methods: {
          updateValue(value) {
            this.$emit('input', value);
          },
        }
    }
</script>
