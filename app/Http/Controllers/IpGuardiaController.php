<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Intranet\Http\Requests\IpGuardiaRequest;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\IpGuardia;
use Intranet\Exceptions\NotFoundDomainException;


/**
 * Class IpGuardiaController
 * @package Intranet\Http\Controllers
 */
class IpGuardiaController extends ModalController
{

    /**
     * @var string
     */
    protected $model = 'IpGuardia';



    protected $gridFields = [ 'id', 'ip','codOcup'];

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return IpGuardia
     */
    private function findIpGuardiaOrFail($id): IpGuardia
    {
        try {
            return IpGuardia::findOrFail((int) $id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException('IP de guàrdia no trobada', ['ip_guardia_id' => $id]);
        }
    }

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
        $this->authorize('update', $this->findIpGuardiaOrFail($id));
        $this->persist($request, $id);
        return back();
    }

    /**
     * Elimina una IP de guàrdia amb autorització explícita.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     */
    public function destroy($id)
    {
        $this->authorize('delete', $this->findIpGuardiaOrFail($id));
        return parent::destroy($id);
    }


}
