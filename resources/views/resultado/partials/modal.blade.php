<!-- Modal -->
<div id="aviso" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            <h4 class="modal-title">{{ trans('models.Resultado.llenar')}}</h4>
            </div>
            <div class="modal-body">
                <form id='formAviso' action="/resultado/infTrimestral" method="POST">
                    {{ csrf_field() }}
                    @if (isset($informes[evaluacion()-1])) 
                    {{ method_field('PUT') }} 
                    <input type='hidden' name='reunion' value="{{$informes[evaluacion()-1]}}" />
                    @endif
                    <input type='hidden' name='trimestre' value='{{evaluacion()-1}}' />
                    <label class='control-label' for='observaciones'>   {!! trans('validation.attributes.observaciones') !!}: </label>   
                    <textarea id='observaciones' name="observaciones" class="form-control" placeholder="{{trans('validation.attributes.observaciones')}}" style=' width: 570px; height:300px;'/>@if (isset($informes[evaluacion()-1])){!! Intranet\Entities\OrdenReunion::where('idReunion',$informes[evaluacion()-1])->where('orden',1)->first()->resumen !!}@endif</textarea>
                    @if (evaluacion()-1==3)
                        <label class='control-label' for='proyectos'>   {!! trans('validation.attributes.proyectos') !!}: </label>   
                        <textarea id='proyectos' name="proyectos" class="form-control" placeholder="{{trans('validation.attributes.proyectos')}}" style=' width: 570px; height:300px;'/>@if (isset($informes[evaluacion()-1])){!! Intranet\Entities\OrdenReunion::where('idReunion',$informes[evaluacion()-1])->where('orden',2)->first()->resumen !!}@endif</textarea>
                    @endif
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="formAviso" class="btn btn-primary">{{trans('messages.buttons.init')}}</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('messages.buttons.cancel')}}</button>
            </div>
        </div>
    </div>
</div>
