<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Application\Notification\NotificationInboxService;

class NotificationController extends ApiResourceController
{

    protected $model = 'Notification';

    public function leer($id)
    {
        if (!app(NotificationInboxService::class)->markAsRead($id)) {
            return $this->sendResponse(['updated' => false], 'Notificació no trobada');
        }
        return $this->sendResponse(['updated' => true], 'OK');
    }

}
