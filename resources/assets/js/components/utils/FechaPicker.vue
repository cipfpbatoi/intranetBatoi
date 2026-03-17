<template>
  <input
    type="date"
    class="noFlotar date"
    :value="value"
    :min="fechaIni || null"
    :max="fechaFin || null"
    @input="updateValue($event.target.value)"
  />
</template>

<script>
export default {
  props: ['value', 'fechaIni', 'fechaFin'],
  methods: {
    updateValue(value) {
      this.$emit('input', value);
    },
  },
}
</script>
