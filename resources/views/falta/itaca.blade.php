@extends('layouts.intranet')
@section('css')
{{Html::style('/assets/datetimepicker/css/bootstrap-datetimepicker.css') }}
{{Html::style('/assets/datetimepicker/css/bootstrap-datetimepicker.min.css') }}
<title>{{trans("models.Falta_itaca.edit")}}</title>
@endsection
@section('content')
<div class="formularionormal borderedondo">
    <div class="contenedor centrado">
        <br><h4 class="centrado">{{ trans('models.modelos.Falta_itaca')}}</h4><br>
        <div id="gestion" >
            @{{ dia }}
            <datepicker></datepicker>
            <br><label for="dia"> {{ trans('validation.attributes.dia')}}: </label>
            <input  id="dia" v-model='dia' type="text" name="dia" class="noFlotar date" autofocus @change='elige()' />
            <button @click="elige">{{ trans('messages.buttons.chooseDate')}}</button>
            <table class="table" id="horarios">
                <tr><th style="text-align:center">{{ trans('validation.attributes.horario')}}</th><th style="text-align:center">{{ trans('validation.attributes.Grupo')}}</th>
                    <th style="text-align:center">{{ trans('validation.attributes.birret')}}</th><th style="text-align:center">{{ trans('validation.attributes.enCentro')}}</th>
                    <th style="text-align:center">{{ trans('validation.attributes.justificacion')}}</th><th style="text-align:center">{{ trans('validation.attributes.estado')}}</th></tr>
                    <tr v-for="(hora,key,index) in horario">
                        <td>@{{hora.desde}} - @{{hora.hasta}}</td>
                        <td>@{{hora.idGrupo}}</td>
                        <template v-if="hora.estado<2">
                            <td><input type="checkbox" v-model="hora.checked"></td>
                            <td>
                                <i v-if="hora.enCentro" class="fa fa-check"></i>
                                <i v-else class="fa fa-times"></i>
                            </td>
                            <td v-if='index==0' v-bind:rowspan='Object.keys(horario).length'><textarea v-bind:rows='Object.keys(horario).length*2' v-model="hora.justificacion" ></textarea></td>
                            <td><span v-if="hora.estado==1">{{trans('models.Falta_itaca.1')}}</span>
                                <span v-if="hora.estado==0">{{trans('models.Falta_itaca.0')}}</span>
                            </td>
                            
                        </template>
                        <template v-else-if="hora.estado==2">
                            <td colspan="2"></td>
                            <td>@{{hora.justificacion}}</td>
                            <td>{{trans('models.Falta_itaca.2')}}</td>
                        </template>  
                        <template v-else>
                            <td colspan="2"></td>
                            <td>@{{hora.justificacion}}</td>
                            <td>{{trans('models.Falta_itaca.3')}}</td>
                        </template> 
                    </tr>                  
                </table>
            <div id="botones">
                <button  class="btn btn-success" @click="confirmar">{{ trans('messages.buttons.submit')}}</button>
            </div>
            <div class="errores"></div>
        </div>
    </div>
</div>
@endsection
@section('titulo')
{{trans("models.Falta_itaca.edit")}}
@endsection
@section('scripts')
{{ Html::script("/assets/axios/dist/axios.js") }}
{{ Html::script("/assets/vue/dist/vue.js") }}
{{ Html::script("/js/Falta/itaca.js") }}
{{ Html::script('/assets/moment.js') }}
{{ Html::script('/assets/datetimepicker/js/bootstrap-datetimepicker.min.js') }}
{{ Html::script("/js/datepicker.js") }}
@endsection