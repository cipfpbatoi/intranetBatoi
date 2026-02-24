<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Intranet\Http\Requests\IpGuardiaRequest;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\IpGuardia;


/**
 * Class LoteController
 * @package Intranet\Http\Controllers
 */
class IpGuardiaController extends ModalController
{

    /**
     * @var string
     */
    protected $model = 'IpGuardia';



    protected $gridFields = [ 'id', 'ip','codOcup'];

    protected function search()
    {
        return IpGuardia::all();
    }

    protected function iniBotones()
    {
        $this->panel->setBotonera(['create']);
        $this->panel->setBoton(
            'grid',
            new BotonImg('ipGuardia.edit')
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg('ipGuardia.delete')
        );
    }

    public function store(IpGuardiaRequest $request)
    {
        $this->authorize('create', IpGuardia::class);
        $this->persist($request);
        return back();
    }

    public function update(IpGuardiaRequest $request, $id)
    {
        $this->authorize('update', IpGuardia::findOrFail((int) $id));
        $this->persist($request, $id);
        return back();
    }

    /**
     * Elimina una IP de guàrdia amb autorització explícita.
     *
     * @param int|string $id
     */
    public function destroy($id)
    {
        $this->authorize('delete', IpGuardia::findOrFail((int) $id));
        return parent::destroy($id);
    }


}
