<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Botones\BotonImg;
use Intranet\Entities\IpGuardia;
use Intranet\Entities\Setting;


/**
 * Class LoteController
 * @package Intranet\Http\Controllers
 */
class SettingController extends ModalController
{

    /**
     * @var string
     */
    protected $model = 'Setting';



    protected $gridFields = [ 'id','collection' ,'key','value'];

    protected function search()
    {
        return Setting::all();
    }

    protected function iniBotones()
    {
        $this->panel->setBotonera(['create'], ['edit','delete']);
    }

    public function store(Request $request)
    {
        $new = new Setting();
        $new->fillAll($request);
        $new->save();
        return back();
    }

    public function update(Request $request, $id)
    {
        Setting::findOrFail($id)->fillAll($request);
        return back();
    }


}
