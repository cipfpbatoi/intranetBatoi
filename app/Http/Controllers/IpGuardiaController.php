<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Illuminate\Http\Request;
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

    public function store(Request $request)
    {
        $this->persist($request);
        return back();
    }

    public function update(Request $request, $id)
    {
        $this->persist($request, $id);
        return back();
    }


}
