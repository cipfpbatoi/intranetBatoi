<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Intranet\Http\Requests\SettingRequest;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Setting;
use Intranet\Services\UI\AppAlert as Alert;
use Intranet\Exceptions\NotFoundDomainException;


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

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return Setting
     */
    private function findSettingOrFail($id): Setting
    {
        try {
            return Setting::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException('Configuració no trobada', ['setting_id' => $id]);
        }
    }

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
        $this->authorize('update', $this->findSettingOrFail($id));
        $this->persist($request, $id);
        Alert::info(system('php ./../artisan cache:clear'));
        return back();
    }

    /**
     * Elimina un setting existent.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     */
    public function destroy($id)
    {
        $this->authorize('delete', $this->findSettingOrFail($id));
        return parent::destroy($id);
    }

}
