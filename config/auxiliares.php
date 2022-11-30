<?php
return [
    'diaSemana' => [ '1' => 'L','2' => 'M','3' => 'X', '4' => 'J','5' => 'V','6' => 'S','7' => 'D'],
    'estadoMaterial' => ['??','OK','Reparandose','Baja'],
    'actasEnabled' => ['Claustro'=>'Claustro','COCOPE'=>'COCOPE'],
    'procedenciaMaterial' => ['Desconocido','Dotación','Compra','Donación'],
    'idiomas' => ['es' => 'Español', 'ca' => 'Valencià' , 'en' => 'English'],
    'estadoIncidencia' => ['Rechazada','Pendiente','En proceso','Resuelta'],
    'estadoOrden' => ['Abierta','Cerrada','Resuelta'],
    'prioridadIncidencia' => ['Baja','Media','Alta'],
    'tipoIncidencia' => [ 1=> 'Material',2=>'Administrativa'],
    'tipoVehiculo' => ['Cotxe','Motocicleta','Avió','Tren','Taxi','Autobus','Altres'],
    'estadoDocumento' => ['Creado','Pendiente','Autorizado','Impreso'],
    'numeracion' => ['--','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15',
        30=>'AvaIni',31=>'1Ava',32=>'2Ava',33=>'3Ava',34=>'AvFinal',35=>'AvExtr',
        21=>'1er Trimestre',22=>'2on Trimestre',23=>'Final'],
    'nombreEval' => [1=>'Primera', 2=> 'Segona', 3=>'Final'],
    'asociacionEmpresa' => [1=>'FCT',2=>'FCT Convalidada/Exempció'],
    'grupoTutoria' => [0=>'Tots el grups',1=>'Grau mitjà',2=>'Grau Superior'],
    'motivoAusencia' => [
        'Baixa mèdica',
        'LLicència per formació',
        'Malaltia comú',
        'Trasllat de domicili',
        'Assistència proves sel·lectives',
        'Malaltia greu o defunció de familiar en primer grau',
        'Assistència mèdica, educativa o assistencial',
        "Altres (omplir cuadre d'observacions)"],
    'tipoEstudio' => [
        1=>'Cicle Formatiu de Grau Mitjà',
        '2'=>'Cicle Formatiu de Grau Superior',
        '3'=>'Cicle Formatiu Bàsic',
        '4'=>'Batxiller',
        '5'=>'ESO',
        '6'=>'Primària'],
    'tipoEstudioC' => [
        1=>'Ciclo Formativo de Grado Medio',
        '2'=>'Ciclo Formativo de Grado Superior',
        '3'=>'Ciclo Formativo Básico',
        '4'=>'Bachiller',
        '5'=>'ESO',
        '6'=>'Primaria'],
    'tipoTutoria' => [
        0=>'No assignada',
        1=>'Xarrades Programades',
        2=>'Convivència',
        3=>'Acadèmiques-Professionals',
        4=>'Temes transversals'],
    'veep' => [],
    'estadoColaboracion' => [ 0=>'' , 1=>'??', 2=>'Col·labora', 3=>'No col·labora'],
    'incidenciasColaboracion' => [
        1=>'Envia correu contacte',
        2=>'Envia confirmació de dades',
        3=>"Envia documentació d'inici",
        4=>"Denegació pràctiques",
        5=>'Comentari Professor',
        6=>'Comentari Instructor',
        7=>'Telefònic'
    ],
    'reunionesControlables' => [2=>1, 5=>1, 6=>0, 7=>4, 9=>1],
    'modelsAvailablePoll' => [1=>'--',
        'Profesor'=>'Professorat',
        'Actividad'=>'Activitats',
        'Fct'=>'Fct x Tutor',
        'AlumnoFct'=> 'Fct x Alumno'],
    'collectMailable' => ['InstructoresAll'=> 'Tots els instructors','AlumnosAll' => 'Alumnat'],

    'notas' => [
        0=>'No Avaluat',
        1=>'1',
        2=>'2',
        3=>'3',
        4=>'4',
        5=>'5',
        6=>'6',
        7=>'7',
        8=>'8',
        9=>'9',
        10=>'10',
        11=>'MH',
        12=>'Convalida',
        13=>'Aprovada amb anterioritat'
    ],
    'valoraciones' => [
        0=>'Ha seguit de manera satisfactòria el procés de formació online,
         realitzant les tasques sol·licitades en la seua majoria',
        1=>'El seguiment de la formació online ha estat suficient, realitzant algunes de les tasques sol·licitades',
        2=>'No ha seguit la formació online i no ha realitzat cap o gairebé cap de les tasques sol·licitades'],
    'capacitats' => [
        0 => "Ha assolit satisfactòriament les capacitats previstes en els objectius dels diferents mòduls.",
        1 => "Ha assolit suficientment les capacitats previstes en els objectius dels diferents mòduls.",
        2 => "No ha assolit el conjunt de capacitats previstes en alguns mòduls,
         però s'aprecia maduresa i possibilitats futures de progrés.",
        3 => "No ha assolit les capacitats previstes en els objectius dels diferents mòduls i
         s’hi constaten dificultats d'aprenentatge."
    ],
    'promociona' => [
        1 => "Promociona",
        3 => "No Promociona"
    ],
    'promocionaSemi' => [
        1 => "Només li queda la FCT",
        3 => "Encara no pot cursar la FCT"
    ],
    'periodesFct' => [1=>'Sept-Des',2=>'Abril-Juny',3=>'Altres',4=>'Flexible'],
    'estadosLote' => [0=>'BUIDA',1=>'ALTA',2=> 'INVENTARIANT',3=>'FINALITZADA']
];
