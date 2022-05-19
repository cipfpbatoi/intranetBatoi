@extends('layouts.intranet')
@section('content')
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="x_panel">
            <form method="post" action="/secure">
                @csrf
                Selecciona dispositiu:
                <select name="dispositivo">
                    @foreach ($doors as $door)
                        <option value="{{$door->dispositivo}}">{{$door->descripcion}}</option>
                    @endforeach
                </select>
                <input type="submit" class="btn btn-dark" value="Tanca" />
                <a href="/" class="btn btn-light">Tornar</a>
            </form>
            @isset($missatge)
                <br/>{{$missatge}}<br/>
            @endisset
        </div>
    </div>
@endsection
@section('titulo')
    Tancar la porta
@endsection

