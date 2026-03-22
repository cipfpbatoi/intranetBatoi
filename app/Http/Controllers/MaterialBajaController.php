<?php
namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Illuminate\Support\Facades\Session;
use Intranet\Entities\Lote;
use Illuminate\Support\Facades\Auth;
use Intranet\UI\Botones\BotonImg;
use Intranet\UI\Botones\BotonBasico;
use Intranet\Entities\Material;
use Intranet\Entities\Incidencia;
use Intranet\Entities\MaterialBaja;
use Intranet\Entities\TipoIncidencia;
use Intranet\Exceptions\NotFoundDomainException;

/**
 * Class MaterialBajaController
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
        if (esRol(authUser()->rol, config(self::ROLES_ROL_DIRECCION))) {
            return MaterialBaja::where('estado', 1)->where('tipo', 0)->get();
        } else {
            return MaterialBaja::whereHas('material.espacios', function ($query) {
                    $query->where('idDepartamento', AuthUser()->departamento);
            })->where('estado', 1)->where('tipo', 0)->where('idProfesor', AuthUser()->dni)->get();
        }
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
                'materialBaja.delete',
                [
                    'roles' => config(self::ROLES_ROL_DIRECCION)
                ]
            )
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'materialBaja.recover',
                [
                    'img' => 'fa-recycle',
                    'roles' => config(self::ROLES_ROL_DIRECCION)
                ]
            )
        );
    }

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $registro = $this->findModelOrFail(
            MaterialBaja::class,
            $id,
            'Registre de baixa no trobat',
            ['material_baja_id' => $id]
        );
        $this->authorize('delete', $registro);
        $material = $this->findModelOrFail(
            Material::class,
            $registro->idMaterial,
            'Material no trobat',
            ['material_id' => $registro->idMaterial]
        );
        $material->delete();
        $registro->delete();
        return redirect()->back();
    }

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function active($id)
    {
        $registro = $this->findModelOrFail(
            MaterialBaja::class,
            $id,
            'Registre de baixa no trobat',
            ['material_baja_id' => $id]
        );
        $this->authorize('update', $registro);
        $material = $this->findModelOrFail(
            Material::class,
            $registro->idMaterial,
            'Material no trobat',
            ['material_id' => $registro->idMaterial]
        );
        $material->estado = 3;
        $material->save();
        $registro->estado = 1;
        $registro->save();
        return redirect()->back();
    }

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function recover($id)
    {
        $registro = $this->findModelOrFail(
            MaterialBaja::class,
            $id,
            'Registre de baixa no trobat',
            ['material_baja_id' => $id]
        );
        $this->authorize('recover', $registro);
        $material = $this->findModelOrFail(
            Material::class,
            $registro->idMaterial,
            'Material no trobat',
            ['material_id' => $registro->idMaterial]
        );
        $material->fechaBaja = null;
        $material->estado = 1;
        $material->save();
        $registro->delete();
        return redirect()->back();
    }
}
