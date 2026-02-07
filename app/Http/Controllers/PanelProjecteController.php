<?php

namespace Intranet\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Intranet\UI\Botones\BotonImg;
use Intranet\UI\Botones\BotonBasico;
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
    protected $gridFields = ['alumne','titol', 'status', 'defensa', 'hora_defensa'];
    protected $formFields = [
        'grup' => ['type' => 'hidden'],
        'estat' => ['type' => 'hidden'],
        'idAlumne' => ['type' => 'select'],
        'titol' => ['type' => 'text'],
        'descripcio' => ['type' => 'textarea'],
        'objectius' => ['type' => 'textarea'],
        'resultats'=> ['type' => 'textarea'],
        'aplicacions' => ['type' => 'textarea'],
        'recursos' => ['type' => 'textarea'],
        'defensa' => ['type' => 'date'],
        'hora_defensa' => ['type' => 'time'],
     ];
    protected $parametresVista = ['modal' => ['defensa']];



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

    public function check($id)
    {
        $projecte = Projecte::findOrFail($id);
        $projecte->estat = 2;
        $projecte->save();
        return back();
    }

    public function destroy($id)
    {
        if ($elemento = Projecte::findOrFail($id)) {
            $elemento->delete();
        }
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
                'resumen' => $projecte->titol.' (Tutor individual)',
                'orden' => $key+1
            ]);
        }

        return redirect()->route('reunion.edit', $acta->id);

    }

    public function actaE()
    {
        $miGrupo = Grupo::where('tutor', '=', authUser()->dni)
            ->orWhere('tutor', '=', authUser()->sustituye_a)
            ->first();

        $alumnos = hazArray($miGrupo->Alumnos, 'nia', 'nia');
        $projectes = Projecte::whereIn('idAlumne', $alumnos)
            ->orderBy('defensa')
            ->orderBy('hora_defensa')
            ->where('estat', 2)
            ->get();


        $acta = new Reunion(['tipo'=>12,'numero'=>0,'curso'=>curso(),'fecha'=>hoy(),'idProfesor'=>authUser()->dni,'descripcion'=>'Data Defensa del mòdul de projecte','objectivos'=>"Assignar dia i hora per a la defensa dels Projectes",'idEspacio'=>'SalaProf' ]);
        $acta->save();
        foreach ($projectes as $key => $projecte) {
            OrdenReunion::create([
                'idReunion' => $acta->id,
                'descripcion' => $projecte->Alumno->fullName,
                'resumen' => '('.$projecte->titol.')'.$projecte->defensa.'('.$projecte->hora_defensa.')',
                'orden' => $key+1
            ]);
        }

        return redirect()->route('reunion.edit', $acta->id);

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
        $this->panel->setBoton('index', new BotonBasico('projectes.sendP',[ 'class' => 'btn-info '  ] ));

        $this->panel->setBoton('index', new BotonBasico('projectes.actaP',[ 'class' => 'btn-warning '  ] ));
        $this->panel->setBoton('index', new BotonBasico('projectes.actaE',[ 'class' => 'btn-success '  ] ));
        $this->panel->setBoton('index', new BotonBasico('projecte.create'));

        //$this->panel->setBoton('grid', new BotonImg('projecte.show'));
        $this->panel->setBoton('grid', new BotonImg('projecte.edit', ['roles' => config(self::TUTOR), 'where' => ['estat', '<' , '3']]));
        $this->panel->setBoton('grid',
            new BotonImg('projectes.delete', ['roles' => config(self::TUTOR), 'where' => ['estat', '< ', '2']]));
        $this->panel->setBoton('grid', new BotonImg('projecte.pdf', ['roles' => config(self::TUTOR)]));
        $this->panel->setBoton('grid', new BotonImg('projecte.check', ['img' => 'fa-check','roles' => config(self::TUTOR), 'where' => ['estat', '==', '1']]));
    }

}