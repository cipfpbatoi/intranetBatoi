@extends('layouts.intranet')
@section('css')
<title>Certificado riesgos laborales</title>
@endsection
@php $pestana = $panel->getPestanas()[0] @endphp
@section($pestana->getNombre())
<div class="formularionormal borderedondo">
        <div class="contenedor">
        <form id="form" action="php/gestionDirModAlum.php" method="post" enctype="multipart/form-data">
            <br><h4 class="centrado"> CERTIFICADO DE RIESGOS LABORALES</h4><br>
            <br>
            <div class="nivel">
                <label for="horas"> HORAS DURACION CURSO *</label>
                <input id="horas" type="text" name="horas" value="30" required placeholder="Número de horas"/>
            </div>
            <div class="nivel">
                <label for="ciclo"> Ciclo *</label>
                <select id="ciclo" name="ciclo">
                    <option value="-1"> - Selecciona ciclo - </option>
                </select>
           </div>
            <br><br>
            <div class="nivel panel_manipulador">
                <label for="grupo"> SELECCION GRUPO:</label>
                <select name="grupo" id="grupo">
                </select>
            </div>
            </br></br>
            <div id="tableContainer2" class="tabla">

            </div>
            <div class="panel_manipulador">
                <label > SELECCION ALUMNO:</label>
                <input type="text" id="buscaralumno" name="buscaralumno"  autofocus/>
            </div>
            <div id="box">
                        <ul id="ulAlumnContainer">
 
                        </ul>
                    </div>
            </br><br><br>
            <div id="tableContainer" class="tabla">
            </div>
            </br></br>
            <span id="submit"></span>
            <br><br>
        </form>
        </div>
        </div>
@include($pestana->getVista(),$pestana->getFiltro())
@endsection
@section('titulo')
Certificat riscs laborals
@endsection
@section('scripts')
{{ Html::script("/js/list.js") }}
@endsection