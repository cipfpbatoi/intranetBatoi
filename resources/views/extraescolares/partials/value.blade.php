<div class="valueContainer col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
    <form method="POST"  enctype="multipart/form-data"
          action="/actividad/valoracion/">
        @csrf
        <div class="container">
                {!! Field::textarea('desenvolupament',$Actividad->desenvolupament) !!}
                {!! Field::textarea('valoracio',$Actividad->valoracio) !!}
                {!! Field::textarea('aspectes',$Actividad->aspectes) !!}
                {!! Field::textarea('dades',$Actividad->dades) !!}
                {!! Field::checkbox('recomanada',null,$Actividad->recomanada) !!}
        </div>
        <input type='hidden' id="estado" name='estado' value="4">
        <input type='hidden' id="idActividad" name='idActividad' value="{!!$Actividad->id!!}">
        <input id="submit" class="btn btn-info"
               type="submit" value="@lang("messages.buttons.value")
               @lang("models.modelos.Actividad") ">
        <a href="/actividad" class="btn btn-info" >@lang('messages.buttons.volver')</a>
    </form>

</div>

