<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;
use Intranet\Events\ActivityReport;

class Tutoria extends Model
{

    use BatoiModels;

    protected $table = 'tutorias';
    public $timestamps = false;
    protected $fillable = [
        'descripcion',
        'obligatoria',
        'grupos',
        'desde',
        'hasta',
        'fichero',
        
    ];
    protected $rules = [
        'desde' => 'required|date',
        'hasta' => 'required|date',
        'descripcion' => 'required',
        'grupos' => 'required',
        ];
    protected $inputTypes = [
        'obligatoria' => ['type' => 'checkbox'],
        'fichero' => ['type' => 'file'],
        'desde' => ['type' => 'date'],
        'hasta' => ['type' => 'date'],
        'grupos' => ['type' => 'select'],
    ];
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];
    
    public function getDesdeAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }

    public function getHastaAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }
    public function getGruposOptions()
    {
        return config('constants.tipoTutoria');
    }
    
    protected function getXobligatoriaAttribute(){
        return $this->obligatoria ? 'X' : '-';
    }
    protected function getGrupoAttribute(){
        return config('constants.tipoTutoria')[$this->grupos];
    }
    
}
