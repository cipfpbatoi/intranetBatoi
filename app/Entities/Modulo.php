<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Intranet\Events\ActivityReport;

class Modulo extends Model
{

    public $primaryKey = 'codigo';
    public $timestamps = false;
    public $keyType = 'string';

    use \Intranet\Entities\Concerns\BatoiModels;

    protected $fillable = [
        'cliteral',
        'vliteral',
    ];
    protected $rules = [
        'cliteral' => 'required',
        'vliteral' => 'required',
    ];
    protected $inputTypes = [
    ];
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];

    public function Horario()
    {
        return $this->hasMany(Horario::class, 'modulo', 'codigo');
    }
    
    
    public function Grupos(){
        return $this->belongsToMany(Grupo::class,'modulo_grupos','idGrupo','idModulo','codigo');
    }
    
    public function scopeMisModulos($query, $profesor = null)
    {
        $dni = is_string($profesor) ? $profesor : (($profesor->dni ?? null) ?: authUser()->dni);

        $modulos = Horario::query()
            ->Profesor($dni)
            ->whereNotNull('idGrupo')
            ->distinct()
            ->pluck('modulo')
            ->filter()
            ->values()
            ->all();

        return $query->whereIn('codigo', $modulos);
    }
    
    public function scopeModulosGrupo($query,$grupo){
        $modulos = Horario::query()
            ->Grup($grupo)
            ->distinct()
            ->pluck('modulo')
            ->filter()
            ->values()
            ->all();

        return $query->whereIn('codigo', $modulos);
    }
    
    public function scopeLectivos($query)
    {
        return $query->whereNotIn('codigo',config('constants.modulosNoLectivos'));
    }
    public function getliteralAttribute()
    {
        $lang = session('lang', App::getLocale());
        return $lang === 'es' ? $this->cliteral : $this->vliteral;
    }
}
