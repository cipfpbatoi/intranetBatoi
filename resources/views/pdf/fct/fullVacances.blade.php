@extends('layouts.pdf')
@section('content')
        <div style="font-size:large;line-height: 2em">
            @include('pdf.partials.cabecera')
            <br/>
            <div class="container col-lg-12" style="width:90%;">
                @php  $grupo = \Intranet\Entities\Grupo::find(AuthUser()->grupoTutoria); @endphp
                <p style="text-indent: 50px">En/Na: {{ AuthUser()->fullName }} tutor de FCT del grup:<strong> {{ $grupo->nombre  }}</strong>
                    del cicle formatiu: <strong>{{ $grupo->Ciclo->cliteral }}</strong> de la familia profesional:
                    {{  substr(AuthUser()->Ldepartamento,12) }}.
            </div>
            <div class="container" >
                <strong>COMUNICA:</strong>
                <br/>
            </div>
            <div class="container" style="width:95%">
                <p style="text-indent: 30px;text-align: justify">
                    Segon Ordre 77/2010 en el seu article 14.3, la rel·lació de l'alumnat que realitzarà les pràcticas formatives
                    durant periode escolar no lectiu és:
                </p>
                <ul>
                @foreach ($todos as $alumno)
                    <li>{{$alumno->Alumno->dni}} - {{$alumno->fullName}}</li>
                @endforeach
                </ul>
            </div>
            <hr/>
            <div class="container" style="width:95%">
                <p style="text-indent: 30px;text-align: justify">Durant este periode els seus centres de práctiques romanen oberts amb el mateix servei,
                    així que l'alumnat pot acudir en el seu horari habitual, complint amb el programa formatiu,
                    per tal de poder completar la realització de les pràctiques en el periode ordinari.</p>
                <p style="text-indent: 30px;text-align: justify">Durant este periode l'alumnat i el centre podran contactar de forma telefònica o via e-mail amb el seu tutor/a per resoldre qualsevol incidència que poguera sorgir.</p>
                <p style="text-indent: 30px;text-align: justify">Per tal que conste als efectes oportuns,</p>
            </div>
            <div class="container col-lg-6" style='padding-right: 200px;float:right' >
                <p>{!! config('contacto.poblacion') !!} a {{ FechaString() }}</p><br/><br/>
                <p>Signatura :</p>
            </div>
        </div>
@endsection
