@extends('layouts.pdf')
@section('content')
    @foreach ($todos as $material)
        <div class="visible-print text-center" style="float:left;width:5cm;height:5cm;margin-right: 0.8cm;margin-bottom: 0.82cm">
            @include('pdf.inventario.partials.fitxa')
        </div>
    @endforeach
@endsection