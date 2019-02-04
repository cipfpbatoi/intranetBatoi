@extends('layouts.email')
@section('body')
<table style='text-align: center'>
    <tr>
        <th>Solicitud de documentacion</th>
    </tr>
</table>
<div>
    <table style=" border:#000 solid 1;">
        <tr >
            <td><strong>De: </strong></td>
        </tr>
    </table>
</div>
<div class="container" >
    <p>Hola {{$colaboracion->contacto}}:</p>
       <p>Este es un email automático para solicitar la documentación que hace falta para completar el anexo I de colaboración 
       de su empresa {{$colaboracion->empresa}} con el CIPFP Batoi, para el desarrollo de las prácticas de FCT.
       Para realizar esta documentación nos hace falta:</p>
       <ul>
           <li>CIF y nombre de la empresa</li>
           <li>NIF y nombre del gerente</li>
           <li>NIF, nombre y correo del instructor de prácticas del alumno en la empresa</li>
           <li>Horario de trabajo</li>
       </ul>
       <p>Ruego remitan esa información al correo {{$email}}. <br/>
           No contesten a este correu ya que es automático.</p>
    Gracias de antemano por su atención.           
    {{AuthUser()->shortName}}   
       
</div>
@endsection