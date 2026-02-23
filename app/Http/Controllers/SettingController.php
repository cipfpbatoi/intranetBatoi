<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Illuminate\Http\Request;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\IpGuardia;
use Intranet\Entities\Setting;
use Styde\Html\Facades\Alert;


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
        $this->persist($request);
        Alert::info(system('php ./../artisan cache:clear'));
        return back();
    }

    public function update(Request $request, $id)
    {
        $this->persist($request, $id);
        Alert::info(system('php ./../artisan cache:clear'));
        return back();
    }


}
