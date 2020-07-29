<!-- Modal Nou -->
<x-modal name="aviso" title='{{ trans('models.Resultado.llenar')}}' message='{{ trans("messages.buttons.init")}}'>
    @if ($reunion = \Intranet\Http\Controllers\PanelListadoEntregasController::existeInforme())
        {{ method_field('PUT') }}
        <input type='hidden' name='reunion' value="{{$reunion->id}}" />
    @endif
    <input type='hidden' name='trimestre' value='{{evaluacion()-1}}' />
    <label class='control-label' for='observaciones'>   {!! trans('validation.attributes.observaciones') !!}: </label>
    <textarea id='observaciones' name="observaciones" class="form-control" placeholder="@lang("validation.attributes.observaciones")" style=' width: 570px; height:300px;'/>@if (isset($informes[evaluacion()-1])){!! Intranet\Entities\OrdenReunion::where('idReunion',$informes[evaluacion()-1])->where('orden',1)->first()->resumen !!}@endif</textarea>
    @if (evaluacion()-1==3)
        <label class='control-label' for='proyectos'>   {!! trans('validation.attributes.proyectos') !!}: </label>
        <textarea id='proyectos' name="proyectos" class="form-control" placeholder="@lang("validation.attributes.proyectos")" style=' width: 570px; height:300px;'/>@if (isset($informes[evaluacion()-1])){!! Intranet\Entities\OrdenReunion::where('idReunion',$informes[evaluacion()-1])->where('orden',2)->first()->resumen !!}@endif</textarea>
    @endif
</x-modal>
