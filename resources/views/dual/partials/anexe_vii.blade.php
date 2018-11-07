<div style="position:absolute;left:50%;margin-left:-631px;top:{{$top}}px;width:1283px;height:882px;border-style:outset;overflow:visible">
    <div style="position:absolute;left:0px;top:0px"><img src="{{url($imagen)}}" width=1283 height=882></div>
    <div style="position:absolute;left:550.60px;top:11.70px;width: 300px" class="cls_002">
        <span class="cls_002">ANNEX VII / </span>
        <span class="cls_003">ANEXO VII</span>
    </div>
    <div style="position:absolute;left:509.90px;top:62.60px" class="cls_004">
        <span class="cls_004">INFORME INDIVIDUALITZAT DE L’INSTRUCTOR O INSTRUCTORA</span>
    </div>
    <div style="position:absolute;left:508.80px;top:79.20px" class="cls_005">
        <span class="cls_005">INFORME INDIVIDUALIZADO DEL INSTRUCTOR O INSTRUCTORA</span>
    </div>
    <div style="position:absolute;left:55.50px;top:127.10px" class="cls_006">
        <span class="cls_006">A</span>
    </div>
    <div style="position:absolute;left:88.18px;top:129.30px;width: 600px" class="cls_007">
        <span class="cls_007">DADES DE L’ALUMNE O ALUMNA / </span>
        <span class="cls_008">DATOS DEL ALUMNO O ALUMNA</span>
    </div>
    <div style="position:absolute;left:54.70px;top:157.70px" class="cls_009">
        <span class="cls_009">NIA</span><br/>
        <span class="cls_004"> {{$todos->Alumno->nia}}</span>
    </div>
    <div style="position:absolute;left:263.20px;top:157.70px" class="cls_009">
        <span class="cls_009">NOM</span>
        <span class="cls_010">/ NOMBRE</span><br/>
        <span class="cls_004"> {{$todos->Alumno->nombre}}</span>
    </div>
    <div style="position:absolute;left:550.90px;top:157.70px" class="cls_009">
        <span class="cls_009">COGNOMS</span>
        <span class="cls_010"> / APELLIDOS</span><br/>
        <span class="cls_004"> {{$todos->Alumno->apellido1}} {{$todos->Alumno->apellido2}}</span>
    </div>
    <div style="position:absolute;left:1030.60px;top:157.70px" class="cls_009">
        <span class="cls_009">NIF</span><br/>
        <span class="cls_004"> {{$todos->Alumno->dni}}</span>
    </div>
    <div style="position:absolute;left:54.70px;top:191.00px" class="cls_009">
        <span class="cls_009">CORREU ELECTRÒNIC</span>
        <span class="cls_010"> / CORREO ELECTRÓNICO</span><br/>
        <span class="cls_004"> {{$todos->Alumno->email}}</span>
    </div>
    <div style="position:absolute;left:687.80px;top:191.00px" class="cls_009">
        <span class="cls_009">DATA DE NAIXEMENT </span>
        <span class="cls_010">/ FECHA DE NACIMIENTO</span><br/>
        <span class="cls_004"> {{$todos->Alumno->fecha_nac}}</span>
    </div>
    <div style="position:absolute;left:54.70px;top:227.70px" class="cls_009">
        <span class="cls_009">FAMÍLIA PROFESSIONAL / </span>
        <span class="cls_010">FAMILIA PROFESIONAL</span><br/>
        <span class="cls_004"> {{$todos->Dual->Colaboracion->Ciclo->Departament->literal}}</span>
    </div>
    <div style="position:absolute;left:687.80px;top:227.70px;width: 300px" class="cls_009">
        <span class="cls_009">CICLE FORMATIU / </span>
        <span class="cls_010">CICLO FORMATIVO</span><br/>
        <span class="cls_004">{{$todos->Dual->Colaboracion->Ciclo->ciclo}}</span>
    </div>
    <div style="position:absolute;left:54.70px;top:260.40px" class="cls_009">
        <span class="cls_009">DENOMINACIÓ DEL CENTRE EDUCATIU</span>
        <span class="cls_010"> / DENOMINACIÓN DEL CENTRO EDUCATIVO</span><br/>
        <span class="cls_004">{{$datosInforme['centro']}}</span>
    </div>
    <div style="position:absolute;left:480.30px;top:260.40px;width:300px;" class="cls_009">
        <span class="cls_009">CODI CENTRE </span>
        <span class="cls_010">/ CÓDIGO CENTRO</span><br/>
        <span class="cls_004"> {{$datosInforme['codigo']}}</span>
    </div>
    <div style="position:absolute;left:687.80px;top:260.40px" class="cls_009">
        <span class="cls_009">TUTOR O TUTORA  FP DUAL</span><br/>
        <span class="cls_004"> {{AuthUser()->FullName}}</span>
    </div>
    <div style="position:absolute;left:54.70px;top:296.10px" class="cls_009">
        <span class="cls_009">EMPRESA</span><br/>
        <span class="cls_004"> {{$todos->Dual->Colaboracion->Centro->nombre}}</span>
    </div>
    <div style="position:absolute;left:549.90px;top:296.10px" class="cls_009">
        <span class="cls_009">INSTRUCTOR O INSTRUCTORA</span><br/>
        <span class="cls_004">@if ($todos->Dual->Instructor) {{$todos->Dual->Instructor->Nombre }} @endif</span>
    </div>
    <div style="position:absolute;left:1030.60px;top:296.10px" class="cls_009">
        <span class="cls_009">NIF</span><br/>
        <span class="cls_004">@if ($todos->Dual->Instructor) {{$todos->Dual->Instructor->dni }} @endif</span>
    </div>
    <div style="position:absolute;left:54.70px;top:338.20px" class="cls_006">
        <span class="cls_006">B</span>
    </div>
    <div style="position:absolute;left:88.18px;top:338.20px;width: 500px" class="cls_007">
        <span class="cls_007">INFORME</span>
        <span class="cls_008"> / INFORME</span>
    </div>
    <div style="position:absolute;left:91.60px;top:375.40px" class="cls_011">
        <span class="cls_011">LLOC FORMATIU OCUPAT</span>
    </div>
    <div style="position:absolute;left:323.30px;top:375.40px" class="cls_011">
        <span class="cls_011">DATA DE DD/MM/AA A DD/MM/AA</span>
    </div>
    <div style="position:absolute;left:544.20px;top:375.40px;width: 150px" class="cls_011">
        <span class="cls_011">Nre. DE JORNADES</span>
    </div>
    <div style="position:absolute;left:675.00px;top:375.40px;width: 150px" class="cls_011">
        <span class="cls_011">Nre. HORES</span>
    </div>
    <div style="position:absolute;left:893.60px;top:375.40px;width:400px" class="cls_011">
        <span class="cls_011">VALORACIONS / OBSERVACIONS</span>
    </div>
    <div style="position:absolute;left:81.60px;top:389.60px" class="cls_012">
        <span class="cls_012">PUESTO FORMATIVO OCUPADO (1)</span>
    </div>
    <div style="position:absolute;left:315.30px;top:389.60px" class="cls_012">
        <span class="cls_012">FECHA DE DD/MM/AA A DD/MM/AA</span>
    </div>
    <div style="position:absolute;left:544.00px;top:389.60px" class="cls_012">
        <span class="cls_012">Nº. DE JORNADAS</span>
    </div>
    <div style="position:absolute;left:675.70px;top:389.60px" class="cls_012">
        <span class="cls_012">Nº. HORAS</span>
    </div>
    <div style="position:absolute;left:883.90px;top:389.60px;width:400px" class="cls_012">
        <span class="cls_012">VALORACIONES / OBSERVACIONES</span>
    </div>
    <div style="position:absolute;left:69.10px;top:582.70px;width:900px" class="cls_013">
        <span class="cls_013">(1) </span>
        <span class="cls_014">Detalle tots els llocs formatius en què ha estat l’alumne o l’alumna</span>
        <span class="cls_013">. / Detalle todos los puestos formativos en los que ha estado el alumno o la alumna.</span>
    </div>
    <div style="position:absolute;left:431.30px;top:620.00px" class="cls_013">
        <span class="cls_013">___________________, ________d __________________de _______________</span>
    </div>
    <div style="position:absolute;left:206.79px;top:650.40px;width: 300px" class="cls_014">
        <span class="cls_014">L'instructor o instructora</span>
    </div>
    <div style="position:absolute;left:870.10px;top:650.40px;width: 300px" class="cls_014">
        <span class="cls_014">V. i p. del tutor o tutora de FP Dual</span>
    </div>
    <div style="position:absolute;left:204.29px;top:665.60px;width: 300px" class="cls_013">
        <span class="cls_013">El instructor o instructora</span>
    </div>
    <div style="position:absolute;left:870.78px;top:665.60px;width: 300px" class="cls_013">
        <span class="cls_013">Vº Bº del tutor o tutora de FP Dual</span>
    </div>
    <div style="position:absolute;left:180.70px;top:774.20px" class="cls_013">
        <span class="cls_004">@if ($todos->Dual->Instructor) {{$todos->Dual->Instructor->Nombre }} @endif</span><br/>
        
    </div>
    <div style="position:absolute;left:130.70px;top:784.20px" class="cls_013">
        
        <span class="cls_013">Firma:__________________________________________</span>
    </div>
    <div style="position:absolute;left:850.70px;top:774.20px" class="cls_013">
        <span class="cls_004"> {{AuthUser()->FullName}}</span><br/>
        
    </div>
    <div style="position:absolute;left:800.10px;top:784.20px" class="cls_013">
        <span class="cls_013">Firma:_________________________________________</span>
    </div>
    <div style="position:absolute;left:1163.80px;top:842.90px" class="cls_009">
        <span class="cls_009">05/12/13</span>
    </div>
</div>
