<template>
    <div>
        <a href="#" @click.prevent="clicked(true)" class="pull-left prev-day">{{ titulo }} anterior</a>
        <a href="#" @click.prevent="clicked(false)" class="pull-right next-day">{{ titulo }} siguiente</a>
    </div>  
</template>

<script>
export default {
  props: ['dias'],
  computed: {
    titulo() {
      return (this.dias==1?'Día':'Semana');
    }
  },
  methods: {
    clicked(atras) {
      this.$emit('click', (atras?0-this.dias:this.dias));
    }
  }
}
</script>