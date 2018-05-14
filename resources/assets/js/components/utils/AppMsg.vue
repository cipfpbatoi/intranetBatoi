<template>
  <div class="x_content">
    <div :class="clases">
      <button type="button" class="close" data-dismiss="alert" @click="close">×</button>
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
      return 'alert alert-block alert-'+(this.estate=='ok'?'success':'danger')+' fade in';
    }
  },
  methods: {
    close() {
      this.$emit('close');
    }
  }
}
</script>