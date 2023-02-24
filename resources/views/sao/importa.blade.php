@extends('layouts.intranet')
@section('css')
    <title>Selecció Fcts</title>
@endsection
@section('content')
    <div class='x-content'>
        <div class='form_box'>
            <form method="POST" action='/sao/importa' class='form-horizontal form-label-left'>
                {{ csrf_field() }}
                <input name="ciclo" type="hidden" value="{{$ciclo}}" />
                @foreach ($dades as $key => $fct)
                    @php
                        $alumno = Intranet\Entities\Alumno::find($fct['nia']);
                    @endphp
                    @if($fct['erasmus'] == 'No')
                        <div class="form-check">
                                <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="{{$key}}"
                                        id="{{$key}}flexRadio"
                                        checked
                                >
                                <label class="form-check-label" for="flexRadioDefault1">
                                    @isset($fct['centre']['id'])
                                        {{ Intranet\Entities\Centro::find($fct['centre']['id'])->nombre }}
                                    @else
                                        {{"No trobat ".$fct['centre']['name'].". Marca per crear" }}
                                    @endisset
                                    - {{ $alumno->fullName }} => {{ $fct['hores'] }}
                                </label>
                        </div>
                    @else
                        <div class="form-check">
                            <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="{{$key}}"
                                    id="{{$key}}flexRadio"
                                    checked
                            >
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
