<?php

namespace Intranet\Http\Controllers;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Intranet\Botones\BotonBasico;
use Intranet\Componentes\MyMail;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Signatura;
use Intranet\Botones\BotonImg;
use Intranet\Services\AttachedFileService;



/**
 * Class PanelExpedienteController
 * @package Intranet\Http\Controllers
 */
class SignaturaController extends ModalController
{
    const ROLES_ROL_TUTOR = 'roles.rol.tutor';
    /**
     * @var array
     */
    protected $gridFields = [ 'centre', 'tipus',  'alumne', 'estat', 'created_at'];

    /**
     * @var string
     */
    protected $model = 'Signatura';
    protected $parametresVista = ['modal' => ['signatura','selDoc','upload','informes','loading']];



    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "alumnoFct",
                [
                    'text' => 'Tornar FCTs',
                    'class' => 'btn-dark back'
                ]
            )
        );

        $this->panel->setBotonera([],['delete','pdf','show']);
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'signatura.send',
                ['img'=>'fa-envelope']
            )
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'signatura.upload',
                ['img'=>'fa-upload up','where' => ['tipus','==','A3','signed',">=", '2']]
            )
        );

        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "signatura.post",
                [
                    'text' => 'Descarrega SAO',
                    'class' => 'btn-danger sign',
                    'roles' => config(self::ROLES_ROL_TUTOR)]
            )
        );
        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "fct.print",
                ['class' => 'btn-warning selecciona', 'roles' => config(self::ROLES_ROL_TUTOR)]
            )
        );
        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "signatura.sendAlumnat",
                [
                    'text' => "Envia A3 l'Alumnat",
                    'class' => 'btn-success seleccion',
                    'data-url' => '/api/documentacionFCT/A3',
                    'id' => '/signatura/A3/send',
                    'roles' => config(self::ROLES_ROL_TUTOR)]
            )
        );

        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "signatura.sendFct",
                [
                    'text' => "Envia Documentació a l'Instructor",
                    'class' => 'btn-info seleccion',
                    'data-url' => '/api/documentacionFCT/Signed',
                    'id' => '/signatura/All/send',
                    'roles' => config(self::ROLES_ROL_TUTOR)
                ]
            )
        );


        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "signatura.post",
                [
                    'text' => 'A1/A5',
                    'class' => 'btn-danger a1',
                    'roles' => config(self::ROLES_ROL_TUTOR)
                ]
            )
        );
        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "signatura.a5",
                [
                    'text' => "Guarda A5",
                    'class' => 'btn-info',
                    'roles' => config(self::ROLES_ROL_TUTOR)
                ]
            )
        );


    }


    /**
     * @return mixed
     */
    protected function search()
    {
        return Signatura::where('idProfesor', authUser()->dni)->get();
    }

    protected function deleteAll()
    {
        $signatures = $this->search();
        foreach ($signatures as $signature){
            $signature->delete();
        }
        return back();
    }

    protected function pdf($id)
    {
        $sig = Signatura::find($id);
        return response()->file($sig->routeFile);
    }

    public function destroy($id)
    {
        if ($elemento = Signatura::find($id)) {
            $fctAl = AlumnoFct::where('idSao',$elemento->idSao)->first();
            $file = $fctAl->routeFile($elemento->tipus);
            if (isset($file) && file_exists($file)){
                unlink($file);
            }
            $elemento->delete();
        }
        return back();
    }

    public function sendUnique($id)
    {
        $signatura = Signatura::find($id);
        if ($signatura->sendTo < 2) {
            $signatura->sendTo += 2;
        }
        $signatura->save();
        $signatura->mail = $signatura->Fct->Fct->Instructor->email;
        $signatura->contact = $signatura->Fct->Fct->Instructor->nombre;
        $col = new Collection();
        $col->push($signatura);
        $view = $signatura->tipus === 'A5' ? 'email.fct.A5' : 'email.fct.anexes';
        $mail = new MyMail(
            $col,
            view($view),
            ['subject'=>'Documents FCT de '.$signatura->Fct->Alumno->fullName],
            [$signatura->simpleRouteFile => 'application/pdf']);
            //per a marcar-lo com a enviat a l'instructor
        session()->flash('email_action', 'Intranet\Events\EmailAnnexeIndividual');
        return $mail->render('/signatura');
    }
    public function sendMultiple(Request $request,$tipus)
    {
        if ($tipus == 'A3'){ //al alumno
            foreach ($request->all() as $key => $value) {
                if (is_numeric($key) && $value == 'on') {
                    $signatura = Signatura::find($key);
                    $signatura->sendTo = 1;
                    $signatura->save();
                    $signatura->mail = $signatura->Alumno->email;
                    $signatura->contact = $signatura->Alumno->fullName;
                    $mail = new MyMail(
                        $signatura,
                        'email.signaturaA3',
                        ['subject'=>'Documents FCT de '.$signatura->Alumno->fullName,
                            ''],
                        [$signatura->simpleRouteFile => 'application/pdf']);
                    $mail->send();
                }
            }
            return back();
        }
        if ($tipus == 'All'){ //a l'instructor
            if (count($request->toArray()) === 2){
                $element = array_keys($request->except('_token'));
                $alumnoFct = AlumnoFct::find(reset($element));
                $signatures = [];
                foreach ($alumnoFct->signatures as $signatura){
                    $signatures[$signatura->simpleRouteFile] = 'application/pdf';
                    $view = $signatura->tipus === 'A5' ? 'email.fct.A5' : 'email.fct.anexes';
                }
                $alumnoFct->mail = $alumnoFct->Fct->Instructor->email;
                $alumnoFct->contact = $alumnoFct->Fct->Instructor->nombre;
                $col = new Collection();
                $col->push($alumnoFct);
                $mail = new MyMail(
                    $col,
                    view($view),
                    ['subject'=>'Documents FCT de '.$signatura->Alumno->fullName],
                    $signatures);
                session()->flash('email_action', 'Intranet\Events\EmailAnnexeIndividual');
                return $mail->render('/signatura');
            }
            foreach ($request->all() as $key => $value) {
                if (is_numeric($key) && $value == 'on') {
                    $alumnoFct = AlumnoFct::find($key);
                    $signatures = [];
                    $a1 = false;
                    foreach ($alumnoFct->signatures as $signatura){
                        $signatures[$signatura->simpleRouteFile] = 'application/pdf';
                        if ($signatura->tipus == 'A1'){
                            $a1 = true;
                        }
                        $view = $signatura->tipus === 'A5' ? 'email.fct.A5' : 'email.fct.anexes';
                    }
                    $alumnoFct->mail = $alumnoFct->Fct->Instructor->email;
                    $alumnoFct->contact = $alumnoFct->Fct->Instructor->nombre;
                    $alumnoFct->annexe = $a1;
                    $mail = new MyMail(
                        $alumnoFct,
                        $view,
                        ['subject'=>'Documents FCT de '.$alumnoFct->Alumno->fullName],
                        $signatures);
                    session()->flash('email_action', 'Intranet\Events\EmailAnnexeIndividual');
                    $mail->send();
                }
            }
            return back();
        }

    }


    public function upload(Request $request,$id)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:2048',
        ]);
        $signatura = Signatura::find($id);
        $file = $request->file('file');
        $file->move($signatura->path, $signatura->fileName);
        if ($signatura->signed == 2){
            $signatura->signed += 1;
        }
        $signatura->sendTo = 0;
        $signatura->save();
        return back();
    }

    public function a5(){

        $signatures = Signatura::where('tipus','A5')->where('idProfesor', authUser()->dni)->get();
        foreach ($signatures as $signature){
            $alFct = AlumnoFct::where('idSao',$signature->idSao)->first();
            $path = 'alumnofctaval/'.$alFct->id;
            if (AttachedFileService::saveExistingFile($signature->routeFile, $path, authUser()->dni))
            {
                $signature->delete();
            }
        }
        return back();
    }
}