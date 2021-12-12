<div id="dropzone" class="valueContainer col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
    <form method="POST"  enctype="multipart/form-data"
          action="/actividad/valoracion/"
          class="dropzone" id="myDropzone">
        @csrf
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div class="container">
                {!! Field::textarea('desenvolupament',$Actividad->desenvolupament) !!}
                {!! Field::textarea('valoracio',$Actividad->valoracio) !!}
                {!! Field::textarea('aspectes',$Actividad->aspectes) !!}
                {!! Field::textarea('dades',$Actividad->dades) !!}
                {!! Field::checkbox('recomanada',null,$Actividad->recomanada) !!}
        </div>
        <div class="dropzone-previews" style="clear: both">
            <div class="dz-message" style="height:200px;">
                Posa els teus fitxers ací (màxim 3)
            </div>
        </div>
        <br/>
        <br/>
        <input type='hidden' id="image1" name='image1' value="">
        <input type='hidden' id="image2" name='image2' value="">
        <input type='hidden' id="image3" name='image3' value="">
        <input type='hidden' id="estado" name='estado' value="4">
        <input type='hidden' id="idActividad" name='idActividad' value="{!!$Actividad->id!!}">
        <input id="submit" class="btn btn-info" type="submit" value="@lang("messages.buttons.value") @lang("models.modelos.Actividad") ">
        <a href="/actividad" class="btn btn-info" >@lang('messages.buttons.volver')</a>
    </form>

</div>

