@php $horas = false;   @endphp
<form action='/fct/{!!$fct->id!!}/modificaHoras' method='post'>
    @csrf
    <ul class="messages fct">
        @foreach ($fct->Colaboradores->sortBy('surnames') as $instructor)
            <li>
                <div class="message_date">
                    <h4 class="text-info"><em class="fa fa-calendar-times-o user-profile-icon"></em> Certifica:
                        @if ($fct->Colaboradores->count() > 1 || $instructor->horas != $fct->horas)
                            @php $horas = true; @endphp
                            <input id='{{$instructor->idInstructor}}' name='{{$instructor->idInstructor}}'
                                   value="{{$instructor->horas}}">
                        @else
                            {{$instructor->horas}}
                        @endif
                        hores
                    </h4>
                </div>
                <div class="message_wrapper">
                    <h4 class="text-info">
                        <a href='/fct/{!!$fct->id!!}/{!!$instructor->idInstructor!!}/instructorDelete'>
                            <em class="fa fa-trash"></em>
                        </a>
                        <em class="fa fa-user user-profile-icon"></em> {{$instructor->name}}
                                ({{$instructor->idInstructor}})
                    </h4>
                </div>
            </li>
        @endforeach
    </ul>
    @if(userIsAllow(config('roles.rol.tutor')))
        <div class="message_wrapper">
            <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#AddInstructor">
                    @lang("messages.generic.anadir") @lang("models.modelos.Colaborador")
            </button>
            @if ($horas)
                <input type='submit' class="btn btn-secondary"
                       value="@lang('messages.generic.modificar') @lang('validation.attributes.horas')"/>
            @endif
        </div>
    @endif
</form>
@include('fct.partials.modalColaboradores')
@include('layouts.partials.error')



