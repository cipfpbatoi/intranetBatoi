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

    use BatoiModels;

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
        return $this->hasMany(Horario::class, 'codigo', 'modulo');
    }
    
    public function Grupos(){
        return $this->belongsToMany(Grupo::class,'modulo_grupos','idGrupo','idModulo','codigo');
    }
    
    public function scopeMisModulos($query, $profesor = null)
    {
        $profesor = $profesor ? $profesor : AuthUser();
        $modulos = Horario::select('modulo')
                ->Profesor(AuthUser()->dni)
                ->whereNotNull('idGrupo')
                ->distinct()
                ->get()
                ->toarray();
        return $query->whereIn('codigo', $modulos);
    }
    
    public function scopeModulosGrupo($query,$grupo){
        $modulos = Horario::select('modulo')
                ->Grup($grupo)
                ->distinct()
                ->get()
                ->toarray();
        return $query->whereIn('codigo', $modulos);
    }
    
    public function scopeLectivos($query)
    {
        return $query->whereNotIn('codigo',config('constants.modulosNoLectivos'));
    }
    public function getliteralAttribute()
    {
        return App::getLocale(session('lang')) == 'es' ? $this->cliteral : $this->vliteral;
    }
}
