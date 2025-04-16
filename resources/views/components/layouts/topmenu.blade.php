<li class="dropdown">
    <a href="#" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        {{ $user->nombre }} {{ $user->apellido1 }}
        <span class="fa fa-angle-down"></span>
    </a>
    <ul class="dropdown-menu dropdown-usermenu pull-right">
        @if (!$isAlumno)
            <x-user-profile :usuario="$user" />
            {{-- Entrada i sortida --}}
            <li>
                <a href="javascript:;">
                    <i class="fa fa-clock-o pull-right"></i>
                    {!! trans("messages.generic.entrada") !!} -> {!! Entrada() !!}
                </a>
            </li>
            <li>
                <a href="javascript:;">
                    <i class="fa fa-clock-o pull-right"></i>
                    {!! trans("messages.generic.salida") !!} -> {!! Salida() !!}
                </a>
            </li>

            {{-- Opció per a Direcció --}}
            @if ($isDireccio)
                <li>
                    <a href="/direccion/simplifica">
                        <i class="fa {{ $simplifica ? 'fa-user' : 'fa-users' }} pull-right"></i>
                        {!! trans("messages.generic." . ($simplifica ? 'simplifica' : 'completa')) !!}
                    </a>
                </li>
            @endif

            {{-- Menú definit per configuració --}}
            {!! Intranet\Entities\Menu::make('topmenu') !!}
        @else
            {!! Intranet\Entities\Menu::make('topalumno') !!}
        @endif

        {{-- Opció per tornar si s'ha fet canvi d'usuari --}}
        @if ($userChange)
            <li>
                <a href="/profesor/backChange">
                    <i class="fa fa-user pull-right"></i>
                    {!! trans("messages.generic.backChange") !!}
                </a>
            </li>
        @endif
    </ul>
</li>