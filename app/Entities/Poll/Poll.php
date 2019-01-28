<?php

namespace Intranet\Entities\Poll;


use Intranet\Entities\BatoiModels;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    use BatoiModels;
    
    protected $fillable = ['title','activo' ];
    protected $rules = [
        'title' => 'required',
    ];
    protected $inputTypes = [
        'activo' => ['type' => 'checkbox']
    ];
    public $timestamps = false;
    
    /**
     * A poll has many options related to
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function options()
    {
        return $this->hasMany(Option::class);
    }

    
    /**
     * Get all of the votes for the poll.
     */
    public function votes()
    {
        return $this->hasManyThrough(Vote::class, Option::class);
    }
    public function getActiuAttribute(){
        return $this->activo?'Activa':'No activa';
    }
}
