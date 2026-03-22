<?php

namespace Intranet\Entities;

use \Illuminate\Database\Eloquent\Model;

/**
 * Model de notes i valoracions per alumne i mòdul.
 *
 * @property int $id
 * @property string $idAlumno
 * @property int $idModuloGrupo
 * @property int $nota
 * @property int $valoraciones
 * @property string|null $observaciones
 */
class AlumnoResultado extends Model
{

    protected $table = 'alumno_resultados';
    public $timestamps = false;

    use \Intranet\Entities\Concerns\BatoiModels;

    protected $fillable = [
        'idAlumno',
        'idModuloGrupo',
        'nota',
        'valoraciones',
        'observaciones'
    ];
    protected $rules = [
        'idAlumno' => 'required',
        'idModuloGrupo' => 'required',
        'nota' => 'nullable|integer|min:0|max:13',
        'observaciones' => 'max:200',
      ];
    protected $inputTypes = [
        'idModuloGrupo' => ['type' => 'hidden'],
        'idAlumno' => ['type' => 'select'],
        'nota' => ['type' => 'select'],
        'valoraciones' => ['type' => 'select'],
    ];
    protected $attributes = [
        'nota' => 0,
    ];
    

    public function Alumno()
    {
        return $this->belongsTo(Alumno::class, 'idAlumno', 'nia');
    }
    public function ModuloGrupo()
    {
        return $this->belongsTo(Modulo_grupo::class, 'idModuloGrupo', 'id');
    }
    public function getNombreAttribute()
    {
        return $this->Alumno->NameFull;
    }

    public function getidAlumnoOptions()
    {
        $alumnos_rellenos = hazArray(AlumnoResultado::where('idModuloGrupo',$this->idModuloGrupo)->get(),'idAlumno');
        return hazArray($this->ModuloGrupo->Grupo->Alumnos->whereNotIn('nia',$alumnos_rellenos),'nia','fullName');
    }

   

    public function getValoracionAttribute()
    {
        return config('auxiliares.valoraciones')[$this->valoraciones];
    }

    public function getModuloAttribute()
    {
        return $this->ModuloGrupo->Xmodulo;
    }


}
