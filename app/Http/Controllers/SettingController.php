<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Intranet\Http\Requests\SettingRequest;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Setting;
use Intranet\Services\UI\AppAlert as Alert;


/**
 * Controlador de manteniment de settings de sistema.
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

    public function store(SettingRequest $request)
    {
        $this->authorize('create', Setting::class);
        $this->persist($request);
        Alert::info(system('php ./../artisan cache:clear'));
        return back();
    }

    public function update(SettingRequest $request, $id)
    {
        $this->authorize('update', Setting::findOrFail($id));
        $this->persist($request, $id);
        Alert::info(system('php ./../artisan cache:clear'));
        return back();
    }

    /**
     * Elimina un setting existent.
     *
     * @param int|string $id
     */
    public function destroy($id)
    {
        $this->authorize('delete', Setting::findOrFail($id));
        return parent::destroy($id);
    }

}
