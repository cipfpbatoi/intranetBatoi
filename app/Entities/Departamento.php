<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

/** Departament docent i metadades de la família professional associada. */
class Departamento extends Model
{
    use \Intranet\Entities\Concerns\BatoiModels;

    public $primaryKey = 'id';
    public $timestamps = false;
    /** @var list<string> Atributs editables del departament i la seua família professional. */
    protected $fillable = [
        'id', 'cliteral', 'vliteral', 'idProfesor','depcurt', 'didactico',
        'familia_professional_val', 'familia_professional_cas', 'codigo_xml', 'abreviatura_xml' ];
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
