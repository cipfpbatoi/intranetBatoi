<ul class="messages colaboracion">
    @foreach ($fcts??[] as $fct)
        <ul class="messages fct">
            <em class="fa fa-credit-card"></em> {{$fct->Instructor->id??'No hi ha instructor'}}
            <em class="fa fa-user user-profile-icon"></em> {{$fct->Instructor->Nombre??''}}
            <em class="fa fa-envelope"></em> {{$fct->Instructor->email??''}}
            <em class="fa fa-phone"></em> {{$fct->Instructor->telefono??''}}
            <form action='/fct/{!!$fct->id!!}/modificaHoras' method='post'>
                @csrf
                @foreach ($fct->Colaboradores->sortBy('surnames') as $instructor)
                    <li>
                        <div class="message_date">
                            <h4 class="text-info"><em class="fa fa-calendar-times-o user-profile-icon"></em> Certifica:
                                    <input id='{{$instructor->idInstructor}}' name='{{$instructor->idInstructor}}'
                                           value="{{$instructor->horas}}">
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
                @if(userIsAllow(config('roles.rol.tutor')))
                    <div class="message_wrapper">
                        <button
                                type="button"
                                class="btn btn-secondary"
                                data-toggle="modal"
                                data-target="#AddInstructor"
                        >
                                @lang("messages.generic.anadir") @lang("models.modelos.Colaborador")
                        </button>
                        <input type='submit' class="btn btn-secondary"
                               value="@lang('messages.generic.modificar') @lang('validation.attributes.horas')"/>
                    </div>
                @endif
            </form>
        </ul>
    @endforeach
</ul>
@include('colaboracion.partials.modalColaboradores')
@include('layouts.partials.error')



