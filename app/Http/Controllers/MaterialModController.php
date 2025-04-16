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
class MaterialModController extends ModalController
{
    const ROLES_ROL_DIRECCION = 'roles.rol.direccion';

    /**
     * @var string
     */
    protected $model = 'MaterialBaja';

    /**
     * @var array
     */
    protected $gridFields = [
        'idMaterial',
        'tipus',
        'descripcion',
        'espacio',
        'fechaBaja',
        'solicitante',
        'motivo',
        'nuevo'
    ];
    /**
     * @var array
     */



    public function search()
    {
        return MaterialBaja::where('estado', 0)->get();
    }

    public function iniBotones()
    {
        $this->panel->setBoton(
            'grid',
            new BotonImg('materialBaja.show')
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'materialBaja.resolve',
                [
                    'roles' => config(self::ROLES_ROL_DIRECCION),
                ]
            )
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'materialBaja.refuse',
                [
                    'roles' => config(self::ROLES_ROL_DIRECCION)
                ]
            )
        );
    }

    public function refuse($id)
    {
        $registro = MaterialBaja::findOrFail($id);
        if ($registro->tipo == 0) {
            $aviso = 'El material '.$registro->Material->descripcion." NO ha estat donat de Baixa : ";
        } else {
            $aviso = 'El material '.$registro->Material->descripcion." NO ha estat canviat d'ubicació";
        }
        avisa($registro->idProfesor, $aviso, '#', 'SISTEMA');
        $registro->delete();

        return redirect()->back();
    }

    public function resolve($id)
    {
        $registro = MaterialBaja::findOrFail($id);
        $material = Material::findOrFail($registro->idMaterial);
        if ($registro->tipo == 0) {
            $material->fechaBaja = Hoy();
            $material->estado = 3;
            $material->save();
        } else {
            $material->espacio = $registro->nuevoEstado;
            $material->save();
        }
        $registro->estado = 1;
        $registro->save();
        return redirect()->back();
    }
}
