<?php
namespace Intranet\Entities\Poll;


use Illuminate\Database\Eloquent\Model;


class VoteAnt extends Model
{
    protected $table = 'colaboracion_votes';
    protected $fillable = ['option_id','idColaboracion','text','value','curs'];
    public $timestamps = false;


    public function Option()
    {
        return $this->belongsTo(Option::class, 'option_id', 'id');
    }
    public function Colaboracion()
    {
        return $this->belongsTo(Colaboracion::class, 'idColaboracion', 'id');
    }

    public function getIsValueAttribute()
    {
        return $this->Option->scala;
    }
    public function getQuestionAttribute()
    {
        return $this->Option->question;
    }
    public function getAnswerAttribute()
    {
        if ($this->isValue) {
            return $this->value;
        }
        return $this->text;
    }


}
