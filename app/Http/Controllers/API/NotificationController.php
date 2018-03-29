<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\Comision;
use Illuminate\Http\Request;
use \DB;
use Jenssegers\Date\Date;
use Intranet\Entities\Notification;

class NotificationController extends ApiBaseController
{

    protected $model = 'Notification';

    public function leer($id)
    {
        $notification = Notification::find($id);
        $notification->read_at = New Date('now');
        $notification->save();
        return $this->sendResponse(['updated' => true], 'OK');
    }

}
