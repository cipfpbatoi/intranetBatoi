<div class="valueContainer col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
    <form method="POST"  enctype="multipart/form-data"
          action="{{route('actividad.valoracion.post')}}">
        @csrf
        <div class="container">
                <div class="form-group mb-3">
                    <label for="desenvolupament" class="form-label"><strong>Desenvolupament de l'activitat</strong></label>
                    <textarea id="desenvolupament" name="desenvolupament" class="form-control" rows="4" placeholder="Explica com s'ha desenvolupat l'activitat">{{ $Actividad->desenvolupament }}</textarea>
                </div>
                <div class="form-group mb-3">
                    <label for="valoracio" class="form-label"><strong>Valoració pedagògica de l'activitat</strong></label>
                    <textarea id="valoracio" name="valoracio" class="form-control" rows="4" placeholder="Indica els resultats pedagògics i l'impacte en l'alumnat">{{ $Actividad->valoracio }}</textarea>
                </div>
                <div class="form-group mb-3">
                    <label for="aspectes" class="form-label"><strong>Aspectes transversals</strong></label>
                    <textarea id="aspectes" name="aspectes" class="form-control" rows="4" placeholder="Descriu competències o valors treballats">{{ $Actividad->aspectes }}</textarea>
                </div>
                <div class="form-group mb-3">
                    <label for="dades" class="form-label"><strong>Altres dades de l'activitat</strong></label>
                    <textarea id="dades" name="dades" class="form-control" rows="4" placeholder="Afig observacions o informació rellevant">{{ $Actividad->dades }}</textarea>
                </div>
                <div class="form-group mb-3">
                    <div class="form-check">
                        <input id="recomanada" type="checkbox" name="recomanada" value="1" class="form-check-input" {{ $Actividad->recomanada ? 'checked' : '' }}>
                        <label for="recomanada" class="form-check-label">Es recomana per al curs següent?</label>
                    </div>
                </div>
        </div>
        <input type='hidden' id="estado" name='estado' value="4">
        <input type='hidden' id="idActividad" name='idActividad' value="{!!$Actividad->id!!}">
        <input id="submit" class="btn btn-info"
               type="submit" value="@lang("messages.buttons.value")
               @lang("models.modelos.Actividad") ">
        <a href="{{ route('actividad.index') }}" class="btn btn-info" >@lang('messages.buttons.volver')</a>
    </form>
</div>
