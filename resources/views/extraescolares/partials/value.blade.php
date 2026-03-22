<div class="valueContainer col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
    <form method="POST"  enctype="multipart/form-data"
          action="{{route('actividad.valoracion.post')}}">
        @csrf
        <div class="container">
                <textarea name="desenvolupament">{{ $Actividad->desenvolupament }}</textarea>
                <textarea name="valoracio">{{ $Actividad->valoracio }}</textarea>
                <textarea name="aspectes">{{ $Actividad->aspectes }}</textarea>
                <textarea name="dades">{{ $Actividad->dades }}</textarea>
                <label>
                    <input type="checkbox" name="recomanada" value="1" {{ $Actividad->recomanada ? 'checked' : '' }}>
                    Recomanada
                </label>
        </div>
        <input type='hidden' id="estado" name='estado' value="4">
        <input type='hidden' id="idActividad" name='idActividad' value="{!!$Actividad->id!!}">
        <input id="submit" class="btn btn-info"
               type="submit" value="@lang("messages.buttons.value")
               @lang("models.modelos.Actividad") ">
        <a href="{{ route('actividad.index') }}" class="btn btn-info" >@lang('messages.buttons.volver')</a>
    </form>
</div>
