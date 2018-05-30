<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestricciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('materiales',function (Blueprint $table){
            $table->foreign('espacio')->references('aula')->on('espacios')->onUpdate('cascade');
        });
        Schema::table('actividad_grupo', function (Blueprint $table) {
            $table->unsignedInteger('idActividad')->change();
            $table->foreign('idGrupo')->references('codigo')->on('grupos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('idActividad')->references('id')->on('actividades')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('actividad_profesor', function (Blueprint $table) {
            $table->unsignedInteger('idActividad')->change();
            $table->foreign('idActividad')->references('id')->on('actividades')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('idProfesor')->references('dni')->on('profesores')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('alumnos_grupos', function (Blueprint $table) {
            $table->foreign('idAlumno')->references('nia')->on('alumnos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('idGrupo')->references('codigo')->on('grupos')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('alumnos_cursos', function (Blueprint $table) {
            $table->unsignedInteger('idCurso')->change();
            $table->foreign('idAlumno')->references('nia')->on('alumnos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('idCurso')->references('id')->on('cursos')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('faltas', function (Blueprint $table) {
            $table->foreign('idProfesor')->references('dni')->on('profesores')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('guardias', function (Blueprint $table) {
            $table->foreign('idProfesor')->references('dni')->on('profesores')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('reservas', function (Blueprint $table) {
            $table->foreign('idProfesor')->references('dni')->on('profesores')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('idEspacio')->references('aula')->on('espacios')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('espacios', function (Blueprint $table) {
            $table->foreign('departamento')->references('id')->on('departamentos')->onUpdate('cascade');
        });
        Schema::table('incidencias', function (Blueprint $table) {
            $table->foreign('espacio')->references('aula')->on('espacios')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('idProfesor')->references('dni')->on('profesores')->onUpdate('cascade');
            $table->foreign('tipo')->references('id')->on('tipoincidencias')->onUpdate('cascade');
       });
       Schema::table('ciclos', function (Blueprint $table) {
           //$table->tinyInteger('departamento')->change();
           $table->foreign('departamento')->references('id')->on('departamentos')->onUpdate('cascade');
        });
        Schema::table('programaciones', function (Blueprint $table) {
            $table->foreign('idProfesor')->references('dni')->on('profesores')->onUpdate('cascade');
        });
        Schema::table('expedientes', function (Blueprint $table) {
            $table->foreign('idProfesor')->references('dni')->on('profesores')->onUpdate('cascade');
            $table->foreign('idAlumno')->references('nia')->on('alumnos')->onUpdate('cascade');
        });
        Schema::table('reuniones', function (Blueprint $table) {
            $table->foreign('idProfesor')->references('dni')->on('profesores')->onUpdate('cascade');
        });
        Schema::table('ordenes_reuniones', function (Blueprint $table) {
            $table->unsignedInteger('idReunion')->change();
            $table->foreign('idReunion')->references('id')->on('reuniones')->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::table('miembros', function (Blueprint $table) {
            $table->unsignedInteger('idGrupoTrabajo')->change();
            $table->foreign('idProfesor')->references('dni')->on('profesores')->onUpdate('cascade');
            $table->foreign('idGrupoTrabajo')->references('id')->on('grupos_trabajo')->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::table('asistencias', function (Blueprint $table) {
            $table->unsignedInteger('idReunion')->change();
            $table->foreign('idProfesor')->references('dni')->on('profesores')->onUpdate('cascade');
            $table->foreign('idReunion')->references('id')->on('reuniones')->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::table('resultados', function (Blueprint $table) {
            $table->foreign('idProfesor')->references('dni')->on('profesores')->onUpdate('cascade');
            $table->foreign('idGrupo')->references('codigo')->on('grupos')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idModulo')->references('codigo')->on('modulos')->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::table('centros', function (Blueprint $table) {
            $table->unsignedInteger('idEmpresa')->change();
            $table->foreign('idEmpresa')->references('id')->on('empresas')->onUpdate('cascade');
         });
         Schema::table('colaboraciones', function (Blueprint $table) {
            $table->unsignedInteger('idCentro')->change();
            $table->unsignedInteger('idCiclo')->change();
            $table->foreign('idCentro')->references('id')->on('centros')->onUpdate('cascade');
            $table->foreign('idCiclo')->references('id')->on('ciclos')->onUpdate('cascade');
         });
         Schema::table('fcts', function (Blueprint $table) {
            $table->unsignedInteger('idColaboracion')->change();
            $table->foreign('idAlumno')->references('nia')->on('alumnos')->onUpdate('cascade');
            $table->foreign('idColaboracion')->references('id')->on('colaboraciones')->onUpdate('cascade');
         });
            Schema::table('tutorias_grupos', function (Blueprint $table) {
                $table->unsignedInteger('idTutoria')->change();
                $table->foreign('idTutoria')->references('id')->on('tutorias')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('idGrupo')->references('codigo')->on('grupos')->onDelete('cascade')->onUpdate('cascade');
            });
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('materiales',function (Blueprint $table){
            $table->dropForeign('materiales_espacio_foreign');
        });
        Schema::table('actividad_grupo',function (Blueprint $table){
            $table->dropForeign('actividad_grupo_idGrupo_foreign');
            $table->dropForeign('actividad_grupo_idActividad_foreign');
        });
        Schema::table('actividad_profesor',function (Blueprint $table){
            $table->dropForeign('actividad_profesor_idProfesor_foreign');
            $table->dropForeign('actividad_profesor_idActividad_foreign');
        });
        Schema::table('alumnos_grupos',function (Blueprint $table){
            $table->dropForeign('alumnos_grupos_idAlumno_foreign');
            $table->dropForeign('alumnos_grupos_idGrupo_foreign');
        });
        Schema::table('alumnos_cursos',function (Blueprint $table){
            $table->dropForeign('alumnos_cursos_idAlumno_foreign');
            $table->dropForeign('alumnos_cursos_idCurso_foreign');
        });
        Schema::table('programaciones',function (Blueprint $table){
            $table->dropForeign('programaciones_idProfesor_foreign');
        });
        Schema::table('faltas',function (Blueprint $table){
            $table->dropForeign('faltas_idProfesor_foreign');
        });
        Schema::table('guardias',function (Blueprint $table){
            $table->dropForeign('guardias_idProfesor_foreign');
        });
        Schema::table('reservas',function (Blueprint $table){
            $table->dropForeign('reservas_idProfesor_foreign');
            $table->dropForeign('reservas_idEspacio_foreign');
        });
        Schema::table('espacios',function (Blueprint $table){
            $table->dropForeign('espacios_departamento_foreign');
        });
        Schema::table('incidencias',function (Blueprint $table){
            $table->dropForeign('incidencias_espacio_foreign');
            $table->dropForeign('incidencias_idProfesor_foreign');
            $table->dropForeign('incidencias_tipo_foreign');
        });
        Schema::table('ciclos',function (Blueprint $table){
            $table->dropForeign('ciclos_departamento_foreign');
        });
        Schema::table('expedientes',function (Blueprint $table){
            $table->dropForeign('expedientes_idProfesor_foreign');
            $table->dropForeign('expedientes_idAlumno_foreign');
        });
        Schema::table('reuniones',function (Blueprint $table){
            $table->dropForeign('reuniones_idProfesor_foreign');
        });
        Schema::table('ordenes_reuniones',function (Blueprint $table){
            $table->dropForeign('ordenes_reuniones_idReunion_foreign');
        });
        Schema::table('miembros',function (Blueprint $table){
            $table->dropForeign('miembros_idProfesor_foreign');
             $table->dropForeign('miembros_idGrupoTrabajo_foreign');
        });
        Schema::table('asistencias',function (Blueprint $table){
            $table->dropForeign('asistencias_idProfesor_foreign');
             $table->dropForeign('asistencias_idReunion_foreign');
        });
        Schema::table('resultados',function (Blueprint $table){
            $table->dropForeign('resultados_idProfesor_foreign');
            $table->dropForeign('resultados_idGrupo_foreign');
            $table->dropForeign('resultados_idModulo_foreign');
        });
        Schema::table('centros',function (Blueprint $table){
            $table->dropForeign('centros_idEmpresa_foreign');
        });
        Schema::table('colaboraciones',function (Blueprint $table){
            $table->dropForeign('colaboraciones_idCentro_foreign');
            $table->dropForeign('colaboraciones_idCiclo_foreign');
        });
        Schema::table('fcts',function (Blueprint $table){
            $table->dropForeign('fcts_idAlumno_foreign');
            $table->dropForeign('fcts_idColaboracion_foreign');
        });
        Schema::table('tutorias_grupos',function (Blueprint $table){
            $table->dropForeign('tutorias_grupos_idTutoria_foreign');
            $table->dropForeign('tutorias_grupos_idGrupo_foreign');
        });
    }
}
