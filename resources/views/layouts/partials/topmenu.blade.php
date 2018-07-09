@if (!isset(AuthUser()->nia))
    <li><a href="javascript:;"><i class="fa fa-clock-o pull-right"></i>{!!trans("messages.generic.entrada")!!} -> {!! Entrada() !!}</a></li>
    <li><a href="javascript:;"><i class="fa fa-clock-o pull-right"></i>{!!trans("messages.generic.salida")!!} -> {!!  Salida() !!}</a></li>
    @if (Illuminate\Support\Facades\Session::get('userChange'))
        <li><a href='/profesor/change'><i class="fa fa-user pull-right"></i>{!!trans("messages.generic.change")!!}</a></li>
    @endif
    @if (esRol(AuthUser()->rol,config('roles.rol.direccion')))
        @if (Illuminate\Support\Facades\Session::get('completa'))
            <li><a href='/direccion/simplifica'><i class="fa fa-user pull-right"></i>{!!trans("messages.generic.simplifica")!!}</a></li>
        @else
            <li><a href='/direccion/simplifica'><i class="fa fa-users pull-right"></i>{!!trans("messages.generic.completa")!!}</a></li>
        @endif 
    @endif
    {!! Intranet\Entities\Menu::make('topmenu') !!}
@else
    {!! Intranet\Entities\Menu::make('topalumno') !!}
@endif
