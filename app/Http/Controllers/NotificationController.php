<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Notification\NotificationInboxService;
use Intranet\Http\Controllers\Core\IntranetController;

use Intranet\UI\Botones\BotonImg;
use Intranet\Services\UI\AppAlert as Alert;

/**
 * Class NotificationController
 * @package Intranet\Http\Controllers
 */
class NotificationController extends IntranetController
{
    private ?NotificationInboxService $notificationInboxService = null;

    /**
     * @var string
     */
    protected $model = 'Notification';
    /**
     * @var array
     */
    protected $gridFields = ['fecha','emisor', 'motivo'];
    // Use el layout de notificacions personalitzat per a la vista show
    protected $vista = ['show' => 'notification'];

    private function inbox(): NotificationInboxService
    {
        if ($this->notificationInboxService === null) {
            $this->notificationInboxService = app(NotificationInboxService::class);
        }

        return $this->notificationInboxService;
    }

    /**
     * @return mixed
     */
    protected function search()
    {
        return $this->inbox()->listForUser(AuthUser());
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function read($id)
    {
        $this->inbox()->markAsRead($id);
        return back();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function readAll()
    {
        $this->inbox()->markAllAsRead(AuthUser());
        return back();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAll()
    {
        $this->inbox()->deleteAll(AuthUser());
        return back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $this->inbox()->deleteById($id);
        return back();
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBotonera(['deleteAll', 'readAll'], ['delete','show']);
        $this->panel->setBoton('grid', new BotonImg('notification.read', [ 'where' => ['read_at', 'isNull','']]));
    }


    /**
     * @param $id
     */
    public function show($id)
    {
        $modelo = $this->model;
        $elemento = $this->inbox()->findForShow($id);
        if (!$elemento) {
            Alert::danger('Notificació no trobada');
            return back();
        }

        return view($this->chooseView('show'), compact('elemento', 'modelo'));
    }
}
