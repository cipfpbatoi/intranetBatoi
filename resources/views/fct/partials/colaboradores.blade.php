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
             @if ($horas)
                <div class="message_wrapper">
                    <input type='submit' class="btn btn-link"
                           value="@lang('messages.generic.modificar') @lang('validation.attributes.horas')"/>
                </div>
            @endif
             <button type="button" class="btn btn-link" data-toggle="modal" data-target="#AddInstructor">
                 <em class="fa fa-plus"></em>@lang("messages.generic.anadir") @lang("models.modelos.Colaborador")
             </button>
        @endif
    </form>

@include('fct.partials.modalColaboradores')
<x-ui.errors />




