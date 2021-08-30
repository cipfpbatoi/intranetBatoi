<div class="valueContainer col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
    <form method="POST" class="agua" action="/actividad/{!!$Actividad->id!!}">
        @csrf
        @method('PATCH')
        {!! Field::textarea('desenvolupament',$Actividad->desenvolupament) !!}
        {!! Field::textarea('valoracio',$Actividad->valoracio) !!}
        {!! Field::textarea('aspectes',$Actividad->aspectes) !!}
        {!! Field::textarea('dades',$Actividad->dades) !!}
        {!! Field::checkbox('recomanada',null,$Actividad->recomanada) !!}
        <input type='hidden' name='estado' value="4">
        <input type='hidden' name='idActividad' value="{!!$Actividad->id!!}">
        <input id="submit" class="boton" type="submit" value="@lang("messages.buttons.value") @lang("models.modelos.Actividad") ">
        <a href="/actividad" class="boton" >@lang('messages.buttons.volver')</a>
    </form>

</div>