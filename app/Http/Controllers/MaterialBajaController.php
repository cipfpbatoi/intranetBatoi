<?php
namespace Intranet\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Intranet\Entities\Lote;
use Illuminate\Support\Facades\Auth;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\Material;
use Intranet\Entities\Incidencia;
use Intranet\Entities\MaterialBaja;
use Intranet\Entities\TipoIncidencia;

/**
 * Class MaterialController
 * @package Intranet\Http\Controllers
 */
class MaterialBajaController extends ModalController
{
    const ROLES_ROL_DIRECCION = 'roles.rol.direccion';

    /**
     * @var string
     */
    protected $model = 'MaterialBaja';

    /**
     * @var array
     */
    protected $gridFields = [ 'idMaterial', 'descripcion', 'espacio','fechaBaja', 'motivo' ];
    /**
     * @var array
     */



    public function search()
    {
        return MaterialBaja::where('estado', 1)->where('tipo', 0)->get();
    }

    public function iniBotones()
    {
        $this->panel->setBoton(
            'grid',
            new BotonImg('materialBaja.show')
        );
        /*
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'materialBaja.active',
                [
                    'roles' => config(self::ROLES_ROL_DIRECCION),
                    'where' => ['estado','==',0]
                ]
            )
        );*/
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'materialBaja.delete',
                [
                    'roles' => config(self::ROLES_ROL_DIRECCION)
                ]
            )
        );
    }

    public function delete($id)
    {
        $registro = MaterialBaja::findOrFail($id);
        $material = Material::findOrFail($registro->idMaterial);
        $material->delete();
        $registro->delete();
        return redirect()->back();
    }

    public function active($id)
    {
        $registro = MaterialBaja::findOrFail($id);
        $material = Material::findOrFail($registro->idMaterial);
        $material->estado = 3;
        $material->save();
        $registro->estado = 1;
        $registro->save();
        return redirect()->back();
    }
}
