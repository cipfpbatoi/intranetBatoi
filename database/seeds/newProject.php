<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Intranet\Entities\Menu;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Departamento;
use Intranet\Entities\Hora;
use Intranet\Entities\TipoIncidencia;
use Intranet\Entities\Profesor;

class newProject extends Seeder
{

    public function run()
    {
        Departamento::create([
            'id' => 1,
            'cliteral' => 'INGLES',
            'vliteral' => 'ANGLES',
            'depcurt' => 'Ang'
        ]);



        Departamento::create([
            'id' => 2,
            'cliteral' => 'SERVICIOS A LA COMUNIDAD',
            'vliteral' => 'SEVEIS A LA COMUNITAT',
            'depcurt' => 'SCo'
        ]);



        Departamento::create([
            'id' => 3,
            'cliteral' => 'IMAGEN PERSONAL',
            'vliteral' => 'IMATGE PERSONAL',
            'depcurt' => 'Img'
        ]);



        Departamento::create([
            'id' => 4,
            'cliteral' => 'PROGRAMAS DE GARANTIA SOCIAL',
            'vliteral' => 'PROGRAMES DE GARANTIA SOCIAL',
            'depcurt' => 'Pgs'
        ]);



        Departamento::create([
            'id' => 5,
            'cliteral' => 'DEPARTAMENTO ADMINISTRATIVO',
            'vliteral' => 'DEPARTAMENT ADMINISTRACIÓ',
            'depcurt' => 'Adm'
        ]);



        Departamento::create([
            'id' => 6,
            'cliteral' => 'DEPARTAMENTO SANITARIO',
            'vliteral' => 'DEPARTAMENT SANITARI',
            'depcurt' => 'San'
        ]);



        Departamento::create([
            'id' => 9,
            'cliteral' => 'EXTENSION CULTURAL',
            'vliteral' => 'EXTENSIO CULTURAL',
            'depcurt' => 'Cul'
        ]);



        Departamento::create([
            'id' => 10,
            'cliteral' => 'HOSTELERIA Y TURISMO',
            'vliteral' => 'HOSTELERIA I TURISME',
            'depcurt' => 'Hos'
        ]);



        Departamento::create([
            'id' => 12,
            'cliteral' => 'FORMACION Y ORIENTACION LABORA',
            'vliteral' => 'FORMACIO I ORIENTACIO LABORAL',
            'depcurt' => 'Fol'
        ]);



        Departamento::create([
            'id' => 14,
            'cliteral' => 'FRANCES',
            'vliteral' => 'FRANCES',
            'depcurt' => 'Fra'
        ]);



        Departamento::create([
            'id' => 18,
            'cliteral' => 'ORIENTACION',
            'vliteral' => 'ORIENTACIO',
            'depcurt' => 'Ori'
        ]);



        Departamento::create([
            'id' => 22,
            'cliteral' => 'CICLOS FORMATIVOS',
            'vliteral' => 'CICLES FORMATIUS',
            'depcurt' => 'Cf'
        ]);



        Departamento::create([
            'id' => 23,
            'cliteral' => 'FCT',
            'vliteral' => 'FCT',
            'depcurt' => 'Fct'
        ]);



        Departamento::create([
            'id' => 24,
            'cliteral' => 'DEPARTAMENTO INFORMATICA',
            'vliteral' => 'DEPARTAMENT INFORMÀTICA',
            'depcurt' => 'Inf'
        ]);



        Departamento::create([
            'id' => 90,
            'cliteral' => 'Personal No Docente',
            'vliteral' => 'Personal No Docent',
            'depcurt' => 'PND'
        ]);



        Departamento::create([
            'id' => 91,
            'cliteral' => 'Personal Limpieza',
            'vliteral' => 'Personal Neteja',
            'depcurt' => 'NET'
        ]);



        Departamento::create([
            'id' => 99,
            'cliteral' => 'Desconegut',
            'vliteral' => 'Desconegut',
            'depcurt' => '???'
        ]);
        Menu::create([
            'id' => 1,
            'nombre' => 'perfil',
            'url' => '/perfil',
            'class' => 'fa-user pull-right',
            'rol' => 1,
            'menu' => 'topmenu',
            'submenu' => '',
            'activo' => 1,
            'orden' => 1
        ]);

        Menu::create([
            'id' => 2,
            'nombre' => 'logout',
            'url' => '/logout',
            'class' => 'fa-power-off pull-right',
            'rol' => 1,
            'menu' => 'topmenu',
            'submenu' => '',
            'activo' => 1,
            'orden' => 2
        ]);

        Menu::create([
            'id' => 3,
            'nombre' => 'perfil',
            'url' => '/alumno/perfil',
            'class' => 'fa-user pull-right',
            'rol' => 1,
            'menu' => 'topalumno',
            'submenu' => '',
            'activo' => 1,
            'orden' => 1
        ]);

        Menu::create([
            'id' => 4,
            'nombre' => 'logout',
            'url' => '/alumno/logout',
            'class' => 'fa-power-off pull-right',
            'rol' => 1,
            'menu' => 'topalumno',
            'submenu' => '',
            'activo' => 1,
            'orden' => 2
        ]);

        Menu::create([
            'id' => 5,
            'nombre' => 'link',
            'url' => '',
            'class' => 'fa-external-link',
            'rol' => 1,
            'menu' => 'general',
            'submenu' => '',
            'activo' => 1,
            'orden' => 3
        ]);

        Menu::create([
            'id' => 6,
            'nombre' => 'edit',
            'url' => '',
            'class' => 'fa-graduation-cap',
            'rol' => 3,
            'menu' => 'general',
            'submenu' => '',
            'activo' => 1,
            'orden' => 4
        ]);

        Menu::create([
            'id' => 7,
            'nombre' => 'institution',
            'url' => '',
            'class' => 'fa-users',
            'rol' => 17,
            'menu' => 'general',
            'submenu' => '',
            'activo' => 1,
            'orden' => 7
        ]);

        Menu::create([
            'id' => 8,
            'nombre' => 'direccion',
            'url' => '',
            'class' => 'fa-home',
            'rol' => 2,
            'menu' => 'general',
            'submenu' => '',
            'activo' => 1,
            'orden' => 10
        ]);

        Menu::create([
            'id' => 9,
            'nombre' => 'administracion',
            'url' => '',
            'class' => 'fa-gears',
            'rol' => 11,
            'menu' => 'general',
            'submenu' => '',
            'activo' => 1,
            'orden' => 13
        ]);

        Menu::create([
            'id' => 10,
            'nombre' => 'gmail',
            'url' => 'https://www.gmail.com',
            'class' => '',
            'rol' => 1,
            'menu' => 'general',
            'submenu' => 'link',
            'activo' => 1,
            'orden' => 2
        ]);

        Menu::create([
            'id' => 11,
            'nombre' => 'moodle',
            'url' => 'https://moodle.cipfpbatoi.es',
            'class' => '',
            'rol' => 1,
            'menu' => 'general',
            'submenu' => 'link',
            'activo' => 1,
            'orden' => 1
        ]);

        Menu::create([
            'id' => 12,
            'nombre' => 'itaca',
            'url' => 'https://acces.edu.gva.es/',
            'class' => '',
            'rol' => 1,
            'menu' => 'general',
            'submenu' => 'link',
            'activo' => 1,
            'orden' => 3
        ]);

        Menu::create([
            'id' => 13,
            'nombre' => 'extraescolar',
            'url' => '/actividad',
            'class' => '',
            'rol' => 3,
            'menu' => 'general',
            'submenu' => 'edit',
            'activo' => 1,
            'orden' => 5
        ]);

        Menu::create([
            'id' => 14,
            'nombre' => 'comision',
            'url' => '/comision',
            'class' => '',
            'rol' => 3,
            'menu' => 'general',
            'submenu' => 'edit',
            'activo' => 1,
            'orden' => 6
        ]);

        Menu::create([
            'id' => 15,
            'nombre' => 'manipulador',
            'url' => '/curso',
            'class' => '',
            'rol' => 2,
            'menu' => 'general',
            'submenu' => 'direccion',
            'activo' => 1,
            'orden' => 1
        ]);

        Menu::create([
            'id' => 16,
            'nombre' => 'baja',
            'url' => '/falta',
            'class' => '',
            'rol' => 3,
            'menu' => 'general',
            'submenu' => 'edit',
            'activo' => 1,
            'orden' => 7
        ]);

        Menu::create([
            'id' => 17,
            'nombre' => 'grupo',
            'url' => '/grupo',
            'class' => '',
            'rol' => 3,
            'menu' => 'general',
            'submenu' => 'edit',
            'activo' => 1,
            'orden' => 2
        ]);

        Menu::create([
            'id' => 18,
            'nombre' => 'profesor',
            'url' => '/direccion/profesor',
            'class' => '',
            'rol' => 2,
            'menu' => 'general',
            'submenu' => 'direccion',
            'activo' => 1,
            'orden' => 2
        ]);

        Menu::create([
            'id' => 19,
            'nombre' => 'Authcomision',
            'url' => '/direccion/comision',
            'class' => '',
            'rol' => 2,
            'menu' => 'general',
            'submenu' => 'direccion',
            'activo' => 1,
            'orden' => 3
        ]);

        Menu::create([
            'id' => 20,
            'nombre' => 'Authactividad',
            'url' => '/direccion/actividad',
            'class' => '',
            'rol' => 2,
            'menu' => 'general',
            'submenu' => 'direccion',
            'activo' => 1,
            'orden' => 4
        ]);

        Menu::create([
            'id' => 21,
            'nombre' => 'departamento',
            'url' => '/departamento',
            'class' => '',
            'rol' => 3,
            'menu' => 'general',
            'submenu' => 'edit',
            'activo' => 1,
            'orden' => 1
        ]);

        Menu::create([
            'id' => 22,
            'nombre' => 'menu',
            'url' => '/menu',
            'class' => '',
            'rol' => 11,
            'menu' => 'general',
            'submenu' => 'administracion',
            'activo' => 1,
            'orden' => 1
        ]);

        Menu::create([
            'id' => 23,
            'nombre' => 'inventario',
            'url' => '',
            'class' => 'fa-cubes',
            'rol' => 7,
            'menu' => 'general',
            'submenu' => '',
            'activo' => 1,
            'orden' => 11
        ]);

        Menu::create([
            'id' => 24,
            'nombre' => 'espacios',
            'url' => '/espacio',
            'class' => '',
            'rol' => 7,
            'menu' => 'general',
            'submenu' => 'inventario',
            'activo' => 1,
            'orden' => 1
        ]);

        Menu::create([
            'id' => 25,
            'nombre' => 'materiales',
            'url' => '/material',
            'class' => '',
            'rol' => 7,
            'menu' => 'general',
            'submenu' => 'inventario',
            'activo' => 1,
            'orden' => 2
        ]);

        Menu::create([
            'id' => 26,
            'nombre' => 'incidencias',
            'url' => '/incidencia',
            'class' => '',
            'rol' => 3,
            'menu' => 'general',
            'submenu' => 'edit',
            'activo' => 1,
            'orden' => 8
        ]);

        Menu::create([
            'id' => 27,
            'nombre' => 'Authfalta',
            'url' => '/direccion/falta',
            'class' => '',
            'rol' => 2,
            'menu' => 'general',
            'submenu' => 'direccion',
            'activo' => 1,
            'orden' => 5
        ]);

        Menu::create([
            'id' => 28,
            'nombre' => 'incidenciasmant',
            'url' => '/mantenimiento/incidencia',
            'class' => '',
            'rol' => 7,
            'menu' => 'general',
            'submenu' => 'inventario',
            'activo' => 1,
            'orden' => 3
        ]);

        Menu::create([
            'id' => 30,
            'nombre' => 'programacion',
            'url' => '/programacion',
            'class' => '',
            'rol' => 3,
            'menu' => 'general',
            'submenu' => 'edit',
            'activo' => 1,
            'orden' => 3
        ]);

        Menu::create([
            'id' => 31,
            'nombre' => 'Authprogram',
            'url' => '/departamento/programacion',
            'class' => '',
            'rol' => 13,
            'menu' => 'general',
            'submenu' => 'jefedep',
            'activo' => 1,
            'orden' => 1
        ]);

        Menu::create([
            'id' => 32,
            'nombre' => 'modulo',
            'url' => '/modulo',
            'class' => '',
            'rol' => 11,
            'menu' => 'general',
            'submenu' => 'auxiliar',
            'activo' => 1,
            'orden' => 1
        ]);

        Menu::create([
            'id' => 33,
            'nombre' => 'jefedep',
            'url' => '',
            'class' => 'fa-institution',
            'rol' => 13,
            'menu' => 'general',
            'submenu' => '',
            'activo' => 1,
            'orden' => 6
        ]);

        Menu::create([
            'id' => 35,
            'nombre' => 'progstate',
            'url' => '/direccion/programacion/list',
            'class' => '',
            'rol' => 2,
            'menu' => 'general',
            'submenu' => 'direccion',
            'activo' => 1,
            'orden' => 6
        ]);

        Menu::create([
            'id' => 36,
            'nombre' => 'expediente',
            'url' => '/expediente',
            'class' => '',
            'rol' => 17,
            'menu' => 'general',
            'submenu' => 'institution',
            'activo' => 1,
            'orden' => 2
        ]);

        Menu::create([
            'id' => 37,
            'nombre' => 'Authexpediente',
            'url' => '/direccion/expediente',
            'class' => '',
            'rol' => 2,
            'menu' => 'general',
            'submenu' => 'direccion',
            'activo' => 1,
            'orden' => 7
        ]);

        Menu::create([
            'id' => 38,
            'nombre' => 'Reunion',
            'url' => '/reunion',
            'class' => '',
            'rol' => 3,
            'menu' => 'general',
            'submenu' => 'gTrabajo',
            'activo' => 1,
            'orden' => 1
        ]);

        Menu::create([
            'id' => 39,
            'nombre' => 'gtrabajo',
            'url' => '',
            'class' => 'fa-edit',
            'rol' => 3,
            'menu' => 'general',
            'submenu' => '',
            'activo' => 1,
            'orden' => 8
        ]);

        Menu::create([
            'id' => 40,
            'nombre' => 'grtrabajo',
            'url' => '/grupotrabajo',
            'class' => '',
            'rol' => 3,
            'menu' => 'general',
            'submenu' => 'gTrabajo',
            'activo' => 1,
            'orden' => 2
        ]);

        Menu::create([
            'id' => 42,
            'nombre' => 'resultados',
            'url' => '/resultado',
            'class' => '',
            'rol' => 3,
            'menu' => 'general',
            'submenu' => 'edit',
            'activo' => 1,
            'orden' => 4
        ]);

        Menu::create([
            'id' => 43,
            'nombre' => 'register',
            'url' => '/alumno/curso',
            'class' => '',
            'rol' => 5,
            'menu' => 'general',
            'submenu' => 'alumno',
            'activo' => 1,
            'orden' => 1
        ]);

        Menu::create([
            'id' => 44,
            'nombre' => 'documento',
            'url' => '/documento',
            'class' => '',
            'rol' => 1,
            'menu' => 'general',
            'submenu' => 'documents',
            'activo' => 1,
            'orden' => 6
        ]);

        Menu::create([
            'id' => 45,
            'nombre' => 'nohanfichado',
            'url' => '/direccion/fichar/list',
            'class' => '',
            'rol' => 2,
            'menu' => 'general',
            'submenu' => 'control',
            'activo' => 1,
            'orden' => 1
        ]);

        Menu::create([
            'id' => 46,
            'nombre' => 'extraescolares',
            'url' => '/resultado/list',
            'class' => '',
            'rol' => 13,
            'menu' => 'general',
            'submenu' => 'jefedep',
            'activo' => 1,
            'orden' => 2
        ]);

        Menu::create([
            'id' => 48,
            'nombre' => 'empresa',
            'url' => '/empresa',
            'class' => NULL,
            'rol' => 31,
            'menu' => 'general',
            'submenu' => 'practicas',
            'activo' => 1,
            'orden' => 4
        ]);

        Menu::create([
            'id' => 49,
            'nombre' => 'guardia',
            'url' => '/guardia',
            'class' => '',
            'rol' => 3,
            'menu' => 'general',
            'submenu' => 'edit',
            'activo' => 1,
            'orden' => 9
        ]);

        Menu::create([
            'id' => 50,
            'nombre' => 'resultados',
            'url' => '/resultado/pdf',
            'class' => '',
            'rol' => 17,
            'menu' => 'general',
            'submenu' => 'institution',
            'activo' => 1,
            'orden' => 1
        ]);

        Menu::create([
            'id' => 51,
            'nombre' => 'lfaltas',
            'url' => '/direccion/falta/pdf',
            'class' => '',
            'rol' => 2,
            'menu' => 'general',
            'submenu' => 'control',
            'activo' => 1,
            'orden' => 4
        ]);

        Menu::create([
            'id' => 52,
            'nombre' => 'fct',
            'url' => '/fct',
            'class' => NULL,
            'rol' => 31,
            'menu' => 'general',
            'submenu' => 'practicas',
            'activo' => 1,
            'orden' => 1
        ]);

        Menu::create([
            'id' => 53,
            'nombre' => 'fichar',
            'url' => '/fichar',
            'class' => 'fa-ticket',
            'rol' => 23,
            'menu' => 'general',
            'submenu' => '',
            'activo' => 1,
            'orden' => 1
        ]);

        Menu::create([
            'id' => 54,
            'nombre' => 'alumno',
            'url' => '',
            'class' => 'fa-graduation-cap',
            'rol' => 5,
            'menu' => 'general',
            'submenu' => '',
            'activo' => 1,
            'orden' => 14
        ]);

        Menu::create([
            'id' => 55,
            'nombre' => 'programacion',
            'url' => '/allProgramacion',
            'class' => '',
            'rol' => 1,
            'menu' => 'general',
            'submenu' => 'documents',
            'activo' => 1,
            'orden' => 1
        ]);

        Menu::create([
            'id' => 56,
            'nombre' => 'orientacion',
            'url' => '',
            'class' => 'fa-paperclip',
            'rol' => 29,
            'menu' => 'general',
            'submenu' => '',
            'activo' => 1,
            'orden' => 12
        ]);

        Menu::create([
            'id' => 57,
            'nombre' => 'acttut',
            'url' => '/actividad',
            'class' => '',
            'rol' => 29,
            'menu' => 'general',
            'submenu' => 'orientación',
            'activo' => 1,
            'orden' => 1
        ]);

        Menu::create([
            'id' => 58,
            'nombre' => 'tutoria',
            'url' => '/tutoria',
            'class' => '',
            'rol' => 29,
            'menu' => 'general',
            'submenu' => 'orientación',
            'activo' => 1,
            'orden' => 2
        ]);

        Menu::create([
            'id' => 59,
            'nombre' => 'tutoria',
            'url' => '/tutoria',
            'class' => '',
            'rol' => 17,
            'menu' => 'general',
            'submenu' => 'institution',
            'activo' => 1,
            'orden' => 3
        ]);

        Menu::create([
            'id' => 60,
            'nombre' => 'importacion',
            'url' => '/import',
            'class' => '',
            'rol' => 11,
            'menu' => 'general',
            'submenu' => 'administracion',
            'activo' => 1,
            'orden' => 2
        ]);

        Menu::create([
            'id' => 70,
            'nombre' => 'documents',
            'url' => '',
            'class' => 'fa-book',
            'rol' => 1,
            'menu' => 'general',
            'submenu' => '',
            'activo' => 1,
            'orden' => 2
        ]);

        Menu::create([
            'id' => 71,
            'nombre' => 'centro',
            'url' => '/documento/2/grupo',
            'class' => '',
            'rol' => 3,
            'menu' => 'general',
            'submenu' => 'documents',
            'activo' => 1,
            'orden' => 3
        ]);

        Menu::create([
            'id' => 72,
            'nombre' => 'proceso',
            'url' => '/documento/1/grupo',
            'class' => '',
            'rol' => 1,
            'menu' => 'general',
            'submenu' => 'documents',
            'activo' => 1,
            'orden' => 2
        ]);

        Menu::create([
            'id' => 73,
            'nombre' => 'acta',
            'url' => '/documento/3/acta',
            'class' => '',
            'rol' => 3,
            'menu' => 'general',
            'submenu' => 'documents',
            'activo' => 1,
            'orden' => 4
        ]);

        Menu::create([
            'id' => 74,
            'nombre' => 'proyecto',
            'url' => '/proyecto',
            'class' => '',
            'rol' => 1,
            'menu' => 'general',
            'submenu' => 'documents',
            'activo' => 1,
            'orden' => 5
        ]);

        Menu::create([
            'id' => 75,
            'nombre' => 'reserva',
            'url' => '/reserva',
            'class' => '',
            'rol' => 3,
            'menu' => 'general',
            'submenu' => 'edit',
            'activo' => 1,
            'orden' => 11
        ]);

        Menu::create([
            'id' => 76,
            'nombre' => 'equipo',
            'url' => '/alumno/equipo',
            'class' => '',
            'rol' => 5,
            'menu' => 'general',
            'submenu' => 'alumno',
            'activo' => 1,
            'orden' => 2
        ]);

        Menu::create([
            'id' => 77,
            'nombre' => 'progstate',
            'url' => '/departamento/programacion/list',
            'class' => '',
            'rol' => 13,
            'menu' => 'general',
            'submenu' => 'jefedep',
            'activo' => 1,
            'orden' => 3
        ]);

        Menu::create([
            'id' => 78,
            'nombre' => 'apitoken',
            'url' => '/apiToken',
            'class' => '',
            'rol' => 11,
            'menu' => 'general',
            'submenu' => 'administracion',
            'activo' => 1,
            'orden' => 3
        ]);

        Menu::create([
            'id' => 80,
            'nombre' => 'Controlg',
            'url' => '/direccion/guardia/control',
            'class' => '',
            'rol' => 2,
            'menu' => 'general',
            'submenu' => 'control',
            'activo' => 1,
            'orden' => 5
        ]);

        Menu::create([
            'id' => 81,
            'nombre' => 'Controlp',
            'url' => '/direccion/fichar/control',
            'class' => '',
            'rol' => 2,
            'menu' => 'general',
            'submenu' => 'control',
            'activo' => 1,
            'orden' => 3
        ]);

        Menu::create([
            'id' => 82,
            'nombre' => 'equipodirectivo',
            'url' => '/equipoDirectivo',
            'class' => '',
            'rol' => 3,
            'menu' => 'general',
            'submenu' => 'edit',
            'activo' => 1,
            'orden' => 10
        ]);

        Menu::create([
            'id' => 83,
            'nombre' => 'Controld',
            'url' => '/direccion/fichar/controlDia',
            'class' => '',
            'rol' => 2,
            'menu' => 'general',
            'submenu' => 'control',
            'activo' => 1,
            'orden' => 2
        ]);

        Menu::create([
            'id' => 84,
            'nombre' => 'ordentrabajo',
            'url' => '/mantenimiento/ordentrabajo',
            'class' => '',
            'rol' => 7,
            'menu' => 'general',
            'submenu' => 'inventario',
            'activo' => 1,
            'orden' => 4
        ]);

        Menu::create([
            'id' => 85,
            'nombre' => 'Controlreunion',
            'url' => '/direccion/reunion/list',
            'class' => '',
            'rol' => 2,
            'menu' => 'general',
            'submenu' => 'control',
            'activo' => 1,
            'orden' => 6
        ]);

        Menu::create([
            'id' => 86,
            'nombre' => 'control',
            'url' => '',
            'class' => 'fa-check',
            'rol' => 2,
            'menu' => 'general',
            'submenu' => '',
            'activo' => 1,
            'orden' => 9
        ]);

        Menu::create([
            'id' => 87,
            'nombre' => 'Controlsegui',
            'url' => '/resultado/list',
            'class' => '',
            'rol' => 2,
            'menu' => 'general',
            'submenu' => 'control',
            'activo' => 1,
            'orden' => 7
        ]);

        Menu::create([
            'id' => 88,
            'nombre' => 'cicle',
            'url' => '/ciclo',
            'class' => NULL,
            'rol' => 11,
            'menu' => 'general',
            'submenu' => 'auxiliar',
            'activo' => 1,
            'orden' => 2
        ]);

        Menu::create([
            'id' => 89,
            'nombre' => 'practicas',
            'url' => '',
            'class' => 'fa-car',
            'rol' => 31,
            'menu' => 'general',
            'submenu' => '',
            'activo' => 1,
            'orden' => 5
        ]);

        Menu::create([
            'id' => 90,
            'nombre' => 'colaboracion',
            'url' => '/colaboracion',
            'class' => '',
            'rol' => 31,
            'menu' => 'general',
            'submenu' => 'practicas',
            'activo' => 1,
            'orden' => 5
        ]);

        Menu::create([
            'id' => 91,
            'nombre' => 'Horarios',
            'url' => '/direccion/horarios/pdf',
            'class' => '',
            'rol' => 2,
            'menu' => 'general',
            'submenu' => 'control',
            'activo' => 1,
            'orden' => 8
        ]);

        Menu::create([
            'id' => 92,
            'nombre' => 'grupo',
            'url' => '/grupo',
            'class' => '',
            'rol' => 3,
            'menu' => 'general',
            'submenu' => 'edit',
            'activo' => 0,
            'orden' => 12
        ]);

        Menu::create([
            'id' => 93,
            'nombre' => 'birret',
            'url' => '/itaca',
            'class' => '',
            'rol' => 3,
            'menu' => 'general',
            'submenu' => 'edit',
            'activo' => 1,
            'orden' => 13
        ]);

        Menu::create([
            'id' => 94,
            'nombre' => 'Authbirret',
            'url' => '/direccion/falta_itaca',
            'class' => '',
            'rol' => 2,
            'menu' => 'general',
            'submenu' => 'direccion',
            'activo' => 1,
            'orden' => 8
        ]);

        Menu::create([
            'id' => 95,
            'nombre' => 'instructor',
            'url' => '/instructor',
            'class' => '',
            'rol' => 31,
            'menu' => 'general',
            'submenu' => 'practicas',
            'activo' => 1,
            'orden' => 3
        ]);

        Menu::create([
            'id' => 96,
            'nombre' => 'empresasc',
            'url' => '/empresaSC',
            'class' => '',
            'rol' => 31,
            'menu' => 'general',
            'submenu' => 'practicas',
            'activo' => 1,
            'orden' => 6
        ]);

        Menu::create([
            'id' => 97,
            'nombre' => 'avaluar',
            'url' => '/avalFct',
            'class' => NULL,
            'rol' => 31,
            'menu' => 'general',
            'submenu' => 'practicas',
            'activo' => 1,
            'orden' => 2
        ]);

        Menu::create([
            'id' => 98,
            'nombre' => 'infdpto',
            'url' => '/direccion/infDpto',
            'class' => '',
            'rol' => 2,
            'menu' => 'general',
            'submenu' => 'documents',
            'activo' => 1,
            'orden' => 7
        ]);

        Menu::create([
            'id' => 99,
            'nombre' => 'expediente',
            'url' => '/expedienteO',
            'class' => '',
            'rol' => 29,
            'menu' => 'general',
            'submenu' => 'orientación',
            'activo' => 1,
            'orden' => 3
        ]);

        Menu::create([
            'id' => 100,
            'nombre' => 'borrarprg',
            'url' => '/programacion/deleteOld',
            'class' => NULL,
            'rol' => 11,
            'menu' => 'general',
            'submenu' => 'administracion',
            'activo' => 1,
            'orden' => 4
        ]);

        Menu::create([
            'id' => 101,
            'nombre' => 'Authhorarios',
            'url' => '/direccion/horarios/cambiar',
            'class' => '',
            'rol' => 2,
            'menu' => 'general',
            'submenu' => 'direccion',
            'activo' => 1,
            'orden' => 9
        ]);

        Menu::create([
            'id' => 102,
            'nombre' => 'Indexdocumento',
            'url' => '/direccion/documento',
            'class' => '',
            'rol' => 2,
            'menu' => 'general',
            'submenu' => 'documents',
            'activo' => 1,
            'orden' => 8
        ]);

        Menu::create([
            'id' => 103,
            'nombre' => 'Nuevocurso',
            'url' => '/nuevoCurso',
            'class' => NULL,
            'rol' => 11,
            'menu' => 'general',
            'submenu' => 'administracion',
            'activo' => 0,
            'orden' => 5
        ]);

        Menu::create([
            'id' => 104,
            'nombre' => 'Changeschedule',
            'url' => '/horario/change',
            'class' => '',
            'rol' => 3,
            'menu' => 'general',
            'submenu' => 'edit',
            'activo' => 1,
            'orden' => 14
        ]);

        Menu::create([
            'id' => 105,
            'nombre' => 'auxiliar',
            'url' => '',
            'class' => 'fa-gears',
            'rol' => 11,
            'menu' => 'general',
            'submenu' => '',
            'activo' => 1,
            'orden' => 15
        ]);

        Menu::create([
            'id' => 106,
            'nombre' => 'modulociclo',
            'url' => '/modulo_ciclo',
            'class' => NULL,
            'rol' => 11,
            'menu' => 'general',
            'submenu' => 'auxiliar',
            'activo' => 1,
            'orden' => 3
        ]);

        Menu::create([
            'id' => 107,
            'nombre' => 'Actasfct',
            'url' => '/direccion/controlFct',
            'class' => '',
            'rol' => 2,
            'menu' => 'general',
            'submenu' => 'direccion',
            'activo' => 1,
            'orden' => 10
        ]);

        Ciclo::create([
            'id' => 2,
            'ciclo' => 'CFM  FCT ESTÈTICA (LOGSE)',
            'departamento' => 3,
            'responsable' => '',
            'tipo' => 1,
            'normativa' => 'LOGSE',
            'titol' => NULL,
            'rd' => NULL,
            'rd2' => NULL,
            'vliteral' => NULL,
            'cliteral' => NULL
        ]);



        Ciclo::create([
            'id' => 3,
            'ciclo' => 'CFM APD (LOE)',
            'departamento' => 2,
            'responsable' => '',
            'tipo' => 1,
            'normativa' => 'LOE',
            'titol' => 'TÈCNIC EN ATENCIÓ A PERSONES EN SITUACIÓ DE DEPENDÈNCIA',
            'rd' => 'RD 1593/2011 (BOE 15/12/2011)',
            'rd2' => 'l\'Orde 30/2015, de 13 de març (DOGV 25/03/2015)',
            'vliteral' => 'Atenció a persones en situació de dependència',
            'cliteral' => 'Atención a personas en situación de dependencia'
        ]);



        Ciclo::create([
            'id' => 4,
            'ciclo' => 'CFM FARMACIA (LOE)',
            'departamento' => 6,
            'responsable' => '',
            'tipo' => 1,
            'normativa' => 'LOE',
            'titol' => 'TÈCNIC EN FARMÀCIA I PARAFARMÀCIA',
            'rd' => 'RD 1689/2007 (BOE 17/01/2008)',
            'rd2' => 'l\'Orde de 29 de juliol (DOGV 02/09/2009)',
            'vliteral' => 'Farmàcia i parafarmàcia',
            'cliteral' => 'Farmacia y parafarmacia'
        ]);



        Ciclo::create([
            'id' => 8,
            'ciclo' => 'CFM CAE (LOGSE)',
            'departamento' => 6,
            'responsable' => '',
            'tipo' => 1,
            'normativa' => 'LOGSE',
            'titol' => 'TÈCNIC EN CURES AUXILIARS D\'INFERMERIA',
            'rd' => 'Reial Decret 546/1995 (BOE 05/06/95)',
            'rd2' => NULL,
            'vliteral' => 'Cures auxiliares d\'infermeria',
            'cliteral' => 'Curas auxiliares de enfermería'
        ]);



        Ciclo::create([
            'id' => 12,
            'ciclo' => 'CFM CUINA (LOE)',
            'departamento' => 10,
            'responsable' => '',
            'tipo' => 1,
            'normativa' => 'LOE',
            'titol' => 'TÈCNIC EN CUINA I GASTRONOMIA',
            'rd' => 'RD 1396/2007 BOE 23-11-2007',
            'rd2' => 'l\'Orde de 29 de juliol de 2009 (DOGV 03/09/2009)',
            'vliteral' => 'Cuina i gastronomia',
            'cliteral' => 'Cocina y gastronomía'
        ]);



        Ciclo::create([
            'id' => 16,
            'ciclo' => 'CFM ESTÈTICA I BELL. (LOE)',
            'departamento' => 3,
            'responsable' => '',
            'tipo' => 1,
            'normativa' => 'LOE',
            'titol' => 'TÈCNIC EN ESTÈTICA I BELLESA',
            'rd' => 'RD 256/2011 de 28 de febrero (BOE 7/04/2011)',
            'rd2' => 'el Decret 158/2017, de 6 d\'octubre (DOGV 20/10/2017)',
            'vliteral' => 'Estètica i bellesa',
            'cliteral' => 'Estética y belleza'
        ]);



        Ciclo::create([
            'id' => 18,
            'ciclo' => 'CFM GESTIÓ ADMVA. (LOE)',
            'departamento' => 5,
            'responsable' => '',
            'tipo' => 1,
            'normativa' => 'LOE',
            'titol' => 'TÈCNIC EN GESTIÓ ADMINISTRATIVA',
            'rd' => 'RD 1631/2009 (BOE 01/12/2009)',
            'rd2' => 'RD 1126/2010 (BOE 11/09/2010) i l\'Orde 37/2012, de 22 de juny (DOGV 09/07/2012).',
            'vliteral' => 'Gestió administrativa',
            'cliteral' => 'Gestión administrativa'
        ]);



        Ciclo::create([
            'id' => 20,
            'ciclo' => 'CFM PERRUQUERIA (LOE)',
            'departamento' => 3,
            'responsable' => '',
            'tipo' => 1,
            'normativa' => 'LOE',
            'titol' => 'TÈCNIC EN PERRUQUERIA I COSMÈTICA CAPILAR',
            'rd' => 'RD 1588/2011 (BOE 15/12/2011)',
            'rd2' => 'l\'Orde 32/2015, de 13 de març (DOGV 26/03/2015)',
            'vliteral' => 'Perruqueria i cosmètica capilar',
            'cliteral' => 'Peluquería y cosmética capilar'
        ]);



        Ciclo::create([
            'id' => 22,
            'ciclo' => 'CFM SERV. RESTAURACIÓ (LOE)',
            'departamento' => 10,
            'responsable' => '',
            'tipo' => 1,
            'normativa' => 'LOE',
            'titol' => 'TÈCNIC EN SERVICIS EN RESTAURACIÓ',
            'rd' => 'RD 1690/2007 BOE 18-01-2008',
            'rd2' => 'l\'Orde de 29 de juliol de 2009 (DOGV 04/09/2009)',
            'vliteral' => 'Servicis en restauració',
            'cliteral' => 'Servicios en restauración'
        ]);



        Ciclo::create([
            'id' => 24,
            'ciclo' => 'CFM SMX  (LOE)',
            'departamento' => 24,
            'responsable' => '',
            'tipo' => 1,
            'normativa' => 'LOE',
            'titol' => 'TÈCNIC EN SISTEMES MICROINFORMÀTICS I XARXES',
            'rd' => 'RD 1691/2007, BOE 17-01-2008',
            'rd2' => 'l\'Orde de 29 de juliol de 2009 (DOGV 03/09/2009)',
            'vliteral' => 'Sistemes microinformàtics i xarxes',
            'cliteral' => 'Sistemas microinformáticos y redes'
        ]);



        Ciclo::create([
            'id' => 28,
            'ciclo' => 'CFS ADM. I FINANC. (LOE)',
            'departamento' => 5,
            'responsable' => '',
            'tipo' => 2,
            'normativa' => 'LOE',
            'titol' => 'TÈCNIC SUPERIOR EN ADMINISTRACIÓ I FINANCES.',
            'rd' => 'R.D.1584/2011 de 4 de novembre (BOE 15/12/2011)',
            'rd2' => 'l\'Orde 13/2015 de 5 de març de 2015 (DOGV 10/03/2015)',
            'vliteral' => 'Administració i finances',
            'cliteral' => 'Administración y finanzas'
        ]);



        Ciclo::create([
            'id' => 30,
            'ciclo' => 'CFS ASIX (LOE)',
            'departamento' => 24,
            'responsable' => '',
            'tipo' => 2,
            'normativa' => 'LOE',
            'titol' => 'TÈCNIC SUPERIOR EN ADMINISTRACIÓ DE SISTEMES INFORMÀTICS EN XARXA',
            'rd' => 'RD 1629/2009, BOE 18-11-2009',
            'rd2' => NULL,
            'vliteral' => 'Administració de sistemes informàtics en xarxa',
            'cliteral' => 'Administración de sistemas informáticos en red'
        ]);



        Ciclo::create([
            'id' => 32,
            'ciclo' => 'CFS DAM (LOE)',
            'departamento' => 24,
            'responsable' => '',
            'tipo' => 2,
            'normativa' => 'LOE',
            'titol' => 'TÈCNIC SUPERIOR EN DESENROTLLAMENT D\'APLICACIONS MULTIPLATAFORMA',
            'rd' => 'RD 450/2010, BOE 20-05-2010',
            'rd2' => 'l\'Orde 58/2012 de 5 de setembre de 2012 (DOGV 24/09/2012)',
            'vliteral' => 'Desenrotllament d\'aplicacions multiplataforma',
            'cliteral' => 'Desarrollo de aplicaciones multiplataforma'
        ]);



        Ciclo::create([
            'id' => 35,
            'ciclo' => 'CFS DAW (LOE)',
            'departamento' => 24,
            'responsable' => '',
            'tipo' => 2,
            'normativa' => 'LOE',
            'titol' => 'TÈCNIC SUPERIOR EN DESENROTLLAMENT D\'APLICACIONS WEB',
            'rd' => 'RD 686/2010, BOE 12-06-2010',
            'rd2' => NULL,
            'vliteral' => 'Desenrotllament d\'aplicacions web',
            'cliteral' => 'Desarrollo de aplicaciones  web'
        ]);



        Ciclo::create([
            'id' => 36,
            'ciclo' => 'CFS DIREC. CUINA (LOE)',
            'departamento' => 10,
            'responsable' => '',
            'tipo' => 2,
            'normativa' => 'LOE',
            'titol' => 'TÈCNIC SUPERIOR EN DIRECCIÓ DE CUINA',
            'rd' => 'RD 687/2010 BOE 12-06-2010',
            'rd2' => NULL,
            'vliteral' => 'Direcció de cuina',
            'cliteral' => 'Dirección de cocina'
        ]);



        Ciclo::create([
            'id' => 38,
            'ciclo' => 'CFS DIREC.RESTAURACIÓ (LOE)',
            'departamento' => 10,
            'responsable' => '',
            'tipo' => 2,
            'normativa' => 'LOE',
            'titol' => 'TÈCNIC SUPERIOR EN DIRECCIÓ DE SERVICIS DE RESTAURACIÓ',
            'rd' => 'RD 688/2010 BOE 12-06-2010',
            'rd2' => NULL,
            'vliteral' => 'Direcció en servicis de restauració',
            'cliteral' => 'Dirección en servicios de restauración'
        ]);



        Ciclo::create([
            'id' => 39,
            'ciclo' => 'CFS EDUC.INFANTIL (LOE)',
            'departamento' => 2,
            'responsable' => '',
            'tipo' => 2,
            'normativa' => 'LOE',
            'titol' => 'TÈCNIC SUPERIOR EN EDUCACIÓ INFANTIL.',
            'rd' => 'RD 1394/2007 BOE 24/11/2007',
            'rd2' => 'l\'Orde de 29 de juliol de 2009 (DOGV 02/09/2009)',
            'vliteral' => 'Educació infantil',
            'cliteral' => 'Educación infantil'
        ]);



        Ciclo::create([
            'id' => 42,
            'ciclo' => 'CFS ESTET.INTEG. (LOE)',
            'departamento' => 3,
            'responsable' => '',
            'tipo' => 2,
            'normativa' => 'LOE',
            'titol' => 'TÈCNIC SUPERIOR EN ESTÈTICA INTEGRAL I BENESTAR',
            'rd' => 'RD 881/2011, (BOE 23/07/2011)',
            'rd2' => 'l\'Orde 19/2015 de 5 de març de 2015 (DOGV 10/03/2015)',
            'vliteral' => 'Estètica integral i benestar',
            'cliteral' => 'Estética integral y bienestar'
        ]);



        Ciclo::create([
            'id' => 44,
            'ciclo' => 'CFS INTEGR.SOCIAL (LOE)',
            'departamento' => 2,
            'responsable' => '',
            'tipo' => 2,
            'normativa' => 'LOE',
            'titol' => 'TÈCNIC SUPERIOR EN INTEGRACIÓ SOCIAL',
            'rd' => 'RD 1074/2012 de 13 de juliol (BOE 15/08/12)',
            'rd2' => 'el Decret 29/2017, de 3 de març (DOGV 13/03/2017)',
            'vliteral' => 'Integració Social',
            'cliteral' => 'Integración Social'
        ]);



        Ciclo::create([
            'id' => 45,
            'ciclo' => 'CFS LABORATORI (LOE)',
            'departamento' => 6,
            'responsable' => '',
            'tipo' => 2,
            'normativa' => 'LOE',
            'titol' => 'TÈCNIC SUPERIOR EN LABORATORI CLÍNIC I BIOMÈDIC',
            'rd' => 'RD 771/2014 (BOE 4/10/2014)',
            'rd2' => NULL,
            'vliteral' => 'Laboratori clínic i biomèdic',
            'cliteral' => 'Laboratorio clinico y biomédico'
        ]);



        Ciclo::create([
            'id' => 46,
            'ciclo' => 'CFS LABORATORI (LOGSE)',
            'departamento' => 6,
            'responsable' => '',
            'tipo' => 2,
            'normativa' => 'LOGSE',
            'titol' => 'TÈCNIC SUPERIOR EN LABORATORI DE DIAGNÒSTIC CLÍNIC.',
            'rd' => 'RD 539/95 BOE 03-06-95',
            'rd2' => NULL,
            'vliteral' => 'Laboratori de diagnòstic clínic',
            'cliteral' => 'Laboratorio de diagnostico clínico'
        ]);



        Ciclo::create([
            'id' => 47,
            'ciclo' => 'CFS RX  (LOGSE)',
            'departamento' => 6,
            'responsable' => '',
            'tipo' => 2,
            'normativa' => 'LOGSE',
            'titol' => 'TÈCNIC SUPERIOR EN IMATGE PER AL DIAGNÒSTIC',
            'rd' => 'Reial Decret 545/1995 (BOE 12/06/95)',
            'rd2' => 'el Reial Decret 557/1995 (BOE 12/06/95)',
            'vliteral' => 'Imatge per al diagnòstic',
            'cliteral' => 'Imagen para el diagnóstico'
        ]);



        Ciclo::create([
            'id' => 51,
            'ciclo' => 'CFS RXMN (LOE)',
            'departamento' => 6,
            'responsable' => '',
            'tipo' => 2,
            'normativa' => 'LOE',
            'titol' => 'TÈCNIC SUPERIOR EN IMATGE PER AL DIAGNÒSTIC I MEDICINA NUCLEAR',
            'rd' => 'RD 770/2014, de 12 de setembre (BOE 04/10/2014)',
            'rd2' => NULL,
            'vliteral' => 'Imatge per al diagnòtic i medicina nuclear',
            'cliteral' => 'Imagen para el diagnóstico y medicina nuclear'
        ]);



        Ciclo::create([
            'id' => 54,
            'ciclo' => 'CFS SALUT AMBIENTAL (LOGSE)',
            'departamento' => 6,
            'responsable' => '',
            'tipo' => 2,
            'normativa' => 'LOGSE',
            'titol' => 'TÈCNIC SUPERIOR EN SALUT AMBIENTAL',
            'rd' => 'RD 540/95 BOE 10-06-95',
            'rd2' => NULL,
            'vliteral' => 'Salut ambiental',
            'cliteral' => 'Salud ambiental'
        ]);



        Ciclo::create([
            'id' => 55,
            'ciclo' => 'CFS TASOC (LOGSE)',
            'departamento' => 2,
            'responsable' => '',
            'tipo' => 2,
            'normativa' => 'LOGSE',
            'titol' => NULL,
            'rd' => NULL,
            'rd2' => NULL,
            'vliteral' => 'Animació Sociocultural',
            'cliteral' => 'Animación Sociocultural'
        ]);



        Ciclo::create([
            'id' => 56,
            'ciclo' => 'CFS TASOCIT (LOE)',
            'departamento' => 2,
            'responsable' => '',
            'tipo' => 2,
            'normativa' => 'LOE',
            'titol' => 'TÈCNIC SUPERIOR EN ANIMACIÓ SOCIOCULTURAL I TURÍSTICA',
            'rd' => 'RD 1684/2011 de 18 de novembre (BOE 27/12/11)',
            'rd2' => NULL,
            'vliteral' => 'Animació Sociocultural i turística',
            'cliteral' => 'Animación Sociocultural y turística'
        ]);
        Tipoincidencia::create([
            'id' => 1,
            'nombre' => 'fontanería',
            'nom' => 'fontanería'
        ]);



        Tipoincidencia::create([
            'id' => 2,
            'nombre' => 'electricidad',
            'nom' => 'electricitat'
        ]);



        Tipoincidencia::create([
            'id' => 3,
            'nombre' => 'carpintería',
            'nom' => 'fusteria'
        ]);



        Tipoincidencia::create([
            'id' => 4,
            'nombre' => 'obra',
            'nom' => 'obra'
        ]);



        Tipoincidencia::create([
            'id' => 5,
            'nombre' => 'estores',
            'nom' => 'estors'
        ]);



        Tipoincidencia::create([
            'id' => 6,
            'nombre' => 'cristalería',
            'nom' => 'cristalleria'
        ]);



        Tipoincidencia::create([
            'id' => 7,
            'nombre' => 'aluminio',
            'nom' => 'alumini'
        ]);



        Tipoincidencia::create([
            'id' => 8,
            'nombre' => 'mantenimiento interno',
            'nom' => 'manteniment intern'
        ]);



        Tipoincidencia::create([
            'id' => 9,
            'nombre' => 'general',
            'nom' => 'general'
        ]);



        Tipoincidencia::create([
            'id' => 10,
            'nombre' => 'otras',
            'nom' => 'altres'
        ]);

        Profesor::create([
            'dni' => '099999999Z',
            'codigo' => '9999',
            'nombre' => 'Admin',
            'apellido1' => 'Administrador',
            'apellido2' => '',
            'password' => 'MU8kVSFXbWf12',
            'emailItaca' => 'admin@intranet.my',
            'email' => 'admin@intranet.my',
            'domicilio' => '',
            'movil1' => '',
            'movil2' => '',
            'sexo' => '',
            'codigo_postal' => '',
            'departamento' => 1,
            'fecha_ingreso' => NULL,
            'fecha_nac' => NULL,
            'fecha_baja' => NULL,
            'fecha_ant' => NULL,
            'sustituye_a' => NULL,
            'foto' => NULL,
            'rol' => '66',
            'remember_token' => NULL,
            'created_at' => NULL,
            'updated_at' => NULL,
            'last_logged' => NULL,
            'activo' => 1,
            'idioma' => 'ca',
            'api_token' => '',
            'mostrar' => 0,
        ]);
        Hora::create([
            'codigo' => 1,
            'turno' => 'mati',
            'hora_ini' => '07:55',
            'hora_fin' => '08:50'
        ]);



        Hora::create([
            'codigo' => 2,
            'turno' => 'mati',
            'hora_ini' => '08:50',
            'hora_fin' => '09:45'
        ]);



        Hora::create([
            'codigo' => 3,
            'turno' => 'mati',
            'hora_ini' => '09:45',
            'hora_fin' => '10:40'
        ]);



        Hora::create([
            'codigo' => 4,
            'turno' => 'pati',
            'hora_ini' => '10:40',
            'hora_fin' => '11:00'
        ]);



        Hora::create([
            'codigo' => 5,
            'turno' => 'mati',
            'hora_ini' => '11:00',
            'hora_fin' => '11:55'
        ]);



        Hora::create([
            'codigo' => 6,
            'turno' => 'mati',
            'hora_ini' => '11:55',
            'hora_fin' => '12:50'
        ]);



        Hora::create([
            'codigo' => 7,
            'turno' => 'mati',
            'hora_ini' => '12:50',
            'hora_fin' => '13:45'
        ]);



        Hora::create([
            'codigo' => 8,
            'turno' => 'mati',
            'hora_ini' => '13:45',
            'hora_fin' => '14:40'
        ]);



        Hora::create([
            'codigo' => 9,
            'turno' => 'migdia',
            'hora_ini' => '14:40',
            'hora_fin' => '14:55'
        ]);



        Hora::create([
            'codigo' => 10,
            'turno' => 'vesprada',
            'hora_ini' => '14:55',
            'hora_fin' => '15:50'
        ]);



        Hora::create([
            'codigo' => 11,
            'turno' => 'vesprada',
            'hora_ini' => '15:50',
            'hora_fin' => '16:45'
        ]);



        Hora::create([
            'codigo' => 12,
            'turno' => 'vesprada',
            'hora_ini' => '16:45',
            'hora_fin' => '17:40'
        ]);



        Hora::create([
            'codigo' => 13,
            'turno' => 'pati',
            'hora_ini' => '17:40',
            'hora_fin' => '18:00'
        ]);



        Hora::create([
            'codigo' => 14,
            'turno' => 'vesprada',
            'hora_ini' => '18:00',
            'hora_fin' => '18:55'
        ]);



        Hora::create([
            'codigo' => 15,
            'turno' => 'vesprada',
            'hora_ini' => '18:55',
            'hora_fin' => '19:50'
        ]);



        Hora::create([
            'codigo' => 16,
            'turno' => 'vesprada',
            'hora_ini' => '19:50',
            'hora_fin' => '20:45'
        ]);



        Hora::create([
            'codigo' => 17,
            'turno' => 'vesprada',
            'hora_ini' => '20:45',
            'hora_fin' => '21:40'
        ]);



        Hora::create([
            'codigo' => 18,
            'turno' => 'vesprada',
            'hora_ini' => '21:40',
            'hora_fin' => '22:35'
        ]);
    }

}
