<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\Comision;
use Illuminate\Http\Request;
use \DB;
use Carbon\Carbon;
use Intranet\Entities\Notification;

class NotificationController extends ApiBaseController
{

    protected $model = 'Notification';

    public function leer($id)
    {
        $notification = Notification::find($id);
        $notification->read_at =  Carbon::parse('now');
        $notification->save();
        return $this->sendResponse(['updated' => true], 'OK');
    }

}
