<div style="position:absolute;left:361.60px;top:11.70px" class="cls_002">
    <span class="cls_002">ANNEX VII / </span>
    <span class="cls_003">ANEXO VII</span>
</div>
<div style="position:absolute;left:339.90px;top:42.60px" class="cls_004">
    <span class="cls_004">INFORME INDIVIDUALITZAT DE L’INSTRUCTOR O INSTRUCTORA</span>
</div>
<div style="position:absolute;left:338.80px;top:55.20px" class="cls_005">
    <span class="cls_005">INFORME INDIVIDUALIZADO DEL INSTRUCTOR O INSTRUCTORA</span>
</div>
<div style="position:absolute;left:35.50px;top:86.10px" class="cls_006">
    <span class="cls_006">A</span>
</div>
<div style="position:absolute;left:57.18px;top:87.30px;width: 500px" class="cls_007">
    <span class="cls_007">DADES DE L’ALUMNE O ALUMNA / </span>
    <span class="cls_008">DATOS DEL ALUMNO O ALUMNA</span>
</div>
<div style="position:absolute;left:28.70px;top:107.00px" class="cls_009">
    <span class="cls_009">NIA</span><br/>
    <span class="cls_004"> {{$todos->Alumno->nia}}</span>
</div>
<div style="position:absolute;left:169.20px;top:107.70px" class="cls_009">
    <span class="cls_009">NOM</span>
    <span class="cls_010">/ NOMBRE</span><br/>
    <span class="cls_004"> {{$todos->Alumno->nombre}}</span>
</div>
<div style="position:absolute;left:355.90px;top:107.70px" class="cls_009">
    <span class="cls_009">COGNOMS</span>
    <span class="cls_010"> / APELLIDOS</span><br/>
    <span class="cls_004"> {{$todos->Alumno->apellido1}} {{$todos->Alumno->apellido2}}</span>
</div>
<div style="position:absolute;left:670.60px;top:107.00px" class="cls_009">
    <span class="cls_009">NIF</span><br/>
    <span class="cls_004"> {{$todos->Alumno->dni}}</span>
</div>
<div style="position:absolute;left:28.70px;top:129.70px" class="cls_009">
    <span class="cls_009">CORREU ELECTRÒNIC</span>
    <span class="cls_010"> / CORREO ELECTRÓNICO</span><br/>
    <span class="cls_004"> {{$todos->Alumno->email}}</span>
</div>
<div style="position:absolute;left:447.80px;top:129.00px" class="cls_009">
    <span class="cls_009">DATA DE NAIXEMENT </span>
    <span class="cls_010">/ FECHA DE NACIMIENTO</span><br/>
    <span class="cls_004"> {{$todos->Alumno->fecha_nac}}</span>
</div>
<div style="position:absolute;left:28.70px;top:151.70px" class="cls_009">
    <span class="cls_009">FAMÍLIA PROFESSIONAL / </span>
    <span class="cls_010">FAMILIA PROFESIONAL</span><br/>
    <span class="cls_004"> {{$todos->Colaboracion->Ciclo->Departament->literal}}</span>
</div>
<div style="position:absolute;left:447.80px;top:151.70px" class="cls_009">
    <span class="cls_009">CICLE FORMATIU / </span>
    <span class="cls_010">CICLO FORMATIVO</span><br/>
    <span class="cls_004">{{$todos->Colaboracion->Ciclo->ciclo}}</span>
</div>
<div style="position:absolute;left:28.70px;top:174.40px" class="cls_009">
    <span class="cls_009">DENOMINACIÓ DEL CENTRE EDUCATIU</span>
    <span class="cls_010"> / DENOMINACIÓN DEL CENTRO EDUCATIVO</span><br/>
    <span class="cls_004">{{$datosInforme['centro']}}</span>
</div>
<div style="position:absolute;left:310.30px;top:174.40px" class="cls_009">
    <span class="cls_009">CODI CENTRE </span>
    <span class="cls_010">/ CÓDIGO CENTRO</span><br/>
    <span class="cls_004"> {{$datosInforme['codigo']}}</span>
</div>
<div style="position:absolute;left:447.80px;top:174.40px" class="cls_009">
    <span class="cls_009">TUTOR O TUTORA  FP DUAL</span><br/>
    <span class="cls_004"> {{AuthUser()->FullName}}</span>
