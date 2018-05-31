<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Material;
use Illuminate\Support\Facades\Auth;
use Intranet\Entities\Incidencia;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;

class MaterialController extends IntranetController
{

    protected $perfil = 'profesor';
    protected $model = 'Material';
    protected $vista = ['index' => 'Material'];
    protected $gridFields = ['id', 'descripcion', 'Estado', 'espacio', 'unidades'];

    public function __construct()
    {
        $this->middleware($this->perfil);
        parent::__construct();
    }

    public function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('material.create', ['roles' => [config('constants.rol.direccion'), config('constants.rol.mantenimiento')]]));
    }

    public function espacio($espacio)
    {
        $todos = Material::where('espacio', $espacio)->get();
        foreach ($todos as $uno) {
            $uno->Estado = $uno->getEstadoOptions()[$uno->estado];
        }
        return $this->llist($todos, $this->panel);
    }

    public function copy($id)
    {
        $elemento = Material::find($id);
        $copia = New Material;
        $copia->fill($elemento->toArray());
        $copia->save();
        return redirect("/material/$copia->id/edit");
    }

    public function incidencia($id)
    {
        $elemento = Material::find($id);
        $incidencia = new Incidencia;
        $incidencia->material = $id;
        $incidencia->espacio = $elemento->espacio;
        $incidencia->descripcion = $elemento->descripcion;
        $incidencia->save();
        return redirect()->route('incidencia.edit', ['id' => $incidencia->id]);
    }

}
