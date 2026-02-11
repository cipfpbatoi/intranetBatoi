<?php
namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Espacio;
use Intranet\Entities\Material;
use Intranet\Entities\MaterialBaja;

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
        return DB::transaction(function () use ($id) {
            $registro = MaterialBaja::findOrFail($id);
            $material = Material::findOrFail($registro->idMaterial);

            if ((int)$registro->tipo === 0) {
                // Baixa
                $material->fechaBaja = Hoy();
                $material->estado = 3;
            } else {
                // Trasllat d'espai (assumint que 'nuevoEstado' guarda l'ID d'Espacio)
                $nuevo = trim((string) $registro->nuevoEstado);

                if ($nuevo === '' || $nuevo === '0') {
                    throw ValidationException::withMessages([
                        'nuevoEstado' => "Cal indicar l'espai destí (no pot ser 0 ni buit).",
                    ]);
                }
                if (!Espacio::whereKey($nuevo)->exists()) {
                    throw ValidationException::withMessages([
                        'nuevoEstado' => "L'espai destí \"$nuevo\" no existeix.",
                    ]);
                }

                $material->espacio = $nuevo;
            }

            $material->save();

            $registro->estado = 1;
            $registro->save();

            return redirect()->back();
        });
    }
}
