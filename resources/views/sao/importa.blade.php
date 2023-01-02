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
                    @php
                        $alumno = Intranet\Entities\Alumno::find($fct['nia']);
                    @endphp
                    @isset($fct['colaboracio']['id'])
                        @php
                            $fcts = $alumno->fctsColaboracion($fct['colaboracio']['id'])->get();
                        @endphp
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="{{$key}}" id="{{$key}}flexRadio" checked>
                            <label class="form-check-label" for="flexRadioDefault1">
                                {{ Intranet\Entities\Centro::find($fct['centre']['id'])->nombre }} -
                                {{ $alumno->fullName }} =>
                                {{ $fct['hores'] }}
                                {{ count($fcts)?'Fct Existent':'Fct Nova' }}
                            </label>
                        </div>
                    @else
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="{{$key}}" id="{{$key}}flexRadio" checked>
                            <label class="form-check-label" for="flexRadioDefault1">
                                Erasmus -
                                {{ $alumno->fullName }} =>
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
