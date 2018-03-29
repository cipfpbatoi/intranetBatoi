<ul class="messages fct">
    @if  (UserisAllow(config('constants.rol.administrador')))
        @foreach (\Intranet\Entities\Fct::Empresa($elemento->id)->get() as $fct)
        <li>
            <div class="message_date">
                <h4 class="text-info"><i class="fa fa-calendar user-profile-icon"></i>{{$fct->desde}} - {{$fct->hasta}} <i class="fa fa-group user-profile-icon"></i> {{$fct->horas}}</h4>
                <h4 class="text-info"><acronym title='{{$fct->Instructores->first()->email}} ({{$fct->Instructores->first()->telefono}})'><i class="fa fa-user user-profile-icon"></i> {{$fct->Instructores->first()->nombre}} ({{$fct->Instructores->first()->dni}})</acronym></h4>
            </div>
            <div class="message_wrapper">
                <h4><a href='/fct/{!!$fct->id!!}/show'><i class="fa fa-eye"></i></a> <a href='/fct/{!!$fct->id!!}/delete'><a href='/fct/{!!$fct->id!!}/edit'><i class="fa fa-edit"></i></a> <a href='/fct/{!!$fct->id!!}/delete'><i class="fa fa-trash"></i></a><span class='info'>{!! $fct->Alumno->FullName !!}</span></h4>
                <h4><i class="fa fa-birthday-cake user-profile-icon"></i> {{$fct->Colaboracion->Centro->nombre}}</h4>
                <h4><i class="fa fa-phone user-profile-icon"></i> {{$fct->Colaboracion->telefono}} <i class="fa fa-envelope user-profile-icon"></i> {{$fct->Colaboracion->email}}</h4>
            </div>
        </li>    
        @endforeach
    @else
        @foreach (\Intranet\Entities\Fct::MisFcts()->Empresa($elemento->id)->get() as $fct)
        
        <li>
            <div class="message_date">
                <h4 class="text-info"><i class="fa fa-calendar user-profile-icon"></i>{{$fct->desde}} - {{$fct->hasta}} <i class="fa fa-group user-profile-icon"></i> {{$fct->horas}}</h4>
                <h4 class="text-info"><i class="fa fa-user user-profile-icon"></i> {{$fct->Instructores->first()->nombre}} ({{$fct->Instructores->first()->dni}})</h4>
                <a href="/fct/{!!$fct->id!!}/delete" class="delGrupo"><sub ><small style="color: purple "> @lang('messages.buttons.delete')</small></sub><i class="fa fa-trash-o user-profile-icon"></i></a>
            </div>
            <div class="message_wrapper">
                <h4><a href='/fct/{!!$fct->id!!}/edit'><sub ><small style="color: purple "> @lang('messages.buttons.edit')</small></sub> {!! $fct->Alumno->FullName !!}</a></h4>
                <h4><i class="fa fa-birthday-cake user-profile-icon"></i> {{$fct->Colaboracion->Centro->nombre}}</h4>
                <h4><i class="fa fa-phone user-profile-icon"></i> {{$fct->Colaboracion->telefono}} <i class="fa fa-envelope user-profile-icon"></i> {{$fct->Colaboracion->email}}</h4>
            </div>
        </li>    
        @endforeach
    @endif    

</ul>
@if(UserisAllow(config('constants.rol.tutor')))
<div class="message_wrapper">
    <!--<button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#AddFct">-->
    <a href='/fct/create/' class="btn btn-secondary">@lang("messages.generic.anadir") @lang("models.modelos.Fct")</a>
    <!--</button>-->
</div>
@endif
@include('layouts.partials.error')



