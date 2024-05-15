<?php

return [
    'tipoReunion' => [
        ['index' => '0',
            'vliteral' => 'Reunió Departament',
            'cliteral' => 'Reunión Departamento',
            'colectivo' => 'Departamento',
            'rol' => '13', 'select' => '0',
            'convocatoria' => 'convocatoria',
            'acta' => 'actaReunio',
            'modificable' => 1,
            'ordenes' => ['Lectura acta anterior', "Torn obert de paraula"],
            'numeracion' => [0=>'--',1=>'01',2=>'02',3=>'03',4=>'04',5=>'05',6=>'06',7=>'07',8=>'08',9=>'09',10=>'10',11=>'11',12=>'12',13=>'13',14=>'14',15=>'15',
                16=>'16',17=>'17',18=>'18',19=>'19',24=>'20',25=>'21',26=>'22',27=>'23',28=>'24',29=>'25',40=>'26',41=>'27',42=>'28',43=>'29',44=>'30',45=>'31',46=>'32',47=>'33',48=>'34',49=>'35']],
        ['index' => '1', 'vliteral' => 'Claustre', 'cliteral' => 'Claustro', 'colectivo' => 'Profesor', 'rol' => '2', 'select' => '0',
            'convocatoria' => 'convocatoria', 'acta' => 'actaReunio',
            'modificable' => 1,
            'ordenes' => ['Lectura acta anterior', "Informe direcció", "Informe Caporalia", "Torn obert de paraula"],
            'numeracion' => [0=>'--',1=>'01',2=>'02',3=>'03',4=>'04',5=>'05',6=>'06',7=>'07',8=>'08',9=>'09']],
        ['index' => '2', 'vliteral' => 'Reunió Equip Educatiu', 'cliteral' => 'Reunión Equipo Educativo', 'colectivo' => 'Grupo', 'rol' => '17', 'select' => '0',
            'convocatoria' => 'convocatoria', 'acta' => 'actaReunio',
            'modificable' => 1,
            'ordenes' => ["Revisió d'acords adoptats a la sessió anterior", "Opinió i/o comentaris dels alumnes", "Problemes detectats al grup i mesures a prendre", "Alumnes amb dificultats acadèmiques i mesures a adoptar", "Acords adoptats", "Observacions"],
            'numeracion' => [0=>'--',1=>'01',2=>'02',3=>'03',4=>'04',5=>'05',6=>'06',7=>'07',8=>'08',9=>'09']],
        ['index' => '3', 'vliteral' => 'Reunió Grup de treball', 'cliteral' => 'Reunión Grupo de trabajo', 'colectivo' => 'GrupoTrabajo', 'rol' => '3', 'select' => '1',
            'convocatoria' => 'convocatoria', 'acta' => 'actaReunio',
            'modificable' => 1,
            'ordenes' => ["Revisió d'acords adoptats a la sessió anterior", "Acords adoptats", "Observacions"],
            'numeracion' => [0=>'--',1=>'01',2=>'02',3=>'03',4=>'04',5=>'05',6=>'06',7=>'07',8=>'08',9=>'09',10=>'10',11=>'11',12=>'12',13=>'13',14=>'14',15=>'15',
                16=>'16',17=>'17',18=>'18',19=>'19']],
        ['index' => '4', 'vliteral' => 'Tutoria', 'cliteral' => 'Tutoría', 'colectivo' => 'Grupo', 'rol' => '17', 'select' => '0',
            'convocatoria' => 'convocatoria', 'acta' => 'actaReunio',
            'modificable' => 1,
            'numeracion' => [0=>'--',1=>'01',2=>'02',3=>'03',4=>'04',5=>'05',6=>'06',7=>'07',8=>'08',9=>'09']],
        ['index' => '5', 'vliteral' => 'Elecció delegat', 'cliteral' => 'Elección de delegado', 'colectivo' => 'Grupo', 'rol' => '17', 'select' => '0',
            'convocatoria' => 'delegado', 'acta' => 'actaDelegat',
            'modificable' => 1,
            'ordenes' => ["Nº Alumnes", "Nº Votants", "Candidats", "Vots", "Delegat", "Subdelegat", "Secretari", "Vocal"],
            'numeracion' => [0=>'--']],
        ['index' => '6', 'vliteral' => 'Reunió pares', 'cliteral' => 'Reunión de padres', 'colectivo' => 'Grupo', 'rol' => '17', 'select' => '0','modificable' => 1,
            'convocatoria' => 'citacion', 'acta' => 'actaReunio','numeracion' => [0=>'--']],
        ['index' => '7', 'vliteral' => 'Reunió Avaluació', 'cliteral' => 'Reunión de evaluación', 'colectivo' => 'Grupo', 'rol' => '17', 'select' => '0',
            'convocatoria' => 'convocatoria', 'acta' => 'actaAvaluacio','modificable' => 0,
            'ordenes' => ["Opinió dels alumnes", "Revisió d'acords adoptats a la sessió anterior", "Revisió de l'acta de qualificacions",
                "Valoració general dels resultats obtinguts", "Problemes detectats al grup i mesures a pendre", "Alumnes amb dificultats acadèmiques i mesures a adoptar",
                "Acords adoptats", "Observacions"],
            'numeracion' => [30=>'AvaIni',31=>'1Ava',32=>'2Ava',33=>'3Ava',34=>'AvFinal',35=>'AvExtr']],
        ['index' => '8', 'vliteral' => 'COCOPE', 'cliteral' => 'COCOPE', 'colectivo' => 'Jefe', 'rol' => '2', 'select' => '0',
            'convocatoria' => 'convocatoria', 'acta' => 'actaReunio','modificable' => 1,
            'ordenes' => ['Lectura acta anterior', "Informe direcció", "Informe Caporalia", "Torn obert de paraula"],
            'numeracion' => [0=>'--',1=>'01',2=>'02',3=>'03',4=>'04',5=>'05',6=>'06',7=>'07',8=>'08',9=>'09']],
        ['index' => '9', 'vliteral' => 'FSE', 'cliteral' => 'FSE', 'colectivo' => 'Grupo', 'rol' => '17', 'select' => '0',
            'convocatoria' => 'convocatoria', 'acta' => 'actaFSE','modificable' => 0,
            'ordenes' => [],
            'numeracion' => [0=>'--']],
        ['index' => '10', 'vliteral' => 'Memòria del departament', 'cliteral' => 'Memoria Trimestral del departament', 'colectivo' => 'Departamento', 'rol' => '13', 'select' => '0','modificable' => 1,
            'numeracion' => [21=>'1er Trimestre',22=>'2on Trimestre',23=>'Final'],
            'ocultar' => true],
        ['index' => '11', 'vliteral' => 'Acta de la reunió de valoració de les propostes de projectes', 'cliteral' => 'Acta aceptación proyectos', 'colectivo' => 'Grupo', 'rol' => '17','select' => '0','modificable' => 1,
            'convocatoria' => 'convocatoria', 'acta' => 'actaProyecto',
            'numeracion' => [0=>'--'],
            'ordenes' => ['Alumno->misAlumnos->FullName'],
            'resumen' => 'Títol i Tutor individual '],
        ['index' => '12', 'vliteral' => 'Data Defensa del mòdul de projecte', 'cliteral' => 'Fecha Defensa del módulo de proyecto', 'colectivo' => 'Grupo', 'rol' => '17','select' => '0','modificable' => 1,
            'convocatoria' => 'convocatoria', 'acta' => 'actaDefensa',
            'numeracion' => [0=>'--'],
            'ordenes' => ['AlumnoFct->misProyectos->FullName'],
            'resumen' => '(Projecte) Data i Hora '],
    ],
    'tipoDocumento' => [
        ['index'=>'Profesor','rol'=>3,'pestana'=>1,'grupo'=>'1'],
        ['index'=>'Tutor','rol'=>17,'pestana'=>1,'grupo'=>'1'],
        ['index'=>'Alumno','rol'=>5,'pestana'=>1,'grupo'=>'1'],
        ['index'=>'Jefe','rol'=>13,'pestana'=>1,'grupo'=>'1'],
        ['index'=>'Consell','rol'=>3,'pestana'=>1,'grupo'=>'2'],
        ['index'=>'PGA','rol'=>1,'pestana'=>1,'grupo'=>'2'],
        ['index'=>'RRI','rol'=>1,'pestana'=>1,'grupo'=>'2'],
        ['index'=>'Emer','rol'=>1,'pestana'=>1,'grupo'=>'2'],
        ['index'=>'Sanit','rol'=>1,'pestana'=>1,'grupo'=>'2'],
        ['index'=>'Pla','rol'=>1,'pestana'=>1,'grupo'=>'2'],
        ['index'=>'Millora','rol'=>1,'pestana'=>1,'grupo'=>'2'],
        ['index'=>'Acta','rol'=>3,'pestana'=>1,'grupo'=>'3'],
        ['index'=>'Fichero','rol'=>2 ,'pestana'=>'0','grupo'=>'0' ],
        ['index'=>'Proyecto','rol'=>1,'pestana'=>0,'grupo'=>'0'],
        ['index'=>'Qualitat','rol'=>1,'pestana'=>0,'grupo'=>'0'],
        ['index'=>'GestioEconomica','rol'=>1,'pestana'=>1,'grupo'=>'2'],
    ],
];
