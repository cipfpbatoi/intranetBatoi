<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Profesor\ProfesorService;
use Intranet\Http\Controllers\Core\IntranetController;

use Intranet\Entities\Notification;
use Intranet\UI\Botones\BotonImg;
use Jenssegers\Date\Date;
use Intranet\Entities\Alumno;
use Styde\Html\Facades\Alert;

/**
 * Class NotificationController
 * @package Intranet\Http\Controllers
 */
class NotificationController extends IntranetController
{
    private ?ProfesorService $profesorService = null;

    /**
     * @var string
     */
    protected $model = 'Notification';
    /**
     * @var array
     */
    protected $gridFields = ['fecha','emisor', 'motivo'];
    /**
     * @var
     */
    protected $key;
    /**
     * @var array
     */
    // Use el layout de notificacions personalitzat per a la vista show
    protected $vista = ['show' => 'notification'];

    private function profesores(): ProfesorService
    {
        if ($this->profesorService === null) {
            $this->profesorService = app(ProfesorService::class);
        }

        return $this->profesorService;
    }

    /**
     * @return mixed
     */
    protected function search()
    {
        $userKey = AuthUser()->primaryKey;
        return Notification::where('notifiable_id', "=", AuthUser()->$userKey)
                ->orderBy('created_at', 'desc')
                ->get();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function read($id)
    {
        $notification = Notification::find($id);
        $notification->read_at = new Date('now');
        $notification->save();
        return back();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function readAll()
    {
        $userKey = AuthUser()->primaryKey;
        if ($userKey == 'dni') {
            $user = $this->profesores()->find((string) AuthUser()->$userKey);
        } else {
            $user = Alumno::find(AuthUser()->$userKey);
        }
        if (!$user) {
            return back();
        }
        $user->unreadNotifications->markAsRead();

        return back();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAll()
    {
        $userKey = AuthUser()->primaryKey;
        if ($userKey == 'dni') {
            $user = $this->profesores()->find((string) AuthUser()->$userKey);
        } else {
            $user = Alumno::find(AuthUser()->$userKey);
        }
        if (!$user) {
            return back();
        }
        $user->notifications()->delete();

        return back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if ($borrar = Notification::find($id)) {
            $borrar->delete();
        }
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
        try {
            $modelo = $this->model;
            $elemento = $this->extractData(Notification::findOrFail($id));
        } catch (\Exception $exception) {
            Alert::danger('Notificació no trobada');
            return back();
        }
        return view($this->chooseView('show'), compact('elemento', 'modelo'));
    }

    /**
     * @param $notification
     * @return mixed
     */
    private function extractData($notification)
    {
        foreach (explode(',', trim($notification->data, '{}')) as $trozo) {
            if (strpos($trozo, '":"')) {
                $ele = explode('":"', $trozo);
                $ind = trim($ele[0], '"');
                $notification->$ind=trim($ele[1], '"');
            }
        }
        return $notification;
    }
}
