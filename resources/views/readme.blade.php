@extends('layouts.intranet')
@section('css')
<title>Ajuda</title>
@endsection
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_content">
            <div class="">
                <ul class="to_do">
                    @markdown($elemento)
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
@section('titulo')
Ajuda
@endsection



