<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Notification;
use Intranet\Botones\BotonImg;
use Jenssegers\Date\Date;
use Intranet\Entities\Profesor;
use Intranet\Entities\Alumno;
use Styde\Html\Facades\Alert;

/**
 * Class NotificationController
 * @package Intranet\Http\Controllers
 */
class NotificationController extends IntranetController
{

    /**
     * @var string
     */
    protected $model = 'Notification';
    /**
     * @var array
     */
    protected $gridFields = ['emisor', 'motivo', 'fecha'];
    /**
     * @var
     */
    protected $key;
    /**
     * @var array
     */
    protected $vista = ['show'=>'notification.show'];

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
            $user = Profesor::find(AuthUser()->$userKey);
        } else {
            $user = Alumno::find(AuthUser()->$userKey);
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
            $user = Profesor::find(AuthUser()->$userKey);
        } else {
            $user = Alumno::find(AuthUser()->$userKey);
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
