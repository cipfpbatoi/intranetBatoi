<div id="dropzone" class="valueContainer col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
    <form method="POST"  enctype="multipart/form-data"
          action="/expediente/adjuntos/"
          class="dropzone" id="myDropzone">
        @csrf
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div class="dropzone-previews" style="clear: both">
            <div class="dz-message" style="height:200px;">
                Posa els teus fitxers ací
            </div>
        </div>
        <br/>
        <br/>
        <input type='hidden' id="image1" name='image1' value="">
        <input type='hidden' id="image2" name='image2' value="">
        <input type='hidden' id="image3" name='image3' value="">
        <input type='hidden' id="idExpediente" name='idExpediente' value="{!!$expediente->id!!}">
        <input id="submit" class="btn btn-info" type="submit" value="@lang("messages.buttons.submit")">
        <a href="/expediente" class="btn btn-info" >@lang('messages.buttons.volver')</a>
    </form>

</div>

