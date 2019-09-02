<?php

namespace Intranet\Entities\Poll;


use Intranet\Entities\BatoiModels;
use Illuminate\Database\Eloquent\Model;
use Intranet\Events\ActivityReport;

class PPoll extends Model
{
    use BatoiModels;

    protected $table = 'ppolls';
    protected $fillable = ['title','who','what','anonymous'];
    protected $rules = [
        'title' => 'required',
    ];
    protected $inputTypes = [
        'who' => ['type' => 'select'],
        'what' => ['type' => 'select'],
        'anonymous' => ['type' => 'checkbox']
    ];
    public $timestamps = false;
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];
    
    /**
     * A poll has many options related to
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function options()
    {
        return $this->hasMany(Option::class,'ppoll_id');
    }
    public function getQuienAttribute(){
        return config('auxiliares.who')[$this->who];
    }
    public function getQueAttribute(){
        return config('auxiliares.what')[$this->what];
    }

    public function getWhoOptions(){
        return config('auxiliares.who');
    }
    public function getWhatOptions(){
        return config('auxiliares.what');
    }

}
