<?php
namespace Intranet\Entities\Poll;

use Intranet\Entities\BatoiModels;
use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Grupo;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Poll\Option;

class Vote extends Model
{
    protected $table = 'votes';
    
    public function Option()
    {
        return $this->belongsTo(Option::class, 'option_id','id');
    }
    public function ModuloGrupo(){
        return $this->belongsTo(Modulo_grupo::class,'idModuloGrupo','id');
    }
    
    public function getIsValueAttribute()
    {
        return $this->Option->scala;
    }
    private function optionsPoll($id){
        return hazArray(Poll::find($id)->options,'id');
    }
    public function scopeMyVotes($query,$id,$modulo){
        return $query->where('idProfesor', AuthUser()->dni)->whereIn('option_id',$this->optionsPoll($id))
            ->where('idModuloGrupo',$modulo);
    }
    public function scopeMyGroupVotes($query,$id,$grup){
        $grupo = Grupo::find($grup);
        return $query->whereIn('idModuloGrupo',hazArray(Grupo::find($grup)->Modulos,'id'))
            ->whereIn('option_id',$this->optionsPoll($id));
    }
}
