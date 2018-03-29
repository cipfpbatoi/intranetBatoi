<ul class="messages fct">
        @foreach ($elemento->Instructores as $instructor)
        <li>
            <div class="message_date">
                <h4 class="text-info"><i class="fa fa-calendar-times-o user-profile-icon"></i> Certifica: {{$instructor->pivot->horas}} hores</h4>
                @if ($instructor->departamento) <h4 class="text-info">{{$instructor->departamento}}</h4>@endif
            </div>
            <div class="message_wrapper">
                <h4 class="text-info"><a href='/fct/{!!$elemento->id!!}/{!!$instructor->dni!!}/instructorEdit'><i class="fa fa-edit"></i></a> <a href='/fct/{!!$elemento->id!!}/{!!$instructor->dni!!}/instructorDelete'><i class="fa fa-trash"></i></a>
                <acronym title='{{$instructor->email}} ({{$instructor->telefono}})'><i class="fa fa-user user-profile-icon"></i> {{$instructor->nombre}} ({{$instructor->dni}})</acronym></h4>
                <h4><i class="fa fa-phone user-profile-icon"></i> {{$instructor->telefono}} <i class="fa fa-envelope user-profile-icon"></i> {{$instructor->email}}</h4>
            </div>
        </li>    
        @endforeach
</ul>
@if(UserisAllow(config('constants.rol.tutor')))
<div class="message_wrapper">
    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#AddInstructor">
    @lang("messages.generic.anadir") @lang("models.modelos.Instructor")
    </button>
</div>
@endif
@include('fct.partials.modalInstructores')
@include('layouts.partials.error')



