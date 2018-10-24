@extends('layouts.pdf')
@section('content')
    @foreach ($todos as $elemento)
    <div class="page">
        @include('pdf.partials.cabecera')
        <br/><br/><br/>
        <div class="container col-lg-12" style="width:40%;float: right">
            {{$elemento->Alumno->FullName}}<br/>
            {{$elemento->Alumno->domicilio}}<br/>
            {{$elemento->Alumno->Municipio()}}<br/>
            {{$elemento->Alumno->Provincia->nombre}}<br/>
        </div>
        <div class="container" style="width:95%;clear:right;text-align: justify">
            <br/><br/><br/>
            <p style="text-indent: 30px">Per la present li comunique que donat el nombre de faltes d'assistència acumulades des de l'inici del curs es procedirà
                a la seua matrícula de {{Curso()}} del {{\Intranet\Entities\Alumno_grupo::where('idAlumno',$elemento->idAlumno)->first()->Grupo->Ciclo->ciclo}}, 
                seguint les instruccions de la Resolució del 19 de Juliol de 2017, de la Secretaria Autonòmica d'Educació i Investigació,
				per la que es dicten les instruccions sobre ordenació acadèmica i d'organització de l'activitat docent dels centres de la Comunitat Valenciana
				que durant el curs 2017-2018 impartisquen cicles formatius de Formació Profesional.</p>
            <p tyle="text-indent: 30px">L'interessat/da disposa de dos dies hàbils, des de la recepció d'aquest document,
                per a presentar la documentació que justifique degudament la causa de les absències.</p>
            <p tyle="text-indent: 30px">Davant aquesta resolució, l'interessat pot presentar recurs d'alçada en el termini d'un mes des de la seua notificació
                davant la Direcció Territorial d'Educació d'Alacant.</p>
        </div>
        <div class="container" style="width:90%;">
            <br/><br/>
            <br/><br/><br/>
            <p>{{config('constants.contacto.poblacion')}},a {{$datosInforme}} </p>
            <br/><br/><br/><br/><br/>
            <div style="width:45%; float:left; ">
                <p><strong>{{\Intranet\Entities\Profesor::find(config('constants.contacto.director'))->FullName}}</strong></p>
                <br/><br/><br/>
                <p>DIRECTOR</p>
            </div>
        </div>
    </div>
    @endforeach
@endsection