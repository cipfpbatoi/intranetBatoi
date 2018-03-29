@extends('layouts.intranet')
@section('css')
<title>{{trans("models.$modelo.show")}}</title>
@endsection
@section('content')
<div class="col-md-6 col-sm-6 col-xs-12">
    <div class="x_panel">
        <div class="x_content">
            <div class="">
                <ul class="to_do">
                    @yield('show_content')
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
@section('titulo')
{{trans("models.$modelo.show")}} {{$elemento->getKey()}}
@endsection
@section('scripts')

@endsection

