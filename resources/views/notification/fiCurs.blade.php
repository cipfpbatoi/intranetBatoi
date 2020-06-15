@extends('layouts.intranet')
@section('content')
        <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="x_panel">
                        <div class="x_content">
                                <div class="">
                                <ul class="to_do">
                                @foreach($avisos as $funcio => $grupAvisos)
                                        @foreach ($grupAvisos as $key => $grup)
                                                @foreach ($grup as $aviso)
                                                <li>
                                                    @if ($key == 'success')
                                                        <span style="color:blue">
                                                    @else
                                                        <span style="color:red">
                                                    @endif
                                                        <strong>{{ ucwords($funcio)}}</strong> : {{ $aviso }}
                                                    </span>
                                                </li>
                                                @endforeach
                                        @endforeach
                                @endforeach
                                </ul>
                                </div>
                        </div>
                </div>
        </div>
@endsection
@section('titulo')
        Me'n Puc anar de vacances ??
@endsection

