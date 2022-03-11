<div id="dropzone" class="valueContainer col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
    <form method="POST" enctype="multipart/form-data"
          action="/dropzone"
          class="dropzone" id="myDropzone">
        @csrf
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div class="dropzone-previews" style="clear: both">
            <div class="dz-message" style="height:200px;">
            </div>
        </div>
        <br/>
        <br/>
        <input type='hidden' id="id" name='id' value="{{ $id }}">
        <input type='hidden' id="modelo" name="modelo" value="{{ $modelo }}">
        <input type="hidden" name="api_token" value="{{AuthUser()->api_token}}">
        <input id="submit" class="hidden" type="submit">
        @foreach ($botones as $text => $link)
                <a href="{{$link}}" class="btn btn-info" >@lang("messages.buttons.$text")</a>
        @endforeach
    </form>
</div>

