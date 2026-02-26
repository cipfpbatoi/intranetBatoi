<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Intranet\Events\ActivityReport;
use Intranet\Presentation\Crud\TutoriaGrupoCrudSchema;

class TutoriaGrupo extends Model
{

    use \Intranet\Entities\Concerns\BatoiModels;

    protected $table = 'tutorias_grupos';
    public $timestamps = false;
    protected $fillable = [
        'idTutoria',
        'idGrupo',
        'observaciones',
        'fecha',
    ];

    protected $rules = TutoriaGrupoCrudSchema::RULES;
    protected $inputTypes = TutoriaGrupoCrudSchema::INPUT_TYPES;
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];
    protected $visible = [
        'observaciones'
    ];
    
    public function getFechaAttribute($entrada)
    {
        if (empty($entrada)) {
            return '';
        }
        $fecha = new Carbon($entrada);
        return $fecha->format('d-m-Y');
    }
    public function getNombreAttribute(){
        return $this->Grupo->nombre ?? '';
    }
    public function Grupo()
    {
        return $this->belongsTo(Grupo::class, 'idGrupo', 'codigo');
    }

}
