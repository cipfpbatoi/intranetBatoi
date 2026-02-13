<?php


namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class TipoIncidencia extends Model
{
    use \Intranet\Entities\Concerns\BatoiModels;

    protected $table = 'tipoincidencias';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nombre',
        'nom',
        'idProfesor',
        'tipus'];


    public function getLiteralAttribute()
    {
        return App::getLocale(session('lang')) == 'es' ? $this->nombre : $this->nom;
    }

    public function getTipoAttribute()
    {
        return $this->tipus?config('auxiliares.tipoIncidencia')[$this->tipus]:'';
    }

    public function Responsable()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }

    public function getIdProfesorOptions()
    {
        return hazArray($this->Rol(config('roles.rol.mantenimiento')), 'dni', 'fullName');
    }

    public function getTipusOptions()
    {
        return config('auxiliares.tipoIncidencia');
    }

    public function Rol($rol)
    {
        $profesores = collect();
        foreach (Profesor::where('activo', 1)->get() as $profe) {
            if ($profe->rol % $rol == 0) {
                $profesores->push($profe);
            }
        }
        return $profesores;
    }
    public function getProfesorAttribute()
    {
        return $this->Responsable->fullName??'';
    }

}
