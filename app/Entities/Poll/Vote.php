<?php
namespace Intranet\Entities\Poll;

use Intranet\Entities\BatoiModels;
use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Poll\Option;

class Vote extends Model
{
    protected $table = 'votes';
    
    public function Option()
    {
        return $this->belongsTo(Option::class, 'option_id','id');
    }
    
    public function getIsValueAttribute()
    {
        return $this->Option->scala;
    }
}
