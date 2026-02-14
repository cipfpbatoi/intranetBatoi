@extends('layouts.email')
@section('body')
<table style='text-align: center'>
    <tr>
        <th>Titol Alumne</th>
    </tr>
</table>
<div>
    <table style="border:#000 solid 1;">
        <tr >
            <td><strong>De: </strong>Secretaria del CIPFP BATOI </td>
        </tr>
    </table>
</div>
<div class="container" >
    Estimat/ada <strong>{{$fct->Alumno->fullName}}</strong>:<br/>
    Ja has estat avaluat/ada de les practiques @if ($fct->calProyecto) i del projecte @endif. En cas de que hajes aprovat tots els mòduls és l'hora
    de sol·licitar el títol. El procediment el tens en el següent <a href="http://www.cipfpbatoi.es/index.php/ca/tramits/">enllaç</a>. Si tens qualsevol dubte possat en contacte amb el teu tutor.<br/>
    Salutacions cordials.
</div>
@endsection