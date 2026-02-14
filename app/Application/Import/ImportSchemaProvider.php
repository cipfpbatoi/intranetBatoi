<?php

declare(strict_types=1);

namespace Intranet\Application\Import;

/**
 * Proveeix l'esquema de mapatge XML -> camps de BD per als imports.
 */
class ImportSchemaProvider
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function forGeneralImport(): array
    {
        return [
            [
                'nombrexml' => 'alumnos',
                'nombreclase' => 'Alumno',
                'id' => 'NIA',
                'filtro' => ['estado_matricula', '<>', 'B'],
                'update' => [
                    'dni' => 'hazDNI,documento,NIA',
                    'nia' => 'NIA',
                    'nombre' => 'nombre',
                    'apellido1' => 'apellido1',
                    'apellido2' => 'apellido2',
                    'fecha_nac' => 'getFechaFormatoIngles,fecha_nac',
                    'sexo' => 'sexo',
                    'expediente' => 'expediente',
                    'domicilio' => 'hazDomicilio,tipo_via,domicilio,numero,puerta,escalera,letra,piso',
                    'codigo_postal' => 'cod_postal',
                    'provincia' => 'provincia',
                    'municipio' => 'municipio',
                    'fecha_ingreso' => 'getFechaFormatoIngles,fecha_ingreso_centro',
                    'fecha_matricula' => 'getFechaFormatoIngles,fecha_matricula',
                    'repite' => 'repite',
                    'turno' => 'turno',
                    'trabaja' => 'trabaja',
                    'password' => 'cifrar,documento',
                    'baja' => null,
                ],
                'create' => [
                    'email' => 'email1',
                    'telef1' => 'digitos,telefono1',
                    'telef2' => 'digitos,telefono2',
                ],
            ],
            [
                'nombrexml' => 'docentes',
                'nombreclase' => 'Profesor',
                'id' => 'documento',
                'update' => [
                    'nombre' => 'nombre',
                    'apellido1' => 'apellido1',
                    'apellido2' => 'apellido2',
                    'sexo' => 'sexo',
                    'codigo_postal' => 'cod_postal',
                    'domicilio' => 'domicilio',
                    'emailItaca' => 'email1',
                    'sustituye_a' => 'titular_sustituido',
                    'fecha_nac' => 'getFechaFormatoIngles,fecha_nac',
                    'fecha_ingreso' => 'getFechaFormatoIngles,fecha_ingreso',
                    'fecha_ant' => 'getFechaFormatoIngles,fecha_antiguedad',
                    'activo' => true,
                ],
                'create' => [
                    'codigo' => 'creaCodigoProfesor,0',
                    'dni' => 'documento',
                    'email' => 'email2',
                    'movil1' => 'digitos,telefono1',
                    'movil2' => 'digitos,telefono2',
                    'departamento' => '99',
                    'password' => 'cifrar,documento',
                    'api_token' => 'aleatorio,60',
                ],
            ],
            [
                'nombrexml' => 'grupos',
                'nombreclase' => 'Grupo',
                'id' => 'codigo',
                'update' => [
                    'nombre' => 'nombre',
                    'turno' => 'turno',
                    'tutor' => 'tutor_ppal',
                ],
                'create' => [
                    'codigo' => 'codigo',
                ],
            ],
            [
                'nombrexml' => 'alumnos',
                'nombreclase' => 'AlumnoGrupo',
                'filtro' => ['estado_matricula', '<>', 'B'],
                'id' => 'NIA,grupo',
                'required' => ['NIA', 'grupo'],
                'update' => [
                    'idAlumno' => 'NIA',
                    'idGrupo' => 'grupo',
                ],
                'create' => [],
            ],
            [
                'nombrexml' => 'aulas',
                'nombreclase' => 'Espacio',
                'id' => 'codigo',
                'update' => [
                    'descripcion' => 'nombre',
                ],
                'create' => [
                    'aula' => 'codigo',
                    'idDepartamento' => '99',
                ],
            ],
            [
                'nombrexml' => 'ocupaciones',
                'nombreclase' => 'Ocupacion',
                'id' => 'codigo',
                'update' => [
                    'nombre' => 'nombre_cas',
                    'nom' => 'nombre_val',
                ],
                'create' => [
                    'codigo' => 'codigo',
                ],
            ],
            [
                'nombrexml' => 'contenidos',
                'nombreclase' => 'Modulo',
                'id' => 'codigo',
                'update' => [
                    'cliteral' => 'nombre_cas',
                    'vliteral' => 'nombre_val',
                ],
                'create' => [
                    'codigo' => 'codigo',
                ],
            ],
            [
                'nombrexml' => 'horarios_grupo',
                'nombreclase' => 'Horario',
                'id' => '',
                'update' => [],
                'create' => [
                    'dia_semana' => 'dia_semana',
                    'plantilla' => 'plantilla',
                    'sesion_orden' => 'sesion_orden',
                    'idProfesor' => 'docente',
                    'modulo' => 'contenido',
                    'idGrupo' => 'grupo',
                    'aula' => 'aula',
                ],
            ],
            [
                'nombrexml' => 'horarios_ocupaciones',
                'nombreclase' => 'Horario',
                'id' => '',
                'update' => [],
                'create' => [
                    'dia_semana' => 'dia_semana',
                    'sesion_orden' => 'sesion_orden',
                    'plantilla' => 'plantilla',
                    'idProfesor' => 'docente',
                    'ocupacion' => 'ocupacion',
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function forTeacherImport(): array
    {
        return [
            [
                'nombrexml' => 'docentes',
                'nombreclase' => 'Profesor',
                'id' => 'documento',
                'update' => [
                    'nombre' => 'nombre',
                    'apellido1' => 'apellido1',
                    'apellido2' => 'apellido2',
                    'sexo' => 'sexo',
                    'codigo_postal' => 'cod_postal',
                    'domicilio' => 'domicilio',
                    'movil1' => 'digitos,telefono1',
                    'movil2' => 'telefono2',
                    'emailItaca' => 'email1',
                    'sustituye_a' => 'titular_sustituido',
                    'fecha_nac' => 'getFechaFormatoIngles,fecha_nac',
                    'fecha_ingreso' => 'getFechaFormatoIngles,fecha_ingreso',
                    'fecha_ant' => 'getFechaFormatoIngles,fecha_antiguedad',
                    'activo' => true,
                ],
                'create' => [
                    'codigo' => 'creaCodigoProfesor,0',
                    'dni' => 'documento',
                    'email' => 'emailProfesorImport,nombre,apellido1',
                    'departamento' => '99',
                    'password' => 'cifrar,documento',
                    'api_token' => 'aleatorio,60',
                ],
            ],
            [
                'nombrexml' => 'horarios_grupo',
                'nombreclase' => 'Horario',
                'id' => '',
                'update' => [],
                'create' => [
                    'dia_semana' => 'dia_semana',
                    'plantilla' => 'plantilla',
                    'sesion_orden' => 'sesion_orden',
                    'idProfesor' => 'docente',
                    'modulo' => 'contenido',
                    'idGrupo' => 'grupo',
                    'aula' => 'aula',
                ],
            ],
            [
                'nombrexml' => 'horarios_ocupaciones',
                'nombreclase' => 'Horario',
                'id' => '',
                'update' => [],
                'create' => [
                    'dia_semana' => 'dia_semana',
                    'sesion_orden' => 'sesion_orden',
                    'plantilla' => 'plantilla',
                    'idProfesor' => 'docente',
                    'ocupacion' => 'ocupacion',
                ],
            ],
        ];
    }
}
