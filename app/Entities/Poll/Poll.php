<?php

namespace Intranet\Entities\Poll;


use Intranet\Entities\BatoiModels;

class Poll extends Model
{
    use BatoiModels;
    
    protected $fillable = ['question','isClosed' ];
    protected $rules = [
        'question' => 'required',
    ];
    protected $inputTypes = [
        'isClosed' => ['type' => 'checkbox']
    ];
    
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
}
