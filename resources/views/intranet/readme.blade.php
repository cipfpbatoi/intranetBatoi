@extends('layouts.intranet')
@section('css')
<title>Ajuda</title>
{{ Html::style('/css/md.css') }}
@endsection
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_content" style="display: block">
            <div>
                <ul class="to_do">
                    @markdown('### '.$elemento)
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
@section('titulo')
Ajuda
@endsection



