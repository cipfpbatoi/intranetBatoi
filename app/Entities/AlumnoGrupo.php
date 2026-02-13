<?php

namespace Intranet\Entities;

use \Illuminate\Database\Eloquent\Model;

class AlumnoGrupo extends Model
{

    use \Intranet\Entities\Concerns\BatoiModels;

    public $primaryKey = 'idAlumno';
    protected $keyType = 'string';
    protected $table = 'alumnos_grupos';
    public $timestamps = false;

    protected $rules = [
        'subGrupo' => 'required|max:1',
        'posicion' => 'max:2'
    ];
    protected $fillable = [
        'subGrupo',
        'posicion'];

    protected $inputTypes = [

        ];
    
    /**
     * Troba un registre d'alumne-grup.
     *
     * Compatibilitat:
     * - `find([$idAlumno, $idGrupo])` per clau composta funcional.
     * - `find($id)` delega al comportament estàndard d'Eloquent.
     *
     * @param mixed $params
     * @param array|string $columns
     * @return static|\Illuminate\Database\Eloquent\Collection<static>|null
     */
    public static function find($params, $columns = ['*'])
    {
        if (is_array($params) && count($params) >= 2) {
            return static::where('idAlumno', $params[0])
                ->where('idGrupo', $params[1])
                ->first($columns);
        }

        return static::query()->find($params, $columns);
    }

    public function Alumno()
    {
        return $this->belongsTo(Alumno::class, 'idAlumno', 'nia');
    }
    public function Grupo()
    {

        return $this->belongsTo(Grupo::class, 'idGrupo', 'codigo');
    }
    public function getNombreAttribute()
    {
        return $this->Alumno->NameFull;
    }
    public function getPoblacionAttribute()
    {
        return $this->Alumno->Poblacion;
    }
    public function getEmailAttribute()
    {
        return $this->Alumno->email;
    }
    public function getTelef2Attribute()
    {
        return $this->Alumno->telef2;
    }
    public function getTelef1Attribute()
    {
        return $this->Alumno->telef1;
    }

    public function getFolAttribute()
    {
        return $this->Alumno->fol;
    }
    public function getFotoAttribute()
    {
        return $this->Alumno->foto;
    }

    public function getDretsAttribute()
    {
        return $this->Alumno->imageRightAccept?'Sí':'No';
    }

    public function getExtraescolarsAttribute()
    {
        return $this->Alumno->outOfSchoolActivityAccept?'Sí':'No';
    }

    public function getDAAttribute()
    {
        return $this->Alumno->DA?'Sí':'No';
    }

}
