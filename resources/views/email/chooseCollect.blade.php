@extends('layouts.intranet')
@section('css')
<title>Spam generator</title>
@endsection
@section('content')
<div class='x-content'>

    <div class='form_box'>
       <form method="POST" action='/direccion/myMail' class='form-horizontal form-label-left' enctype="multipart/form-data"> 
    {{ csrf_field() }}
    <div class="form-group item"><label class="control-label col-md-3 col-sm-3 col-xs-12">Col.lectiu:</label>
        <div class="col-md-6 col-sm-6 col-xs-12"><select name='collect' class="form-control">
            @foreach (config('auxiliares.collectMailable') as $key => $colectiu)
                <option value='{{$key}}'>{{$colectiu}}</option>
            @endforeach
            </select>
        </div>
    </div>
    <div class="form-group item"><label class="control-label col-md-3 col-sm-3 col-xs-12">Adjunt:</label>
        <div class="col-md-6 col-sm-6 col-xs-12"><input type='file' name='file'></div>
    </div>
    
    <input type='submit' class='btn btn-success'value='Enviar'/>
</form>
</div>
</div>
@endsection
@section('titulo')
Enviament emails massiu
@endsection


