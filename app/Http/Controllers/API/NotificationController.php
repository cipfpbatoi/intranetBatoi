<?php

namespace Intranet\Http\Controllers\API;

use Jenssegers\Date\Date;
use Intranet\Entities\Notification;

class NotificationController extends ApiResourceController
{

    protected $model = 'Notification';

    public function leer($id)
    {
        $notification = Notification::find($id);
        if (!$notification) {
            return $this->sendResponse(['updated' => false], 'Notificació no trobada');
        }

        $notification->read_at = new Date('now');
        $notification->save();
        return $this->sendResponse(['updated' => true], 'OK');
    }

}
