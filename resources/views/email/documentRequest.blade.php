@extends('layouts.email')
@section('body')
<table style='text-align: center'>
    <tr>
        <th>Solicitud de documentacion practicas de FCT</th>
    </tr>
</table>
<div>
    <table style=" border:#000 solid 1;">
        <tr >
            <td><strong>De {{AuthUser()->shortName}} del {{config('contacto.nombre')}} </strong></td>
        </tr>
    </table>
</div>
<div class="container" >
    <p>Hola {{$colaboracion->contacto}}:</p>
       <p>Este es un email automático para solicitar la documentación que hace falta para completar el anexo I de colaboración 
       de su empresa {{$colaboracion->empresa}} con el {{config('contacto.nombre')}}, para el desarrollo de las prácticas de FCT del 
       ciclo de {{$colaboracion->Ciclo->cliteral}}.</p>
       <p>Para realizar esta documentación nos hace falta:</p>
       <ul>
           <li>CIF y nombre de la empresa</li>
           <li>NIF y nombre del gerente</li>
           <li>NIF, nombre y correo del instructor de prácticas del alumno en la empresa</li>
           <li>Horario de trabajo</li>
       </ul>
       <p>Ruego remitan esa información al correo {{$email}}. <br/>
           <b>No contesten a este correu ya que es automático.</b></p>
    Gracias de antemano por su atención y disculpas por si ya le hemos solicitado esa información por otro canal de comunicación.<br/>           
    {{AuthUser()->shortName}}   
       
</div>
@endsection