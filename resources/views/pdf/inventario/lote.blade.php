@extends('layouts.label')
@section('content')
    @foreach ($todos as $material)
        <div class="visible-print text-center" style="float:left;width:7cm;height:3.7cm;margin-right: 0cm;margin-bottom: 0cm">
            @include('pdf.inventario.partials.fitxa')
        </div>
    @endforeach
@endsection