<?php

namespace Intranet\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

/**
 * Event de registre d'activitat.
 */
class ActivityReport
{

    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    /**
     * @var Model
     */
    public $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

}
