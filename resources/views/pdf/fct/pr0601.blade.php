@extends('layouts.pdf')
@section('content')
<body style="max-width:27.59cm;margin-top:1.251cm; margin-bottom:1.251cm; margin-left:1.5cm; margin-right:1.5cm; ">
    @include('pdf.fct.partials.cabecera')
    <br/>
    <table border="1" style="margin-bottom: 10px">
        <colgroup><col width="449"/><col width="329"/></colgroup>
        <tr>
            <td style="text-align:left;width:13.283cm;padding-left: 5px; "><strong>Revisión actual:</strong><span> 1</span></td>
            <td style="text-align:left;width:10.523cm;padding-left: 5px; "><strong>Fecha revisión actual:</strong><span> 4-06-2012</span></td>
        </tr>
        <tr>
            <td style="text-align:left;padding-left: 5px;" colspan="2"><strong>Objetivo:</strong><span>  Comprobar la recepción del certificado de prácticas por parte del alumno</span></td>
        </tr>
    </table>

    <table border="1">
        <colgroup><col width="347"/><col width="153"/><col width="238"/></colgroup>
        <tr>
            <td style="text-align:left;width:9.938cm;padding-left: 5px; "><strong>ALUMNO/OS</strong></td>
            <td style="text-align:left;width:5.493cm;padding-left: 5px; "><strong>FECHA</strong></td>
            <td style="text-align:left;width:7.45cm;padding-left: 5px; "><strong>FIRMA ALUMNO</strong></td>
        </tr>
        @foreach ($todos as $alumno)
        <tr><td style="text-align:left;width:9.938cm;padding-left: 5px;font-size: 0.8em;" >{{ $alumno->Alumno->FullName }} </p></td>
            <td style="text-align:left;width:5.493cm; " ></td>
            <td style="text-align:left;width:7.45cm; " ></td>
        </tr>
        @endforeach
        <tr><td colspan="3" style="text-align:center;width:21.938cm;height: 12.938cm;font-size: 14px;" >
                <p s>He recibido por parte del tutor el certificado de realización de las prácticas donde figuran los puestos de trabajo que he cubierto y las horas realizadas en el mismo</p>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <p>FIRMA TUTOR</p>
                <br/>
                <br/>
                <br/>
                <br/>
                
            </td>
        </tr>
    </table>


</body>
</html>
@endsection