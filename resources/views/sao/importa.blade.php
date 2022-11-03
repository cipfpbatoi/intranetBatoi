@extends('layouts.intranet')
@section('css')
    <title>Selecció Fcts</title>
@endsection
@section('content')
    <div class='x-content'>
        <div class='form_box'>
            <form method="POST" action='/sao/importa' class='form-horizontal form-label-left'>
                {{ csrf_field() }}
                @foreach ($dades as $key => $fct)
                    @isset($fct['colaboracio']['id'])
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="{{$key}}" id="{{$key}}flexRadio" checked>
                            <label class="form-check-label" for="flexRadioDefault1">
                                {{ Intranet\Entities\Centro::find($fct['centre']['id'])->nombre }} -
                                {{ Intranet\Entities\Alumno::find($fct['nia'])->fullName }} =>
                                {{ $fct['hores'] }}
                            </label>
                        </div>
                    @endisset
                @endforeach
                <br/>
                <input type='submit' class='btn btn-success'value='Enviar'/>
                <a href="{{route('alumnofct.index')}}" class='btn btn-danger'>Cancelar</a>
            </form>

        </div>
    </div>
@endsection
@section('titulo')
    Gestió Importació SAO
@endsection