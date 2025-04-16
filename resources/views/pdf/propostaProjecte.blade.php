@extends('layouts.pdf')

@php ($projecte = is_array($todos)?$todos[0]:$todos)
@php ($alumno = $projecte->Alumno)

@section('content')

    @include('pdf.partials.cabecera')

    <div class="container">
        <table class="table table-bordered" style="width: 100% ">
            <tr>
                <td style="background-color: #f2f2f2 !important; font-size: 24px; font-weight: bold; text-align: center;">
                    Proposta Projecte de Cicle Formatiu Superior
                </td>
            </tr>
        </table>
    </div>

    <div class="container">
        <p style="font-size: 16px;">
            Jo, {{$alumno->fullName}}, amb DNI núm. {{$alumno->dni}}, alumne del CFS "{{ $projecte->Grupo->Ciclo->vliteral }}” en el CIPFP Batoi d'Alcoi, presente la següent proposta per al Mòdul "Projecte de {{ $projecte->Grupo->Ciclo->vliteral}}”:
        </p>
        <table class="table table-bordered" style="width: 100%;">
            <tr>
                <td style="text-align: left; padding: 12px; font-weight: bold; background-color: #f2f2f2 !important; font-size: 18px;">Títol</td>
            </tr>
            <tr>
                <td style="text-align: left; padding: 12px; font-size: 16px;">{{$projecte->titol}}</td>
            </tr>

            <tr>
                <td style="text-align: left; padding: 12px;   background-color: #f2f2f2 !important; font-size: 18px;"><strong>Objectius</strong> generals del projecte i <strong>resultats esperats</strong></td>
            </tr>
            <tr>
                <td style="text-align: left; padding: 12px; font-size: 16px;">{{$projecte->objectius}}</td>
            </tr>

            <tr>
                <td style="text-align: left; padding: 12px;  background-color: #f2f2f2 !important; font-size: 18px;">Possibles <strong>aplicacions pràctiques</strong> del projecte (en general).<strong> En cas d'aplicació real </strong>concreta del projecte (en una empresa o ...), especificar el lloc on es posarà en marxa i descripció breu d'on i com es posarà en marxa</td>
            </tr>
            <tr>
                <td style="text-align: left; padding: 12px; font-size: 16px;">{{$projecte->resultats}}</td>
            </tr>

            <tr>
                <td style="text-align: left; padding: 12px;   background-color: #f2f2f2 !important; font-size: 18px;"><strong>Recursos</strong> a utilitzar:</td>
            </tr>
            <tr>
                <td style="text-align: left; padding: 12px; font-size: 16px;">{{$projecte->recursos}}</td>
            </tr>

            <tr>
                <td style="text-align: left; padding: 12px;   background-color: #f2f2f2 !important; font-size: 18px;"><strong>Descripció del contingut</strong> del projecte, incloent el pla de treball previst</td>
            </tr>
            <tr>
                <td style="text-align: left; padding: 12px; font-size: 16px;">{{$projecte->descripcio}}</td>
            </tr>

            <tr>
                <td style="text-align: left; padding: 12px;   background-color: #f2f2f2 !important; font-size: 18px;"><strong>Observacions</strong> i comentaris:</td>
            </tr>
            <tr>
                <td style="text-align: left; padding: 12px; font-size: 16px;">{{$projecte->observacions}}</td>
            </tr>
        </table>
    </div>
    <p style="font-size: 16px;">I espere que aquesta proposta siga acceptada com a vàlida.</p>
    <div style="margin-top: 20px; text-align: center;">
        <p style="font-size: 16px;">Alcoi a {{$datosInforme}}</p>
        <p style="margin-top: 40px; font-size: 16px;">Firma: ___________________________</p>
        <p style="font-size: 16px;">Nom i cognoms: {{$alumno->fullName}}</p>
    </div>
    @include('pdf.partials.pie',['document'=>'propuestaProyecto'])
@endsection
