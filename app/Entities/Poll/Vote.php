<?php
namespace Intranet\Entities\Poll;

use Intranet\Entities\BatoiModels;
use Illuminate\Database\Eloquent\Model;
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
    public function scopeMyVotes($query,$enquesta){
        $enquesta = Poll::find($enquesta);
        $options = hazArray($enquesta->options,'id');
        return $query->where('idProfesor', AuthUser()->dni)->whereIn('option_id',$options);
    }
}
