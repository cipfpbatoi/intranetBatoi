@extends('layouts.pdf')
@section('content')
    @php $material = $todos @endphp
    <div class="visible-print text-center">
        @include('pdf.inventario.partials.fitxa')
    </div>
@endsection