@foreach ($panel->getElementos($pestana) as $elemento)
    @php
        $contactCol = \Intranet\Entities\Activity::mail('Colaboracion')->id($elemento->id)->orderBy('created_at')->get();
        $fcts = \Intranet\Entities\Fct::where('idColaboracion',$elemento->id)->where('asociacion',1)->get();
    @endphp
    @if (count($fcts))
        @foreach ($fcts as $fct)
            @php
                $contactFct = \Intranet\Entities\Activity::mail('Fct')->id($fct->id)->orderBy('created_at')->get();
                $alumnos = $fct->Alumnos;
                if (count($alumnos))
                    $contactAl = \Intranet\Entities\Activity::mail('Alumno')->id($alumnos->first()->nia)->orderBy('created_at')->get();
                else
                    $contactAl = collect();
                $contacted = $contactFct->merge($contactCol);
                $contacted = $contacted->merge($contactAl);
                $contacted = $contacted->sortBy('created_at');
            @endphp

            @include('intranet.partials.profile.partials.fct')
        @endforeach
    @else
        @include ('intranet.partials.profile.partials.colaboracion')
    @endif
@endforeach
