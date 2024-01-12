<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Signatura;
use Intranet\Botones\BotonImg;
use Intranet\Entities\Expediente;
use Intranet\Entities\TipoExpediente;
use Intranet\Services\DigitalSignatureService;
use Intranet\Services\FormBuilder;

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
    protected $gridFields = [ 'centre', 'tipus',  'alumne', 'signed','send', 'created_at'];
    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Signatura';
    protected $parametresVista = ['modal' => ['signatura']];


    /**
     *
     */
    protected function iniBotones()
    {

        $this->panel->setBotonera([],['delete','pdf','show']);
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'signatura.sendAlumne',
                ['img'=>'fa-send','where' => ['tipus', '==', 'A3', 'signed', '==', '1']]
            )
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'signatura.sendInstructor',
                ['img'=>'fa-envelope','where' => ['tipus', '==', 'A2', 'signed', '==', '2']]
            )
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'signatura.sendInstructor',
                ['img'=>'fa-envelope','where' => ['tipus', '==', 'A1', 'signed', '==', '1']]
            )
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'signatura.sendInstructor',
                ['img'=>'fa-envelope','where' => ['tipus', '==', 'A3', 'signed', '==', '2']]
            )
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'signatura.upload',
                ['img'=>'fa-upload','where' => ['sendTo',">=", '1']]
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


}
