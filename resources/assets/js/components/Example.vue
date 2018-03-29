<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Example Component</div>

                    <div class="panel-body">
                        I'm an example component!
      <select id="recurso2">
        <option value="0">-- Selecciona --</option>
        <option v-for="espacio in espacios" :value="espacio.aula">{{ espacio.descripcion }}</option>
      </select>
                          </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['espacios'],
        mounted() {
            console.log('Component muy ready.');
            console.log(this.espacios);
        }
    }
</script>
