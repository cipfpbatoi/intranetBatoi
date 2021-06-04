@php
    $misElementos = $panel->getElementos($pestana)->where('tutor',AuthUser()->dni);
    $otros =  $panel->getElementos($pestana)->where('tutor','!=',AuthUser()->dni);
@endphp
@foreach ($misElementos as $elemento)
    @php
        $fcts = \Intranet\Entities\Fct::with('Instructor')->where('idColaboracion',$elemento->id)->where('asociacion',1)->orderBy('desde','desc')->get();
    @endphp
    @if (count($fcts))
        @foreach ($fcts as $fct)
            @php
                $contactos = \Intranet\Entities\Activity::mail('Fct')->id($fct->id)->orderBy('created_at')->get();
                $alumnos = $fct->Alumnos;
            @endphp
            @include('intranet.partials.profile.partials.fct')
        @endforeach
    @else
        @php
        $contactos = \Intranet\Entities\Activity::mail('Colaboracion')->id($elemento->id)->orderBy('created_at')->get();
        @endphp
        @include ('intranet.partials.profile.partials.colaboracion')
    @endif
@endforeach
@foreach ($otros as $elemento)
    @php
        $fcts = \Intranet\Entities\Fct::with('Instructor')->where('idColaboracion',$elemento->id)->where('asociacion',1)->get();
    @endphp
    @if (count($fcts))
        @foreach ($fcts as $fct)
            @php
                $contactos = \Intranet\Entities\Activity::mail('Fct')->id($fct->id)->orderBy('created_at')->get();
                $alumnos = $fct->Alumnos;
            @endphp
            @include('intranet.partials.profile.partials.fct')
        @endforeach
    @else
        @php
            $contactos = \Intranet\Entities\Activity::mail('Colaboracion')->id($elemento->id)->orderBy('created_at')->get();
        @endphp
        @include ('intranet.partials.profile.partials.colaboracion')
    @endif
@endforeach
