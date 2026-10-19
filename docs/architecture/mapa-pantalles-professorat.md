# Mapa de pantalles del professorat

Este mapa connecta la documentació funcional amb els punts d'entrada tècnics.
No és un inventari de tots els endpoints auxiliars: mostra les pantalles des
de les quals comença cada tasca del professorat.

> El menú es configura en base de dades. La presència d'una ruta en esta taula
> no implica que l'opció estiga activa per a tots els usuaris.

## Pantalles comunes

| Àrea | URL d'entrada | Implementació principal | Manual |
|---|---|---|---|
| Inici | `/home` | `Auth\Profesor\HomeController@index` | [Entrada i pàgina d'inici](../manuals/manual-profe.md#entrada) |
| Perfil | `/perfil` | `Auth\Profesor\PerfilController@editar` | [Editar el perfil](../manuals/manual-profe.md#perfil-i-fitxers-personals) |
| Fitxers personals | `/files` | `Auth\Profesor\PerfilController@files` | [Fitxers personals](../manuals/manual-profe.md#fitxers-personals) |
| Notificacions | `/notification` | `NotificationController@index` | [Notificacions](../manuals/manual-profe.md#notificacions) |
| Programacions del centre | `/allProgramacion` | `PanelModuloGrupoController@index` | [Totes les programacions](../manuals/manual-profe.md#totes-les-programacions) |
| Grups docents | `/grupo` | `GrupoController@index` | [Gestió de grups](../manuals/manual-profe.md#gestió-de-grups) |
| Mòduls impartits | `/modulogrupo` | `ModuloGrupoController@index` | [Gestió de mòduls](../manuals/manual-profe.md#gestió-de-mòduls) |
| Claustre/departaments | `/departamentos` | `ProfesorController@departamento` | [Claustre](../manuals/manual-profe.md#claustre) |
| Equip directiu | `/equipoDirectivo` | `ProfesorController@equipoDirectivo` | [Claustre i equip directiu](../manuals/manual-profe.md#claustre-i-equip-directiu) |
| Guàrdies | `/guardia` | `GuardiaController@index` | [Guàrdia](../manuals/manual-profe.md#guàrdia) |
| Seguiments | `/resultado` | `ResultadoController@index` | [Seguiments](../manuals/manual-profe.md#seguiments) |
| Activitats | `/actividad` | `ActividadController@index` | [Activitats extraescolars](../manuals/manual-profe.md#activitats-extraescolars) |
| Comissions de servei | `/comision` | `ComisionController@index` | [Comissions de servei](../manuals/manual-profe.md#comissions-de-servei) |
| Absències | `/falta` | `FaltaController@index` | [Notificació d'absències](../manuals/manual-profe.md#notificació-absències) |
| Assumptes particulars | `/assumptes-particulars` | `AssumpteParticularProfessorPanel` | [Assumptes particulars](../manuals/manual-profe.md#assumptes-particulars) |
| Expedients | `/expediente` | `ExpedienteController@index` | [Expedients](../manuals/manual-profe.md#expedients) |
| Incidències | `/incidencia` | `IncidenciaController@index` | [Gestió d'incidències](../manuals/manual-profe.md#gestió-dincidències) |
| Reserva d'aules | `/reserva` | `ReservaController@index` | [Reserva d'aules](../manuals/manual-profe.md#reserva-aules) |
| Canvi temporal d'horari | `/horario/change` | `HorarioController@horarioCambiar` | [Canvi d'horari](../manuals/manual-profe.md#sollicitar-canvi-dhorari) |
| Reunions | `/reunion` | `ReunionController@index` | [Gestió de reunions](../manuals/manual-profe.md#gestió-de-reunions) |
| Grups de treball | `/grupotrabajo` | `GrupoTrabajoController@index` | [Grups de treball](../manuals/manual-profe.md#grups-de-treball) |

Les rutes d'esta secció es defineixen principalment en `routes/profesor.php` i
`routes/todos.php`, darrere dels middleware `role:profesor` i `role:todos`.

## Pantalles condicionades pel rol

| Perfil | Pantalla | URL d'entrada | Implementació principal | Manual |
|---|---|---|---|---|
| Tutor/a | Tutories | `/tutoria` | `TutoriaController@index` | [Manual de tutoria](../manuals/manual-tutor.md) |
| Tutor/a | Seguiment del grup | `/tutoriagrupo/{id}` | `TutoriaGrupoController@edit` | [Manual de tutoria](../manuals/manual-tutor.md) |
| Cap de departament | Informe de departament | `/resultado/list` | `PanelListadoEntregasController@index` | [Informe de departament](../manuals/manual-cap.md#informe-de-departament) |
| Tutor/a d'FCT | Col·laboracions del departament | `/colaboracion` | `ColaboracionController@index` | [Col·laboracions del departament](../manuals/manual-fct-gestio-contactes.md) |
| Tutor/a d'FCT | Les meues col·laboracions | `/misColaboraciones` | `PanelColaboracionController@index` | [Les meues col·laboracions](../manuals/manual-fct-meues-colaboracions.md) |
| Tutor/a d'FCT | Empreses | `/empresa` | `EmpresaController@index` | [Empreses d'FCT](../manuals/manual-fct-empreses.md) |
| Tutor/a d'FCT | Panell de seguiment | `/fct` | `PanelFctController@index` | [Panell d'FCT](../manuals/manual-fct-panel.md) |
| Tutor/a d'FCT | FCT per alumne | `/alumnofct` | `FctAlumnoController@index` | [FCT de l'alumnat](../manuals/manual-fct-alumno.md) |
| Tutor/a d'FCT | Avaluació | `/avalFct` | `PanelFctAvalController@index` | [Avaluació d'FCT](../manuals/manual-fct-avaluacio.md) |
| Tutor/a d'FCT | Signatures | `/signatura` | `SignaturaController@index` | [Signatures digitals](../manuals/manual-fct.md#signatures-digitals) |
| Direcció | Activitats | `/direccion/actividad` | `ActividadDireccionPanel` | [Activitats](../manuals/manual-dir.md#activitats) |
| Direcció | Comissions | `/direccion/comision` | `ComisionDireccionPanel` | [Comissions](../manuals/manual-dir.md#comissions-de-servei) |
| Direcció | Absències | `/direccion/falta` | `FaltaDireccionPanel` | [Absències](../manuals/manual-dir.md#absències) |
| Direcció | Assumptes particulars | `/direccion/assumptes-particulars` | `AssumpteParticularDireccionPanel` | [Assumptes particulars](../manuals/manual-dir.md#assumptes-particulars) |
| Direcció | Programacions | `/direccion/programacion/list` | `PanelControlProgramacionController@index` | [Programacions](../manuals/manual-dir.md#programacions) |
| Direcció | Professorat | `/direccion/profesor` | `ProfesorController@index` | [Dades del professorat](../manuals/manual-dir.md#dades-del-professorat) |

Els rols són acumulatius. Un tutor o un cap de departament continua accedint a
les pantalles generals del professorat i rep opcions addicionals.

## Com verificar el mapa

Abans de publicar una revisió del manual:

1. iniciar sessió amb un usuari de cada perfil documentat;
2. anotar el menú i els submenús visibles;
3. comparar-los amb la taula `menus` i amb `php artisan route:list`;
4. obrir cada pantalla d'entrada i verificar el títol, els filtres i les
   accions disponibles;
5. actualitzar la captura i el manual funcional;
6. comprovar que la icona d'ajuda contextual obri l'apartat correcte.
