<?php
return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used
	| by the validator class. Some of the rules contain multiple versions,
	| such as the size (max, min, between) rules. These versions are used
	| for different input types such as strings and files.
	|
	| These language lines may be easily changed to provide custom error
	| messages in your application. Error messages for custom validation
	| rules may also be added to this file.
	|
	*/

	
    'Alumno' => array(
          'show' => "Dades de l'alumna",
          'edit' => "Modificació del perfil de l'Alumna",

        ),
	'Comision' => array(
		'create'=>'Sol·licitud autorització comissió Servei',
                'index'=>'Gestionar comissions Servei',
                'edit'=> 'Modificació comissió de servei',
                'alta'=> 'Alta Comissió de Servei',
                'autorizar' => 'Autoritzar comissions pendents',
                'pdf' => 'Imprimir Comissions autoritzades',
                'delete' => 'Esborrar Comissió',
                'notification' => 'Avisar equip educatiu',
                'email' => 'Enviar para autoritzar',
                'show' => 'Mostra Comissió id.',
                '3' => 'Registrada',
                '4' => 'Pendent Cobrament',
                '2' => 'Autoritzada',
                '1' => 'Enviada',
                '-1' => 'Anul·lada',
                '5' => 'Cobrada',
                '0' => 'No autoritzada/comunicada',
                'unpaid' => 'Cobrar',
                'paid' => 'Imprimir pagaments'
	     ),
        'Falta' => array(
		'create'=>'Comunicació de Ausència Professorat',
                'index'=>'Gestionar Baixes',
                'edit'=> 'Modificació Ausència',
                'alta' => 'Alta Ausència',
                'resolve'=> 'Autoritzar Baixes',
                'pdf' => 'Imprimir Baixes pendents',
                'notification' => 'Avisar Equip',
                'email' => 'Comunicar a direcció',
                'show' => 'Veure Absència id.',
                'imprime' => 'Imprimir informe mensual',
                '3' => 'Autoritzada',
                '4' => 'Resolta',
                '2' => 'Justificada',
                '1' => 'Sense justificant',
                '5' => 'Llarga Durada',
                '6' => 'Itaca',
                '0' => 'No enviada/autoritzada'
                
             ),
        'Profesor' => array(
                'edit' => 'Modificar Perfil Professorat',
                'show' => 'Dades Professorat',
                'index' => 'LListat Professorat',
                'list' => 'Professorat Absents',
                'horario-cambiar' => 'Canviar horari Professorat',
                'titulo' => 'Documentació Addicional Pràctiques de :quien',
                'files' => "Fitxers de l'usuari",
        ),
        'Menu' => array(
                'index' => 'Elements del menú',
                'create' => 'Crear entrada',
                'edit' => 'Editar element del menú'
        ),
        'Alumnogrupo' => array(
            'index' => 'Gestionar Grup :quien',
            'carnet' => 'Imprimeix Carnets de Grup',
            'profile' => 'Plantilla Alumnat',
            'show' => 'Llista Alumnat',
            'edit' => 'Editar Perfil Alumnat',
        ),
        'Inventario' => array(
            'espacios' => 'Vore Espais',
            'materiales' => 'Vore Materials',
            'index' => 'Gestionar Inventari',
        ),
        'Materialbaja' => array(
            'index' => 'Gestionar Moviments',
        ),
        'Alumno_curso' => array(
            'index' => 'Alumnat Curs',
        ),
        'Incidencia' => array(
            'index' => 'Gestionar incidències',
            'create' => 'Incidència',
            '3' => 'Resolta',
            '2' => 'En procés',
            '1' => 'Assignada',
            '0' => 'No comunicada'
        ),
        'Actividad' => array(
            'index' => 'Gestionar Activitats extraescolars',
            'create' => 'Alta activitat',
            'createO' => 'Alta activitat Orientació',
            'autorizacion' => 'Imprimir Autorizatció menors',
            'autorize' => "Control Autoritzacions",
            'valoracion' => 'Imprimir Valoració',
            'edit' => 'Modificar Activitat extraescolar',
            'detalle' => 'Detall activitat',
            'titulo' => 'Detall activitat :actividad',
            'profesores' => 'Professorat participant',
            'grupos' => 'Grups participants',
            'email' => 'Enviar per autoritzar',
            'value' => 'Valorar Activitat',
            'pdf' => 'Imprimir Activitats Autoritzades',
            'delete' => 'Esborrar Activitat',
            'autorizar' => 'Autoritzar totes activitats',
            'notification' => 'Avisar professorat',
            'pdfVal' => 'Imprimir Valoració',
            'showVal' => 'Mostrar valoració',
            '2' => 'Autoritzada',
            '3' => 'Impressa',
            '4' => 'Valorada',
            '1' => 'Pendent',
            '5' => 'Itaca',
            '0' => 'Rebutjada/No comunicada'
        ),
        'TipoIncidencia' => array(
            'index' => 'Gestionar Incidències',
            'create' => 'Crear Incidència',
            'email' => 'Passar a processament',
            'authorize' => 'Tractar Incidència',
            'unauthorize' => 'Abandonar Incidència',
            'resolve' => 'Resoldre Incidència',
            'edit' => 'Modificar Incidència',
            'notification' => 'Avisa Responsable',
            '3' => 'Resolta',
            '2' => 'En procés',
            '1' => 'Assignada',
            '0' => 'No comunicada'
        ),
        'Material' => array(
            'index' => 'Gestionar Materials',
            'create' => 'Crear Material',
            'delete' => 'Esborrar Material',
            'edit' => 'Editar Material',
            'list' => 'Llistat Inventari'
        ),
        'Espacio' => array(
            'index' => 'Gestionar Espais',
            'create' => 'Crear Espai',
            'delete' => 'Esborrar Espai',
            'edit' => 'Editar Espai',
            'detalle' => 'Vore Materials'
        ),
    
        'Grupo' => array(
            'index' => 'Grups',
            'detalle' => 'Vore alumnat',
            'edit' => 'Editar Grup',
            'pdf' => 'Imprimir Full de fotos',
            'asigna' => 'Asigna cicle Automàticament',
        ),
        'Curso' => array(
            'create' => 'Nou Curs/Ponència',
            'index' => 'Gestionar Cursos/Ponències',
            'edit' => 'Modifica Curso/Ponència',
        ),
        'Notification' => array(
            'index' => 'Notificacions',
            'read' => 'Marca com a llegida',
            'show' => 'Detalls Notificació',
        ),
        'Programacion' => array(
            'item0' => 'Propostes de millora',
            'item1' => 'Secuenciació',
            'item2' => 'Continguts',
            'item3' => 'Metodologia',
            'item4' => 'Avaluació.Criteris de qualificació',
            'item5' => 'Programa de recuperació',
            'item6' => 'Recursos',
            'index' => 'Manteniment Programacions',
            'create' => 'Nova Programació',
            'checklist' => "Confirmar Requeriments",
            'save' => 'Guardar requeriments',
            'edit' => 'Editar programació',
            'resolve' => 'Aprovar',
            'email' => 'Enviar per revisar',
            'show' => 'Mostrar Programació id.',
            'seguimiento' => 'Omplir seguiment de la programació',
            'link' => 'Vore programació',
            '2' => 'Comprobada i compleix els requeriments.',
            '3' => 'Aprobada pel departament',
            '1' => 'Pendent de revisar',
            '0' => 'Rebutjada. Li manquen els següents requeriments: '
        ),
        'Guardia' =>  array(
            'edit' => 'Guàrdia a realitzar',
            'create' => 'Dades de la guàrdia',
            'control' => 'Control de les guàrdies'
        ),
        'Modulo' => array(
            'asigna' => 'Assigna Cicle Automàticament',
            'index' => 'Mantenimient mòduls',
            'edit' => 'Modifica mòdul',
            'list' => 'Estat Programacions'
        ),
        'Expediente' => array(
            'show' => 'Mostra expedient id.',
            'index' => 'Gestió de expedients',
            'link' => 'Adjuntar fitxers',
            'create' => 'Nou expedient',
            'edit' => 'Editar expedient',
            'pdf' => 'Imprimir Tramitats',
            'authorize' => 'Tramitar',
            'unauthorize' => 'No tramitar',
            'autorizar' => 'Tramitar tots els expedients',
            'titulo' => 'Fitxer adjunts expediente :quien',
            '2' => 'En tramit',
            '3' => 'Resolts',
            '1' => 'Pendent de revisar',
            '0' => 'No comunicat/Rebutjat',
            '4' => 'Comunicada',
            '5' => 'En Tractament',
        ),
        'Reunion'=> array(
            'show' => 'Mostra reunió id.',
            'index' => 'Gestió de reunions',
            'create' => 'Nova reunió',
            'edit' => 'Editar ',
            'pdf' => 'Imprimir',
            'email' => 'Enviar convocatòria/acta',
            'notification' => 'Avisar participants',
            'titulo' => 'Participants reunió',
            'participantes' => 'Assistents',
            'ordenes' => 'Ordre del dia',
            'detalle' => 'Dades reunió',
            'saveFile' => 'Arxivar',
            'deleteFile' => 'Esborrar Acta Arxivada',
            'control' => 'Control Reunions per grup',
            'alumnos' => 'Avaluació individual'
        ),
        'Grupotrabajo'=> array(
            'show' => 'Mostra grup de treball id.',
            'index' => 'Gestió de Grups',
            'create' => 'Nou Grup',
            'edit' => 'Editar Grup',
            'participantes' => 'Membres'
        ),
        'Resultado' => array(
            'index' => "Gestiona els teus resultats d'avaluació",
            'create' => 'Crea resultats per un grup',
            'edit' => 'Edita resultats',
            'delete' => 'Esborra resultats',
            'informe' => 'Gestionar Informes de Departament',
            'llenar' => 'Omplir dades complementàries',
            'faltan' => "Per generar l'informe falten els resultats dels següents mòduls",
            'avisa' => 'Avisa Professors',
            'estan' => 'Tens tots els resultats del departament disponibles',
            'generado' => "L'informe del departament està disponible",
        ),
        'AlumnoResultado' => array(
            'index' => 'Inserir qualificacions del modul/grup :quien',
            'create' => 'Afegir Qualificació'
        ),
        'Documento' => array(
            'index' => 'Cercar Documents',
            'create' => 'Crear Document',
            'edit' => 'Modifica Document',
            'default' => 'Modifica Documentació Qualitat',
        ),
        'Projecte' => array(
            'create' => 'Pujar projecte alumne/a',
            'index' => 'Gestió de Projectes',
         ),
        'Empresa' => array(
            'index' => 'Llistat Empreses',
            'create' => 'Crear empresa',
            'edit' => 'Editar empresa',
            'delete' => 'Esborrar empresa',
            'detalle' => 'Editar centre de treball',
            'anexo' => 'Editar col.laboracions'
        ),
        'Centro' => array(
            'index' => 'Centre de treball de :quien',
            'edit' => 'Editar centre',
            'delete' => 'Esborrar centre',
            'copy' => 'Crear centre'
        ),
        'Colaboracion' => array(
            'index' => 'Colaboracions de treball de :quien',
            'edit' => 'Editar colaboració',
            'delete' => 'Esborrar colaboració',
            'copy' => 'Crear colaboració',
            'inicia' => 'Reinicia cicle de contactes',
            'resolve' => 'SI',
            'unauthorize' => '??',
            'refuse' => 'NO',
            'contacto' => 'Sol·licitud Pràctiques',
            'switch' => 'Assignar',
            'contactos' => "Contactes amb l'empresa",
            'fctAl' => "Contactes amb l'alumnat",
            'fct' => 'FCTs',
            'revision' => 'Revisió Documentació',
            'inicioEmpresa' => 'Recordatori inici',
            'inicioAlumno' => 'Documentació Alumnat',
            'seguimiento' => "Seguiment",
            'visitaEmpresa'=> "Concertar visita",
            'citarAlumnos' => 'Citar Alumnat',
            'centro' => 'Centre',
            'colaboradores' => 'Col·laboradors',
        ),
        'Fct' => array(
            'index' => 'Contactes',
            'create' => 'Nova Fct',
            'edit' => 'Canviar instructor',
            'show' => 'Detalls Fct',
            'delete' => 'Esborrar Fct',
            'fin' => 'Finalitzar Fct',
            'pdf' => 'Imprimir certificats Instructors',
            'colaboradorPdf' => 'Imprimir Certificat Col·laboradors',
            'mail' => 'Enviar email valoracio',
            'apte' => 'Qualificar Apto',
            'noApte' => 'Qualificar No Apte',
            'noAval' => 'No Avaluat',
            'noProyecto' => 'No presenta Projecte',
            'proyecto' => 'Pujar projecte',
            'nuevoProyecto' => 'Accedeix al Projecte',
            'nullProyecto' => 'Esborra projecte',
            'upload' => 'Zip Informes',
            'dropzone' => 'Guardar Informes',
            'pg0301' => 'Full Control Servei',
            'pr0402' => 'Entrevista Alumnat',
            'pr0401' => 'Entrevista Final Instructora',
            'autTutor' => 'Autorització Tutor Situació Excepcional',
            'autDireccio' => 'Autorització Direcció Situació Excepcional',
            'autAlumnat' => 'Conformitat Alumnat Situació Excepcional',
            'print' => 'Informes',
            'all' => 'Vore tots',
            'only' => 'Vore actius',
            'list' => 'Acta Grupo :quien',
            'acta' => 'Demanar acta d\'avaluació',
            'insercio' => "Inserció laboral",
            'alumno' => 'x Alumnat',
            'empresa' => 'x Empresa',
            'send' => 'Enviar Correu Alumnat Inici',
            'link' => 'Adjuntar fitxers',
            'default' => 'x Empresa',
            'an1' => 'Imprimir annexes I signats',
            'an2' => 'Imprimir annexes II signats',
            'an3' => 'Imprimir annexes III signats',
            'an5' => 'Imprimir informe competències adquirides',
        ),
        'Dual' => array(
            'index' => 'FP Dual',
            'create' => 'Nova Dual',
            'edit' => 'Editar Dual',
            'delete' => 'Esborrar Dual',
            'fin' => 'Finalitzar Dual',
            'anexe_vii' => 'Imprimir Annexe VII',
            'anexe_va' => 'Imprimier Annexe V(a)',
            'anexe_vb' => 'Imprimier Annexe V(b)',
            'anexeVI' => 'Imprimir Annexe VI',
            'anexeXIV' => 'Imprimir Annexe XIV',
            'anexeXIII' => 'Imprimir AnnexeXIII',
            'firma' => 'Generar ZIP firma'
        ),
        'Direccion' => array(
           'acta' => 'Acta completada',
            'reject' => 'Rebutjar Acta',
        ),
        'Falta_profesor' => array(
            'index' => 'Panell Fitxaje'
        ),
        'Tutoria' => array(
            'index' => 'Índex de Tutories :que',
            'edit' => 'Editar tutoria',
            'delete' => 'Esborrar tutoria',
            'document' => 'Vore fitxer',
            'detalle' => 'Vore comentaris',
            'create' => 'Crear tutoria',
            'anexo' => 'Crear comentari',
            
        ),
        'GrupoTrabajo' => array(
            'index' => 'Grups treball',
            'create' => 'Crear grup treball',
        ),
        'Tutoriagrupo' => array(
            'index' => 'Llistat feedbacks :que',
            'edit' => 'Editar FeedBack tutoria',
            'create' => 'Crear FeedBack tutoria',
            'list' => 'Llistat feedbacks :que'
        ),
        'Ordentrabajo' => array(
            'index' => 'Gestionar Ordres de Treball',
            'edit' => 'Editar Ordre',
            'delete' => 'Esborrar Ordre',
            'anexo' => 'Vore Incidències',
            'pdf' => 'Llistar ordre',
            'open' => 'Obrir ordre a més incidències',
            'resolve' => 'Finalitzar ordre'
        ),
        'Ciclo' => array(
            'index' => 'Manteniment cicles',
            'create' => 'Nou cicle',
            'edit' => 'Editar cicle',
            'delete' => 'Esborrar cicle'
        ),
        'Falta_itaca' => array(
            'index' => 'No marcatge Birret',
            'edit' => 'Editar Birret',
            'resolve' => 'Justificar',
            '0' => 'No comunicada',
            '3' => 'Rebutjada',
            '1' => 'Pendent',
            '2' => 'Justificada',
            '4' => 'Itaca',
        ),
        'Horario' => array(
            'index' => 'Modificar Horari :quien',
            'edit' => 'Edita Horari',
            'cambiar' => "Canviar funcions horari",
        ),
        'Instructor' => array(
          'index' => 'Consulta de instructores',
           'edit' => 'Editar instructora',
           'create' => 'Crear instructora',
           'copy' => 'Copiar instructora'
        ),
        'Infdepartamento' => array(
          'index' => 'Consulta informes de departament',
          'create' => 'Crear Informe departament',
          'edit' => 'Modificar informe departament',
          'avisa' => 'Avisa Professorat falta informe',
          'pdf' => 'Vore Informe departament'
            
        ),
        'Modulo_grupo' => array(
          'index' => 'Llistat de mòduls',
        ),
        'Alumnofct' => array(
          'index' => 'Fct x Alumne/a',
          'convalidacion' => 'FCT Convalidada/Exempt',
          'edit' => 'Modificar Dades Fct',
          'pdf' => 'Imprimir certificat Alumne/a',
          'auth' => 'Imprimir autoritzacions',
          'email' => 'Enviar avis emplenar diari',
          'pg0301' => "Entregada documentació",
            'default' => 'x Alumne/a',
            'selecciona' => 'Alumne/a',
            'A5' => 'Informe Competències adquirides',
            'delete' => 'Esborra fct',
            'unlink' => 'Esborra connexiò amb el SAO',
            'importa' => 'Importa annexes dual any anterior'

        ),
        'AlumnoFct' => array(
            'selecciona' => 'Alumne/a',
            'create' => 'Nova Exempció',
        ),
        'Alumnofctaval' => array(
            'index' => 'Avaluació FCT i projecte',
            'list' => "Llistat pendents d'avaluació grup :quien",
            'titulo' => "Annexes FCT/DUAL de :quien"
        ),
        'Fctcap' => array(
          'index' => 'Control Fct :quien',
           'check' => 'Control documentació'
          
        ),
        'Fctdual' => array(
            'index' => 'Control Dual :quien',
            'check' => 'Control documentació'
        ),
        'fctDay' => array(
            'show' => 'Calendari pràctiques :quien',

        ),
        'Ipguardia' => array(
          'index' => 'Control IP',
        ),
        'Setting' => array(
          'index' => 'Variables de Configuració',
          'edit' => 'Editar Configuració',
          'create' => 'Crear Configuració',
        ),
        'Ppoll' => array(
          'show' => 'Plantilla Enquesta',
          'index' => 'Manteniment Plantilles Enquestes',
          'edit' => 'Editar Plantilla',
          'slave' => 'Vore preguntes',
        ),
        'Poll' => array(
            'show' => 'Mostra resultats',
            'index' => 'Manteniment Enquestes',
            'edit' => 'Editar Enquesta',
            'chart' => 'Vore resultats agregats',
            'do' => 'Contesta',
        ),
        'Modulo_ciclo' => array(
            'index' => 'Programacions'
        ),
        'Lote' => array(
            'show' => 'Factura :quien',
            'index' => 'Llibre de Factures',
            'edit' => 'Editar Factura',
            'create' => 'Nova Factura',
        ),
        'Articulolote' => array(
            'index' => 'Articles pendents de ubicar',
            'show' => 'Vore Materials'
        ),
        'Articulo' => array(
            'index' => 'Mostra Articles',
            'edit' => 'Editar Article',
            'show' => 'Mostrar Article',
            'delete' => 'Esborrar Article',
            'create' => 'Crear Article',
        ),
        'Sao' => array(
            'post' => 'Connexió SAO',
        ),
        'Signatura' => array(
            'post' => 'Signatura Annexes',
            'index' => 'Llistat signatures pendents',
            'show' => 'Vore Estat Signatures',
            'pdf' => 'Imprimir Fitxer',
            'delete' => 'Esborrar fitxer',
            'send' => "Enviar a l'instructor"
        ),
        'Solicitud' => array(
            'index' => "Derivacions al departament d'Orientació",
            'create' => "Qüestionari de derivació",
            'edit' => 'Modificar qüestionari',
            'idAlumno' => 'Alumne',
            'text1' => 'Motiu de la sol·licitud',
            'text2' => 'Aspectes afectats per la situació: (Curriculars, emocionals,personals,socials...)',
            'text3' => "Altres dades i/o informació d'interés:",
            'idOrientador' => "Orientador",
            '1' => 'Comunicada',
            '2' => 'En proces',
            '3' => 'Resolta',

        ),
        'Cotxe' => array(
            'index' => 'Manteniment de Vehicles',
            'create' => 'Crear Vehicle',
        ),

        'modelos' => array(
            'Comision' => 'Comissió de Servei',
            'Curso' => 'Curs',
            'Grupo' => 'Grup',
            'Falta' => 'Falta',
            'Actividad' => 'Activitat Extraescolar',
            'Profesor' => 'Professorat',
            'AlumnoGrupo' => 'Alumnat del grup',
            'Alumno' => 'Alumne/a',
            'Menu' => 'Menú general',
            'Programacion' => 'Programació',
            'Expediente' => 'Expedient',
            'Reunion' => 'Reunió',
            'GrupoTrabajo' => 'Grup de Treball',
            'OrdenReunion' => 'Ordre',
            'Resultado' => 'Resultat Avaluació',
            'Documento' => 'Gestor Documental',
            'Empresa' => 'Empresa FCT',
            'Centro' => 'Centre de Treball',
            'Colaboracion' => 'Colaboració',
            'Fct' => 'FCT',
            'Tutoria' => 'Tutoria',
            'TutoriaGrupo' => 'FeedBack Tutoria',
            'Material' => 'Material',
            'TipoIncidencia' => 'Incidència',
            'OrdenTrabajo' => 'Ordre de Treball',
            'Ciclo' => 'Cicle',
            'Falta_itaca' => 'Justificar Birret',
            'Instructor' => 'Instructora',
            'Proyecto' => 'Projecte',
            'Evaluacion' => 'Avaluació',
            'Colaborador' => 'Col.laborador',
            'PPoll' => 'Plantilla Enquesta',
            'Poll' => 'Enquesta',
            'Option' => 'Pregunta',
            'AlumnoResultado' => 'Avaluacio Alumnat',
            'Lote' => 'Factura',
            'Articulo' => 'Article',
            'ArticuloLote' => 'Articles Factura',
            'Incidencia' => 'Incidència',
            'Fctcap' => 'Revisió FCT',
            'Fctdual' => 'Revisió Dual',
            'Solicitud' => "Derivació al departament d'orientació",
            'Signatura' => 'Signatures Digitals',
            'Cotxe' => 'Vehicles'
        ),
        'resign' => array(
            'Falta' => 'El document no justifica la baixa',
        ),
        'refuse' => array(
            'Programacion' => 'En la programació falten els siguents requeriments: ',
            
        ),
        'deliver' => array(
            'Falta' => 'Has de justificar la falta en Caporalia',
        ),
        'accept' => array(
            'Expediente' => "Baixa per inasistència de :alumno iniciada per :profesor",
        ),
        
);
