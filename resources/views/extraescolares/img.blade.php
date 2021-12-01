@extends('layouts.intranet')
@section('css')
<title></title>
@endsection
@section('content')
<h4 class="centrado">{{trans("models.Actividad.titulo",['actividad'=>$actividad->name])}}</h4>
<div id="dropzone">
    <form action="/actividad/{{$actividad->id}}/fileupload"
          class="dropzone"
          id="my-dropzone"
          enctype="multipart/form-data">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div class="dz-message" style="height:200px;">
            Posa els teus fitxers ací (màxim 3)
        </div>
        <div class="dropzone-previews"></div>
        <button type="submit" class="btn btn-success" id="submit">Save</button>
    </form>
</div>
@endsection
@section('scripts')
    {{ Html::script('/js/Actividad/img.bo.js') }}
@endsection