</div>
<div style="position:absolute;left:28.70px;top:197.10px" class="cls_009">
    <span class="cls_009">EMPRESA</span><br/>
    <span class="cls_004"> {{$todos->Colaboracion->Centro->nombre}}</span>
</div>
<div style="position:absolute;left:355.90px;top:197.10px" class="cls_009">
    <span class="cls_009">INSTRUCTOR O INSTRUCTORA</span><br/>
    <span class="cls_004"> {{$todos->Instructores->first()->Nombre }}</span>
</div>
<div style="position:absolute;left:670.60px;top:197.10px" class="cls_009">
    <span class="cls_009">NIF</span><br/>
    <span class="cls_004"> {{$todos->Instructores->first()->dni }}</span>
</div>
<div style="position:absolute;left:35.50px;top:226.20px" class="cls_006">
    <span class="cls_006">B</span>
</div>
<div style="position:absolute;left:57.10px;top:227.50px;width: 500px" class="cls_007">
    <span class="cls_007">INFORME</span>
    <span class="cls_008"> / INFORME</span>
</div>
<div style="position:absolute;left:71.60px;top:252.40px" class="cls_011">
    <span class="cls_011">LLOC FORMATIU OCUPAT</span>
</div>
<div style="position:absolute;left:213.30px;top:252.40px" class="cls_011">
    <span class="cls_011">DATA DE DD/MM/AA A DD/MM/AA</span>
</div>
<div style="position:absolute;left:354.20px;top:252.40px;width: 150px" class="cls_011">
    <span class="cls_011">Nre. DE JORNADES</span>
</div>
<div style="position:absolute;left:455.00px;top:252.40px;width: 150px" class="cls_011">
    <span class="cls_011">Nre. HORES</span>
</div>
<div style="position:absolute;left:583.60px;top:252.40px" class="cls_011">
    <span class="cls_011">VALORACIONS / OBSERVACIONS</span>
</div>
<div style="position:absolute;left:53.00px;top:261.60px" class="cls_012">
    <span class="cls_012">PUESTO FORMATIVO OCUPADO (1)</span>
</div>
<div style="position:absolute;left:209.60px;top:261.60px" class="cls_012">
    <span class="cls_012">FECHA DE DD/MM/AA A DD/MM/AA</span>
</div>
<div style="position:absolute;left:356.00px;top:261.60px" class="cls_012">
    <span class="cls_012">Nº. DE JORNADAS</span>
</div>
<div style="position:absolute;left:456.70px;top:261.60px" class="cls_012">
    <span class="cls_012">Nº. HORAS</span>
</div>
<div style="position:absolute;left:577.90px;top:261.60px" class="cls_012">
    <span class="cls_012">VALORACIONES / OBSERVACIONES</span>
</div>
<div style="position:absolute;left:39.10px;top:395.70px" class="cls_013">
    <span class="cls_013">(1) </span>
    <span class="cls_014">Detalle tots els llocs formatius en què ha estat l’alumne o l’alumna</span>
    <span class="cls_013">. / Detalle todos los puestos formativos en los que ha estado el alumno o la alumna.</span>
</div>
<div style="position:absolute;left:271.30px;top:436.00px" class="cls_013">
    <span class="cls_013">___________________, ________d __________________de _______________</span>
</div>
<div style="position:absolute;left:166.79px;top:454.40px;width: 300px" class="cls_014">
    <span class="cls_014">L'instructor o instructora</span>
</div>
<div style="position:absolute;left:570.10px;top:454.40px;width: 300px" class="cls_014">
    <span class="cls_014">V. i p. del tutor o tutora de FP Dual</span>
</div>
<div style="position:absolute;left:164.29px;top:463.60px;width: 300px" class="cls_013">
    <span class="cls_013">El instructor o instructora</span>
</div>
<div style="position:absolute;left:570.78px;top:463.60px;width: 300px" class="cls_013">
    <span class="cls_013">Vº Bº del tutor o tutora de FP Dual</span>
</div>
<div style="position:absolute;left:110.70px;top:537.20px" class="cls_013">
    <span class="cls_013">Firma:__________________________________________</span>
</div>
<div style="position:absolute;left:516.10px;top:537.20px" class="cls_013">
    <span class="cls_013">Firma:_________________________________________</span>
</div>
<div style="position:absolute;left:763.80px;top:567.90px" class="cls_009">
    <span class="cls_009">05/12/13</span>
</div>
