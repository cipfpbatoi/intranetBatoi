<?php


namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Intranet\Presentation\Crud\TipoIncidenciaCrudSchema;

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
    protected $inputTypes = TipoIncidenciaCrudSchema::INPUT_TYPES;


    public function getLiteralAttribute()
    {
        return App::getLocale() === 'es' ? $this->nombre : $this->nom;
    }

    public function getTipoAttribute()
    {
        if (!$this->tipus) {
            return '';
        }

        return config("auxiliares.tipoIncidencia.{$this->tipus}") ?? '';
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
        return Profesor::query()
            ->where('activo', 1)
            ->get()
            ->filter(static fn ($profe) => $profe->rol % $rol === 0)
            ->values();
    }
    public function getProfesorAttribute()
    {
        return $this->Responsable->fullName??'';
    }

}
