<template id="app">
    <div class="col-md-4 col-sm-4 col-xs-12 profile_details">
        <div class="well profile_view">
            <div  class="col-sm-12">
                <h4 class="brief">
                    <em class="fa fa-calendar"></em>
                    {{id}} {{*salida}} - <span v-if="entrada">{{ *entrada }}</span>
                </h4>
                <h6>{{*idProfesor}}</h6>
                <div class="left col-xs-12">
                    <h5>{{*servicio}} </h5>
                    <ul class="list-unstyled">
                        <li><em class="fa fa-automobile"></em> {{*medio}} - {{*kilometraje}} km.</li>
                        <li><em class="fa fa-automobile"></em> {{*marca}} {{*matricula}}</li>
                        <li><em class="fa fa-money"></em> {{alojamiento + comida + gastos }}</li>
                    </ul>
                </div>
            </div>
            <div class="col-xs-12 bottom text-center">
                <div class="col-xs-12 col-sm-4 emphasis">
                    <p class="ratings">
                         <a href='#' class='btn btn-danger btn-xs' >
                                {{ situacion }}</a>
                    </p>
                </div>
                <div class="col-xs-12 col-sm-8 emphasis">
                    
                </div>
            </div>
        </div>
    </div>
</template>

<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.24/vue.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue-resource/0.7.2/vue-resource.min.js"></script>
<script>
new Vue({
    el: '#app',
        data: {
        comisiones: []
        },
        created: function(){
        this.$http.get('http://intranet.app/api/comision/')
                .then(function (response){
                this.comisiones = response.data.results.map(function (comision){
                return {
                        id: comision.id,
                        servicio : comision.servicio,
                        salida : comision.salida,
                        entrada : comision.entrada,
                        medio : comision.medio,
                        marca : comision.marca,
                        estado : comision.estado,
                        kilometraje : comision.kilometraje,
                        matricula : comision.matricula,
                        alojamiento : comision.alojamiento,
                        comida : comision.comida,
                        gastos: comida.gastos
                }
            })
        }.bind(this))
        }
});
//Vue.component('comision-card', {
//    template: '#comision-card',
//    props: [servicio,salida,entrada,medio,marca,estado,kilometraje,matricula,alojamiento,comida,gastos],
//});
</script>
