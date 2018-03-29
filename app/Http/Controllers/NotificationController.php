<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Notification;
use Illuminate\Support\Facades\Auth;
use Intranet\Botones\BotonImg;
use Jenssegers\Date\Date;
use Intranet\Entities\Profesor;
use Intranet\Entities\Alumno;
use Illuminate\Support\Facades\Session;

class NotificationController extends IntranetController
{

    protected $model = 'Notification';
    protected $gridFields = ['emisor', 'motivo', 'fecha'];
    protected $key;
    protected $vista = ['show'=>'notification.show'];

    protected function search()
    {
        $key = AuthUser()->primaryKey;
        return Notification::where('notifiable_id', "=", AuthUser()->$key)
                ->orderBy('created_at','desc')
                ->get();  
    }
    
    public function read($id)
    {
        $notification = Notification::find($id);
        $notification->read_at = New Date('now');
        $notification->save();
        return back();
    }

    public function readAll()
    {
        $key = AuthUser()->primaryKey;
        if ($key == 'dni')
            $user = Profesor::find(AuthUser()->$key);
        else
            $user = Alumno::find(AuthUser()->$key);
        $user->unreadNotifications->markAsRead();

        return back();
    }

    public function deleteAll()
    {
        $key = AuthUser()->primaryKey;
        if ($key == 'dni')
            $user = Profesor::find(AuthUser()->$key);
        else
            $user = Alumno::find(AuthUser()->$key);
        $user->notifications()->delete();

        return back();
    }
    
    public function destroy($id)
    {
        $borrar = $this->class::findOrFail($id);
        $borrar->delete();
        return back();
    }

    protected function iniBotones()
    {
        $this->panel->setBotonera(['deleteAll', 'readAll'], ['delete','show']);
        $this->panel->setBoton('grid', new BotonImg('notification.read', [ 'where' => ['read_at', 'isNull','']]));
    }
    
    public function show($id)
    {
        $elemento = $this->class::findOrFail($id);
        $modelo = 'Notification';
        $elemento_data = trim($elemento->data,'{}');
        $trozos = explode(',',$elemento_data);
        foreach ($trozos as $trozo){
            if (strpos($trozo,'":"')){
                $ele = explode('":"',$trozo);
                $ind = trim($ele[0],'"');
                $elemento->$ind=trim($ele[1],'"');
            }
        }
        return view($this->chooseView('show'), compact('elemento', 'modelo'));
    }
}
