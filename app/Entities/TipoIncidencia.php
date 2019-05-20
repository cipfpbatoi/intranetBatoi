<?php


namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Profesor;
use App;

class TipoIncidencia extends Model
{
    use BatoiModels,TraitEstado;

    protected $table = 'tipoincidencias';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nombre',
        'nom',
        'idProfesor',
        'tipus'];

    protected $inputTypes = [
        'idProfesor' => ['type' => 'select'],
        'tipus' => ['type' => 'select']
    ];

    protected $rules = [
        'id' => 'required:numeric',
        'nombre' => 'required',
        'nom' => 'required',
        'idProfesor' => 'required',
        'tipus' => 'required',
    ];

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
        return hazArray($this->Rol(config('roles.rol.mantenimiento')),'dni','fullName');
    }

    public function getTipusOptions(){
        return config('auxiliares.tipoIncidencia');
    }

    public function Rol($rol){
        $profesores = collect();
        foreach (Profesor::where('activo',1)->get() as $profe)
        if ($profe->rol % $rol == 0)
            $profesores->push($profe);
        return $profesores;
    }
    public function getProfesorAttribute(){
        return isset($this->Responsable->fullName)?$this->Responsable->fullName:'';
    }

}
