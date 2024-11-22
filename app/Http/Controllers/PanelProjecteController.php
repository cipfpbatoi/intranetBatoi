<?php

namespace Intranet\Http\Controllers;


use Illuminate\Support\Facades\Mail;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Componentes\Mensaje;
use Intranet\Componentes\Pdf as PDF;
use Intranet\Entities\Grupo;
use Intranet\Entities\OrdenReunion;
use Intranet\Entities\Profesor;
use Intranet\Entities\Projecte;
use Intranet\Entities\Reunion;
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
        $request->request->add(['grup' => $miGrupo->codigo,'estat'=>1]);
        $new->fillAll($request);

        return back();
    }

    public function update(ProyectoRequest $request, $id)
    {
        Projecte::findOrFail($id)->fillAll($request);
        return back();
    }

    public function send()
    {
        $miGrupo = Grupo::where('tutor', '=', authUser()->dni)
            ->orWhere('tutor', '=', authUser()->sustituye_a)
            ->first();

        $alumnos = hazArray($miGrupo->Alumnos, 'nia', 'nia');
        $projectes = Projecte::whereIn('idAlumne', $alumnos)
            ->where('estat', 1)
            ->get();

        // Usar hazZip para generar el zip
        $zipPath = Pdf::hazZip('pdf.propostaProjecte', $projectes , null, 'portrait',  'idAlumne'   );

        // Enviar el correo con el zip adjunto
        $profesores = Profesor::Grupo($miGrupo->codigo)->get();
        $professorsEmails = [];
        foreach ($profesores as $profesor) {
            $professorsEmails[] = $profesor->email;
        }

        Mail::send('email.projectes', ['grupo' => $miGrupo], function($message) use ($zipPath, $professorsEmails) {
            $message->to($professorsEmails)
                ->subject('Projectes del grup')
                ->attach($zipPath);
        });

        // Limpiar el archivo zip
        unlink($zipPath);

        return back()->with('success', 'Se ha enviado el correo con los proyectos del grupo.');
    }


    public function acta()
    {
        $miGrupo = Grupo::where('tutor', '=', authUser()->dni)
            ->orWhere('tutor', '=', authUser()->sustituye_a)
            ->first();

        $alumnos = hazArray($miGrupo->Alumnos, 'nia', 'nia');
        $projectes = Projecte::whereIn('idAlumne', $alumnos)
            ->where('estat', 1)
            ->get();


        $acta = new Reunion(['tipo'=>11,'numero'=>0,'curso'=>curso(),'fecha'=>hoy(),'idProfesor'=>authUser()->dni,'descripcion'=>'Acta valoració propostes','objectivos'=>"Valorar les propostes que ha fet l'alumnat per al mòdul de Projecte",'idEspacio'=>'SalaProf' ]);
        $acta->save();
        foreach ($projectes as $key => $projecte) {
            OrdenReunion::create([
                'idReunion' => $acta->id,
                'descripcion' => $projecte->Alumno->fullName,
                'resumen' => $projecte->titol,
                'orden' => $key+1
            ]);
        }

        return back()->with('success', 'Se ha creado el acta de valoración de propuestas.');

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
        $this->panel->setBoton('index', new BotonBasico('projectes.send',[ 'class' => 'btn-info '  ] ));
        $this->panel->setBoton('index', new BotonBasico('projectes.acta',[ 'class' => 'btn-warning '  ] ));
        $this->panel->setBoton('index', new BotonBasico('projecte.create'));
        //$this->panel->setBoton('grid', new BotonImg('projecte.show'));
        $this->panel->setBoton('grid', new BotonImg('projecte.edit', ['roles' => config(self::TUTOR)]));
        $this->panel->setBoton('grid',
            new BotonImg('projecte.delete', ['roles' => config(self::TUTOR), 'where' => ['estat', '==', '0']]));
        $this->panel->setBoton('grid', new BotonImg('projecte.pdf', ['roles' => config(self::TUTOR)]));
    }

}