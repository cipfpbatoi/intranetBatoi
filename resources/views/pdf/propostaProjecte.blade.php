@extends('layouts.pdf')

@php ($projecte = $todos)
@php ($alumno = $projecte->Alumno)

@section('content')

    @include('pdf.partials.cabecera')

    <div class="container">
        <table class="table table-bordered" style="width: 100%">
            <tr>
                <th><h1>Proposta Projecte de Cicle Formatiu Superior</h1></th> <!-- Títol principal més gran -->
            </tr>
        </table>
    </div>

    <div class="container">
        <p style="font-size: 16px;">
            Jo, {{$alumno->fullName}}, amb DNI núm. {{$alumno->dni}}, alumne del CFS "{{ $projecte->Grupo->Ciclo->vliteral }}” en el CIPFP Batoi d'Alcoi, presente la següent proposta per al Mòdul "Projecte de {{ $projecte->Grupo->Ciclo->vliteral}}”:
        </p>
        <table class="table table-bordered" style="width: 100%;">
            <tr>
                <th style="text-align: left; padding: 12px; font-weight: bold; border-bottom: 2px solid black; font-size: 18px;">Títol</th> <!-- Ús de bord inferior negre per simular el destacat -->
            </tr>
            <tr>
                <td style="text-align: left; padding: 12px; font-size: 16px;">{{$projecte->titol}}</td>
            </tr>

            <tr>
                <th style="text-align: left; padding: 12px; font-weight: bold; border-bottom: 2px solid black; font-size: 18px;">Objectius generals del projecte i resultats esperats</th>
            </tr>
            <tr>
                <td style="text-align: left; padding: 12px; font-size: 16px;">{{$projecte->objectius}}</td>
            </tr>

            <tr>
                <th style="text-align: left; padding: 12px; font-weight: bold; border-bottom: 2px solid black; font-size: 18px;">Possibles aplicacions pràctiques del projecte (en general). En cas d'aplicació real concreta del projecte (en una empresa o ...), especificar el lloc on es posarà en marxa i descripció breu d'on i com es posarà en marxa</th>
            </tr>
            <tr>
                <td style="text-align: left; padding: 12px; font-size: 16px;">{{$projecte->resultats}}</td>
            </tr>

            <tr>
                <th style="text-align: left; padding: 12px; font-weight: bold; border-bottom: 2px solid black; font-size: 18px;">Recursos a utilitzar:</th>
            </tr>
            <tr>
                <td style="text-align: left; padding: 12px; font-size: 16px;">{{$projecte->recursos}}</td>
            </tr>

            <tr>
                <th style="text-align: left; padding: 12px; font-weight: bold; border-bottom: 2px solid black; font-size: 18px;">Descripció del contingut del projecte, incloent el pla de treball previst</th>
            </tr>
            <tr>
                <td style="text-align: left; padding: 12px; font-size: 16px;">{{$projecte->descripcio}}</td>
            </tr>

            <tr>
                <th style="text-align: left; padding: 12px; font-weight: bold; border-bottom: 2px solid black; font-size: 18px;">Observacions i comentaris :</th>
            </tr>
            <tr>
                <td style="text-align: left; padding: 12px; font-size: 16px;">{{$projecte->observacions}}</td>
            </tr>
        </table>
    </div>
    <p style="font-size: 16px;">I espere que aquesta proposta siga acceptada com a vàlida.</p>
    <div style="margin-top: 20px; text-align: center;"> <!-- Centrat de la data i la firma -->
        <p style="font-size: 16px;">Alcoi a {{$datosInforme}}</p>
        <p style="margin-top: 40px; font-size: 16px;">Firma: ___________________________</p>
        <p style="font-size: 16px;">Nom i cognoms: {{$alumno->fullName}}</p>
    </div>
     @include('pdf.partials.pie',['document'=>'valoracio'])
 @endsection

