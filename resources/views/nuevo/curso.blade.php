@extends('layouts.intranet')
@section('css')
<title>Canvi de curs</title>
@endsection
@section('content')
<h3>Vas a esborrar les dades del curs passat</h3>
<p>Quedaràn les programacions i el gestor Documental. Vas a esborrar 'actividades','comisiones','cursos','expedientes','faltas','faltas_itaca','faltas_profesores',
            'fcts','grupos_trabajo','guardias','horarios','incidencias','notifications','ordenes_trabajo','reservas',
            'resultados','reuniones','tutorias_grupos'  </p>
<form method="POST" >
    {{ csrf_field() }}
    <input type='submit' value='Enviar'/>
</form>
@endsection
@section('titulo')
Esborrar Dades curs anterior
@endsection