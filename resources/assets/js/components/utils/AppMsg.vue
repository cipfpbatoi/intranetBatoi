<template>
  <div class="x_content">
    <div :class="clases">
      <button type="button" class="btn-close" aria-label="Close" @click="close"></button>
      <p>
        <strong>{{ msg }}</strong>
      </p>
    </div>
  </div>  
</template>

<script>
export default {
  name: 'app-msg',
  props: ['estate', 'msg'],
  computed: {
    clases() {
      return 'alert alert-'+(this.estate=='ok'?'success':'danger')+' alert-dismissible fade show';
    }
  },
  methods: {
    close() {
      this.$emit('close');
    }
  }
}
</script>
