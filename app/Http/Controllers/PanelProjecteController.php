<?php

namespace Intranet\Http\Controllers;


use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Componentes\Mensaje;
use Intranet\Componentes\Pdf as PDF;
use Intranet\Entities\Grupo;
use Intranet\Entities\Projecte;
use Intranet\Http\Requests\ProyectoRequest;

/**
 * Class EspacioController
 * @package Intranet\Http\Controllers
 */
class PanelProjecteController extends ModalController
{
    const TUTOR = 'roles.rol.tutor';
    /**
     * @var string
     */
    protected $model = 'Projecte';
    /**
     * @var array
     */
    protected $gridFields = ['alumne','titol', 'status', 'defensa', 'hora'];

    protected $formFields = [
        'idAlumne' => ['type' => 'select'],
        'titol' => ['type' => 'text'],
        'grup' => ['type' => 'hidden'],
        'descripcio' => ['type' => 'textarea'],
        'objectius' => ['type' => 'textarea'],
        'resultats'=> ['type' => 'textarea'],
        'aplicacions' => ['type' => 'textarea'],
        'recursos' => ['type' => 'textarea'],
     ];

    public function search()
    {
        $miGrupo = Grupo::where('tutor', '=', authUser()->dni)->orWhere('tutor', '=', authUser()->sustituye_a)->first();
        $alumnos = hazArray($miGrupo->Alumnos,'nia','nia');
        return Projecte::whereIn('idAlumne', $alumnos)->get();
    }

    public function store(ProyectoRequest $request)
    {
        $miGrupo = Grupo::where('tutor', '=', authUser()->dni)->orWhere('tutor', '=', authUser()->sustituye_a)->first();
        $new = new Projecte();
        $request->request->add(['grup' => $miGrupo->codigo]);
        $new->fillAll($request);

        return back();
    }

    public function update(ProyectoRequest $request, $id)
    {
        Projecte::findOrFail($id)->fillAll($request);
        return back();
    }


    public function pdf($id)
    {
        $elemento = Projecte::findOrFail($id);
        $informe = 'pdf.propostaProjecte';
        $pdf = PDF::hazPdf($informe, $elemento, null);
        return $pdf->stream();
    }



    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('projecte.create'));
        $this->panel->setBoton('grid', new BotonImg('projecte.show'));
        $this->panel->setBoton('grid', new BotonImg('projecte.edit', ['roles' => config(self::TUTOR)]));
        $this->panel->setBoton('grid',
            new BotonImg('projecte.delete', ['roles' => config(self::TUTOR), 'where' => ['estat', '==', '0']]));
        $this->panel->setBoton('grid', new BotonImg('projecte.pdf', ['roles' => config(self::TUTOR)]));
    }

}