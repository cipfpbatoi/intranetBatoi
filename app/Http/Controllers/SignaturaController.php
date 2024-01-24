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
class SignaturaController extends IntranetController
{
    const ROLES_ROL_TUTOR = 'roles.rol.tutor';
    /**
     * @var array
     */
    protected $gridFields = [ 'centre', 'tipus',  'alumne', 'estat', 'created_at'];
    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Signatura';
    protected $parametresVista = ['modal' => ['signatura','selDoc']];



    /**
     *
     */
    protected function iniBotones()
    {

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
                ['img'=>'fa-upload','where' => ['tipus','==','A3','sendTo',">=", '1']]
            )
        );
        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "alumnoFct",
                [
                    'text' => 'Tornar FCTs',
                    'class' => 'btn-success back'
                ]
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
                    'text' => 'Envia Alumnat',
                    'class' => 'btn-info selecciona',
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
                    'text' => 'Envia Instructor',
                    'class' => 'btn-info selecciona',
                    'data-url' => '/api/documentacionFCT/Signed',
                    'id' => '/signatura/All/send',
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
        $signatura->sendTo = 1;
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
                            $signatura->sendTo = 1;
                            $signatura->save();
                        }
                    });
                    Alert::info('Signatura enviada a '.$alumnoFct->Fct->Instructor->nombre);
                }
            }
            return back();
        }

    }


}
