@extends('layouts.pdf')
@section('content')
    @php $material = $todos @endphp
    <div class="visible-print text-center" style="float:right;width: 200px;height:225px">
        @include('pdf.inventario.partials.fitxa')
    </div>
@endsection