@extends('layouts.intranet')
@section('css')
    <title>Omplir lot {{$lote}}</title>
@endsection
@section('content')
    <form method="post">
        @csrf
        @foreach ($materiales as $material)
            <strong>{{ $material->descripcion }}</strong>
                <input type="text" list="articulos" name="{{$material->id}}"/>
                <datalist id="articulos">
                    @foreach (Intranet\Entities\Articulo::orderBy('descripcion')->get() as $articulo)
                        <option>{{ucfirst(strtolower($articulo->descripcion))}}</option>
                    @endforeach
                </datalist>
            <br/>
        @endforeach
        <input type="submit" value="enviar" />
    </form>
@endsection
@section('titulo')
    Omplir lot {{$lote}}
@endsection

