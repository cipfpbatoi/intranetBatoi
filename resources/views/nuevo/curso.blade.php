@extends('layouts.intranet')
@section('css')
<title>Canvi de curs</title>
@endsection
@section('content')
<h3>Vas a esborrar les dades del curs passat</h3>
<p>Quedaràn les programacions i el gestor Documental. Vas a esborrar 'actividades','comisiones','cursos','expedientes','faltas','faltas_itaca','faltas_profesores',
            'fcts','grupos_trabajo','guardias','horarios','incidencias','notifications','ordenes_trabajo','reservas',
            'resultados','reuniones','tutorias_grupos','resultados_alumno,'votes'  </p>
<p>Recorda modificar el fitxer /config/curso.php amb les dades del nou curs i contacto.php si hi ha canvis en l'equip directiu</p>
<p></p>
<form method="POST" >
    {{ csrf_field() }}
    <input type="checkbox" name="Vots" checked/>Vots Permanents<br/>
    <input type="checkbox" name="Auxiliars" checked/>Esborrat taules auxiliars<br/>
    <input type="checkbox" name="Dual" checked/>Traspassar dual<br/>
    <input type="checkbox" name="Esborrat" />Esborrar dades<br/>
    <input type='submit' value='Enviar'/>
</form>
@endsection
@section('titulo')
Esborrar Dades curs anterior
@endsection