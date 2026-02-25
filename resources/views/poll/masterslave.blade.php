@extends('layouts.intranet')
@section('css')
<title>{{trans("models.$modelo.show")}}</title>
@endsection
@section('content')
<!-- page content -->
<div class="x_content">
    <section class="content invoice">
        
        <div class="row">
            <div class="col-xs-12 invoice-header">
                <h1>
                    <i class="fa fa-globe"></i> @lang("models.modelos.$modelo") 
                    <small class="pull-right">{{$elemento->actiu}}</small>
                </h1>
            </div>
        </div>
        <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
                {{$elemento->id}}.{{$elemento->title}}
            </div>
            
        </div>
        
        <!-- Table row -->
        <div class="row">
            <div class="col-xs-12 table">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>@lang('validation.attributes.question')</th>
                            <th>@lang('validation.attributes.scala')</th>
                            <th>@lang('validation.attributes.operaciones')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($elemento->options as $option)
                        <tr>
                            <td>{{$option->question}}</td>
                            <td>{{$option->scala}}</td>
                            <td><a href="{{ route('option.destroy', ['id' => $option->id]) }}" class="delGrupo"><i class="fa fa-trash"></i></a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
        <!-- this row will not appear when printing -->
        <div class="message_wrapper">
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#AddOption">
                        @lang("messages.generic.anadir") @lang("models.modelos.Option")
                </button>
        </div>
        
    </section>
</div>
@include('poll.partials.modalOption')
<!-- /page content -->
@endsection
@section('titulo')
{{trans("models.$modelo.show")}} {{$elemento->getKey()}}
@endsection
@section('scripts')

@endsection
