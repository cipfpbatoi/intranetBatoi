<template>
  <div>
    <template v-if="ficha">
      <span :class="'label label-'+(ficha.realizada?'default':'warning')"><slot></slot></span>
      <p v-if="ficha.obs_personal">Nota: {{ ficha.obs_personal }}</p>
      <p v-if="ficha.observaciones">OBS: {{ ficha.observaciones }}</p>
    </template>
    <template v-else>
      <span class="label label-danger"><slot></slot></span>
    </template>
  </div>  
</template>

<script>
export default {
  props: ['ficha']
}
</script>