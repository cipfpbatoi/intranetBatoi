@php $horas = false;   @endphp
<form action='/fct/{!!$fct->id!!}/modificaHoras' method ='post'>
     @csrf
    <ul class="messages fct">
        @foreach ($fct->Instructores as $instructor)
        <li>
            <div class="message_date">
                <h4 class="text-info"><i class="fa fa-calendar-times-o user-profile-icon"></i> Certifica:
                    @if ($fct->Instructores->count() > 1 || $instructor->pivot->horas != $fct->horas)
                        @php $horas = true; @endphp          
                        <input id='{{$instructor->dni}}' name='{{$instructor->dni}}' value="{{$instructor->pivot->horas}}"> 
                    @else
                        {{$instructor->pivot->horas}}
                    @endif    
                    hores
                </h4>
                @if ($instructor->departamento) <h4 class="text-info">{{$instructor->departamento}}</h4> @endif
            </div>
            <div class="message_wrapper">
                @if ($fct->Instructores->count() > 1)
                    <h4 class="text-info"><a href='/fct/{!!$fct->id!!}/{!!$instructor->dni!!}/instructorDelete'><i class="fa fa-trash"></i></a>
                @endif    
                <acronym title='{{$instructor->email}} ({{$instructor->telefono}})'><i class="fa fa-user user-profile-icon"></i> {{$instructor->nombre}} ({{$instructor->dni}})</acronym></h4>
                <h4><i class="fa fa-phone user-profile-icon"></i> {{$instructor->telefono}} <i class="fa fa-envelope user-profile-icon"></i> {{$instructor->email}}</h4>
            </div>
        </li>    
        @endforeach
    </ul>
    @if(UserisAllow(config('roles.rol.tutor')))
        <div class="message_wrapper">
            @if ($fct->Colaboracion->Centro->Instructores->count() - $instructores->count()  > 0)
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#AddInstructor">
                @lang("messages.generic.anadir") @lang("models.modelos.Instructor")
                </button>
            @endif
            @if ($horas)
                <input type='submit'  class="btn btn-secondary" value="@lang('messages.generic.modificar') @lang('validation.attributes.horas')"/>
            @endif
        </div>
    @endif
 </form>
@include('fct.partials.modalInstructores')
@include('layouts.partials.error')



