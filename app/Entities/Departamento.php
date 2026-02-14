<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Departamento extends Model
{
    use \Intranet\Entities\Concerns\BatoiModels;

    public $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'id', 'cliteral', 'vliteral', 'idProfesor','depcurt', 'didactico' ];
    protected $inputTypes = [ 'didactico' => ['type' => 'checkbox'] ];

    public function Profesor()
    {
        return $this->hasMany(Profesor::class, 'departamento', 'id');
    }
    public function Modulo()
    {
        return $this->belongstoMany(Modulo::class, 'modulo_ciclos', 'idDepartamento', 'idModulo');
    }
    public function Jefe()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }
    
    public function getLiteralAttribute()
    {
        return App::getLocale(session('lang')) == 'es' ? $this->cliteral : $this->vliteral;
    }

    public function getidProfesorOptions()
    {
        return hazArray(Profesor::where('activo',1)->orderBy('departamento')->orderBy('apellido1')->get(), 'dni', 'nameFull');
    }

}
