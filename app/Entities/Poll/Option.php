<?php

namespace Intranet\Entities\Poll;

use Intranet\Entities\BatoiModels;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use BatoiModels;
    
    protected $fillable = ['question','scala','poll_id' ];
    protected $rules = [
        'poll_id' => 'required',
        'question' => 'required',
        'scala' => 'number|max:10',
    ];
    protected $inputTypes = [
        'poll_id' => ['disabled' => 'disabled'],
    ];
    public $timestamps = false;
    /**
     * An option belongs to one poll
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Check if the option is Closed
     *
     * @return bool
     */
    public function isPollClosed()
    {
        return $this->poll->isLocked();
    }
}
