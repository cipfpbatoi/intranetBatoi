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

    /**
     * @var array
     */
    protected $gridFields = [ 'centre', 'tipus',  'alumne', 'signed','send', 'created_at'];
    /**
     * @var string
     */
    //protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Signatura';


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

    }

    public function fct($id)
    {

        $fct = AlumnoFct::find($id);
        $this->titulo = ['quien' => $fct->Alumno->fullName];
        Session::forget('redirect'); //buida variable de sessió redirect ja que sols se utiliza en cas de direccio
        $this->iniBotones();
        $this->sao = $fct->idSao;
        $this->iniPestanas();
        return $this->grid();
    }

    protected function grid()
    {
        if (is_array($this->vista)) {
            $vista = $this->vista['index'] ??'intranet.indexModal';
        } else {
            $vista = $this->vista ??'intranet.indexModal';
        }
        return $this->panel->render(
            Signatura::where('idSao',$this->sao)->get(),
            $this->titulo,
            $vista ,
            new FormBuilder($this->createWithDefaultValues(), $this->formFields));
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


}
