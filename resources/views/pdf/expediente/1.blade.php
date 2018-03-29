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
            <p style="text-indent: 30px">Por la presente le comunico que dado el número de faltas de asistencia acumuladas desde el inicio de curso se procederá
                a su matrícula de {{Curso()}} del {{\Intranet\Entities\Alumno_grupo::where('idAlumno',$elemento->idAlumno)->first()->Grupo->Ciclo->ciclo}}, 
                siguiendo las instrucciones de la Resolución del 19 de julio de 2017, de la Secretaria Autonómica de Educación e Investigación, 
                por la que se dictan instrucciones sobre ordenación académica y de organización de la actividad docente de los centros de la Comunitat Valenciana 
                que durante el curso 2017­2018 impartan ciclos formativos de Formación Profesional.</p>
            <p tyle="text-indent: 30px">El interesado/a dispone de dos días hábiles, desde la recepción de este documento, 
                para presentar la documentación que justifique debidamente la causa de las ausencias.</p>
            <p tyle="text-indent: 30px">Contra esta resolución, el interesado puede presentar recurso de alzada en el plazo de un mes desde su notificación
                ante la Dirección Territorial de Educación de Alicante.</p>
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
