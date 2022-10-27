@extends('layouts.intranet')
@section('css')
{{Html::style('/assets/datetimepicker/css/bootstrap-datetimepicker.css') }}
{{Html::style('/assets/datetimepicker/css/bootstrap-datetimepicker.min.css') }}
<title>Connexió amb SAO</title>
@endsection
@section('content')
<div class='x-content'>
    <div class='form_box'>
       <form method="POST" action='/sao/createFct' class='form-horizontal form-label-left'>
            {{ csrf_field() }}
            <div class="form-group item">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">El teu Password d'Itaca:</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type='password' class="" id='password' name='password'/>
                </div>
            </div>
            <div class="form-group item">
               <label class="control-label col-md-3 col-sm-3 col-xs-12">Profesor per a fer importació:</label>
               <div class="col-md-6 col-sm-6 col-xs-12">
                   <select class="select-box" name='profesor'>
                       @foreach ($tutores as $tutor)
                           <option value="{{$tutor->dni}}">{{$tutor->nameFull}}</option>
                       @endforeach
                   </select>
               </div>
            </div>
            <input type='submit' class='btn btn-success'value='Enviar'/>
        </form>
    </div>
</div>
@endsection
@section('titulo')
    Gestió Importació SAO
@endsection