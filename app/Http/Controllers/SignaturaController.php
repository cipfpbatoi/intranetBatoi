<?php

namespace Intranet\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Signatura;
use Intranet\Botones\BotonImg;
use Styde\Html\Facades\Alert;


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
    protected $parametresVista = ['modal' => ['signatura','selDoc','upload']];



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
                ['img'=>'fa-upload up','where' => ['tipus','==','A3','signed',"==", '2','sendTo','==','1']]
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
                "signatura.sendAlumnat",
                [
                    'text' => "Envia A3 l'Alumnat",
                    'class' => 'btn-success selecciona',
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
                    'class' => 'btn-info selecciona',
                    'data-url' => '/api/documentacionFCT/Signed',
                    'id' => '/signatura/All/send',
                    'roles' => config(self::ROLES_ROL_TUTOR)
                ]
            )
        );

        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "signatura.deleteAll",
                [
                    'text' => 'Esborra totes Signatures',
                    'class' => 'btn-danger',
                    'onclick' => "return confirm('Vas a esborrar totes les signatures de les FCT')",
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
            if (isset($file)) {
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
        Mail::send('email.signaturaA3', ['signatura' => $signatura], function ($message) use ($signatura) {
            $message->to($signatura->Fct->Fct->Instructor->email,
                $signatura->Fct->Fct->Instructor->name)->subject('Signatura FCT');
            $message->attach($signatura->routeFile);
        });
        Alert::info('Signatura enviada a '.$signatura->Fct->Fct->Instructor->name);
        return back();
    }
    public function sendMultiple(Request $request,$tipus)
    {
        if ($tipus == 'A3'){
            foreach ($request->all() as $key => $value) {
                if (is_numeric($key) && $value == 'on') {
                    $signatura = Signatura::find($key);
                    $signatura->sendTo = 1;
                    $signatura->save();
                    Mail::send('email.signaturaA3', ['signatura' => $signatura], function ($message) use ($signatura) {
                        $message->to($signatura->Fct->Alumno->email,
                            $signatura->Fct->Alumno->fullName)->subject('Signatura FCT');
                        $message->attach($signatura->routeFile);
                    });
                    Alert::info('Signatura enviada a '.$signatura->Fct->Alumno->fullName);
                }
            }
            return back();
        }
        if ($tipus == 'All'){
            foreach ($request->all() as $key => $value) {
                if (is_numeric($key) && $value == 'on') {
                    $alumnoFct = AlumnoFct::find($key);
                    Mail::send('email.signaturaAll', ['fct' => $alumnoFct], function ($message) use ($alumnoFct) {
                        $message->to($alumnoFct->Fct->Instructor->email,
                            $alumnoFct->Fct->Instructor->fullName)->subject('Signatura FCT');
                        foreach ($alumnoFct->signatures as $signatura){
                            $message->attach($signatura->routeFile);
                            if ($signatura->sendTo < 2) {
                                $signatura->sendTo += 2;
                            }
                            $signatura->save();
                        }
                    });
                    Alert::info('Signatura enviada a '.$alumnoFct->Fct->Instructor->nombre);
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
        $signatura->signed += 1;
        $signatura->sendTo = 0;
        $signatura->save();
        return back();
    }


}
