<div class="top_nav">
    <div class="nav_menu">
        <nav>
            {{-- Botó per desplegar menú lateral --}}
            <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>

            <ul class="nav navbar-nav navbar-right">
                {{-- Perfil usuari --}} 
                <x-layouts.topmenu />

                {{-- Icona de fitxatge (només si no té NIA) --}}
                @unless(isset($user->nia))
                    <li>
                        <a href="{{ url('/ficha') }}">
                            @if (estaDentro())
                                <img src="{{ asset('img/clock-icon.png') }}" alt="reloj" class="iconomediano" id="imgFitxar">
                            @else
                                <img src="{{ asset('img/clock-icon-rojo.png') }}" alt="reloj" class="iconomediano" id="imgFitxar">
                            @endif
                        </a>
                    </li>
                @endunless

                {{-- Bústia --}}
                <li>
                    <a href="{{ route('bustia.form') }}" title="Bústia">
                        <i class="fa fa-comments" style="color:#7e3ff2"></i>
                        <span class="hidden-xs"> Bústia</span>
                    </a>
                </li>
                @can('manage-bustia-violeta')
                    @php
                        $pendents = \Intranet\Entities\BustiaVioleta::pendents()->count();
                    @endphp
                    <li>
                        <a href="{{ route('bustia.admin') }}" title="Administrar Bústies">
                            <i class="fa fa-shield" style="color:#e67e22"></i>
                            @if($pendents > 0)
                                <span class="badge bg-red">{{ $pendents }}</span>
                            @endif
                            <span class="hidden-xs"> Admin Bústies</span>
                        </a>
                    </li>
                @endcan

                {{-- Notificacions --}}
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-envelope-o"></i>
                        @if ($totalNotifications > 0)
                            <span class="badge bg-green">{{ $totalNotifications }}</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu list-unstyled msg_list" role="menu">
                        @foreach ($notifications as $notification)
                            @php
                                $data = $notification->data ?? [];
                                $motiu = is_array($data['motiu'] ?? null) ? implode(', ', $data['motiu']) : ($data['motiu'] ?? '');
                                $emissor = is_array($data['emissor'] ?? null) ? implode(', ', $data['emissor']) : ($data['emissor'] ?? '');
                                $enlace = is_string($data['enlace'] ?? null) ? $data['enlace'] : '#';
                                $fecha = is_string($data['data'] ?? null) ? $data['data'] : (is_array($data['data'] ?? null) ? implode(' ', $data['data']) : ($data['data'] ?? ''));
                            @endphp
                            <li id="{{ $notification->id }}">
                                <a class="papelera" href="{{ url('/notification/'.$notification->id.'/delete') }}">
                                    <span class="image">
                                        <img src="{{ asset('img/delete.png') }}" alt="Marcar com a llegida" class="iconopequeno" />
                                    </span>
                                </a>
                                <a href="{{ $enlace }}">
                                    <span>
                                        <span>{{ $emissor }}</span>
                                        <span class="time">{{ $fecha }}</span>
                                    </span>
                                    <span class="message {{ $enlace != '#' ? 'blue' : '' }}">
                                        {{ $motiu }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                        <div class="text-center">
                            <a href="{{ url('/notification') }}">
                                <strong>@lang("messages.buttons.seeAll")</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </div>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</div>
