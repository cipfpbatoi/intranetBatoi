<?php

namespace Intranet\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ActivityReport
{

    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    public $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

}
