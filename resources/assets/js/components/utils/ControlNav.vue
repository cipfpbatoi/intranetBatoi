<template>
    <div class="control-nav">
        <a href="#" @click.prevent="clicked(true)" class="float-start prev-day">{{ tituloAnterior }}</a>
        <a href="#" @click.prevent="clicked(false)" class="float-end next-day">{{ tituloSiguiente }}</a>
    </div>  
</template>

<script>
export default {
  props: ['dias'],
  computed: {
    titulo() {
      if (Number(this.dias) === 1) {
        return 'Día';
      }

      if (Number(this.dias) === 28) {
        return '4 setmanes';
      }

      return 'Setmana';
    },
    tituloAnterior() {
      return this.titulo + ' anterior';
    },
    tituloSiguiente() {
      return this.titulo + ' següent';
    }
  },
  methods: {
    clicked(atras) {
      this.$emit('click', (atras?0-this.dias:this.dias));
    }
  }
}
</script>

<style scoped>
.control-nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
  margin-bottom: 0.5rem;
}

.control-nav a {
  display: inline-block;
}
</style>
