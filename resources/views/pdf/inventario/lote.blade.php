@extends('layouts.label')
@section('content')
    @foreach ($todos as $material)
        <div class="visible-print text-center" style="float:left;width:8.2cm;height:4.15cm;margin: 0cm;padding-left: 0.5cm;padding-top: 0.5cm">
            @include('pdf.inventario.partials.fitxa')
        </div>
    @endforeach
@endsection