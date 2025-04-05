<div class='form_box'>
    {!! Form::model($elemento,['class'=>'form-horizontal form-label-left']) !!}
    {{ method_field('PUT') }}
    {!! Field::radios('criterios',['1','2','3','4','5'],$elemento->criterios,['inline']) !!}
    {!! Field::radios('metodologia',['1','2','3','4','5'],$elemento->metodologia,['inline']) !!}
    {!! Field::textarea('propuestas',$elemento->propuestas) !!}
    <a href="{{\Intranet\Services\NavigationService::getPreviousUrl()}}?back=true" class="btn btn-danger">
        @lang("messages.buttons.cancel")
    </a>
    {!! Form::submit(trans('messages.buttons.submit'),['class'=>'btn btn-success','id'=>'submit']) !!}
    {!! Form::close() !!}
</div>
<x-ui.errors />

