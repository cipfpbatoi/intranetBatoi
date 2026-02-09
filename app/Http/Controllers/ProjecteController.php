<?php

namespace Intranet\Http\Controllers;


use Intranet\UI\Botones\BotonImg;
use Intranet\UI\Botones\BotonBasico;
use Intranet\Services\NotificationService;
use Intranet\Componentes\Pdf as PDF;
use Intranet\Entities\Projecte;
use Intranet\Http\Requests\ProyectoRequest;

/**
 * Class EspacioController
 * @package Intranet\Http\Controllers
 */
class ProjecteController extends ModalController
{
    const ALUMNO = 'roles.rol.alumno';
    /**
     * @var string
     */
    protected $model = 'Projecte';
    /**
     * @var array
     */
    protected $gridFields = ['titol', 'status', 'defensa', 'hora'];

    protected $formFields = [
        'titol' => ['type' => 'text'],
        'grup' => ['type' => 'select'],
        'descripcio' => ['type' => 'textarea'],
        'objectius' => ['type' => 'textarea'],
        'resultats'=> ['type' => 'textarea'],
        'aplicacions' => ['type' => 'textarea'],
        'recursos' => ['type' => 'textarea'],

     ];

    public function search()
    {
        return Projecte::where('idAlumne', AuthUser()->nia)->get();
    }

    public function store(ProyectoRequest $request)
    {
        $new = new Projecte();
        $request->request->add(['idAlumne' => AuthUser()->nia]);
        $request->request->add(['estat' => 0]);
        $new->fillAll($request);
        return $this->redirect();
    }

    public function update(ProyectoRequest $request, $id)
    {
        $request->request->add(['idAlumne' => AuthUser()->nia]);

        Projecte::findOrFail($id)->fillAll($request);
        return $this->redirect();
    }

    public function email($id)
    {
        $projecte = Projecte::findOrFail($id);
        $projecte->estat = 1;
        $projecte->save();
        $tutor = $projecte->Grupo->Tutor;
        app(NotificationService::class)->send($tutor->dni, 'Projecte  '.$projecte->Alumno->fullname.' enviat', '#');
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
        $numProjectes =  Projecte::where('idAlumne', AuthUser()->nia)->count();
        $numGrups = AuthUser()->Grupo->count();

        if ($numGrups > $numProjectes) {
            $this->panel->setBoton('index', new BotonBasico('projecte.create'));
        }
        $this->panel->setBoton('grid', new BotonImg('projecte.show'));
        $this->panel->setBoton('grid', new BotonImg('projecte.edit', ['roles' => config(self::ALUMNO)]));
        $this->panel->setBoton('grid', new BotonImg('projecte.delete', ['roles' => config(self::ALUMNO),'where' => ['estat','==','0']]));
        $this->panel->setBoton('grid', new BotonImg('projecte.pdf', ['roles' => config(self::ALUMNO)]));
        $this->panel->setBoton('grid', new BotonImg('projecte.email', ['roles' => config(self::ALUMNO),'where' => ['estat','==','0']]));

    }

}