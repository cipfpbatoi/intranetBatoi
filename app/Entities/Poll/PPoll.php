<?php

namespace Intranet\Entities\Poll;


use Intranet\Entities\Concerns\BatoiModels;
use Illuminate\Database\Eloquent\Model;
use Intranet\Events\ActivityReport;

class PPoll extends Model
{
    use \Intranet\Entities\Concerns\BatoiModels;

    protected $table = 'ppolls';
    protected $fillable = ['title','what','anonymous','remains'];
    protected $rules = [
        'title' => 'required',
        'what' => 'required'
    ];
    protected $inputTypes = [
        'what' => ['type' => 'select'],
        'anonymous' => ['type' => 'checkbox'],
        'remains' => ['type'=>'checkbox']
    ];
    public $timestamps = false;


    public function polls()
    {
        return $this->hasMany(Poll::class, 'idPPoll');
    }

    
    /**
     * A poll has many options related to
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function options()
    {
        return $this->hasMany(Option::class, 'ppoll_id');
    }

    public function getWhatOptions()
    {
        return config('auxiliares.modelsAvailablePoll');
    }

}
