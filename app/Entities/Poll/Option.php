<?php

namespace Intranet\Entities\Poll;

use Intranet\Entities\Concerns\BatoiModels;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use \Intranet\Entities\Concerns\BatoiModels;
    
    protected $fillable = ['question','scala','ppoll_id' ];
    protected $rules = [
        'ppoll_id' => 'required',
        'question' => 'required',
        'scala' => 'numeric|between:0,10',
    ];
    protected $inputTypes = [
        'ppoll_id' => ['disabled' => 'disabled'],
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
        return $this->poll->activo;
    }
}
