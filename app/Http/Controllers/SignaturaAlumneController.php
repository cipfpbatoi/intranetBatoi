<?php

namespace Intranet\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
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
class SignaturaAlumneController extends ModalController
{

    /**
     * @var array
     */
    protected $gridFields = [ 'centre', 'tipus',  'alumne', 'created_at'];

    /**
     * @var string
     */
    protected $model = 'Signatura';


    /**
     *
     */


    public function index()
    {

        $alumnoFcts = AlumnoFct::where('idAlumno',authUser()->nia)->get();
        $this->signatures = new Collection();
        foreach ($alumnoFcts as $alumnoFct){
            $this->signatures->add($alumnoFct->Signatures->where('tipus','A3')->first());
        }
        $this->titulo = ['quien' => authUser()->nia];
        Session::forget('redirect'); //buida variable de sessió redirect ja que sols se utiliza en cas de direccio
        $this->iniBotones();
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
            $this->signatures,
            $this->titulo,
            $vista ,
            new FormBuilder($this->createWithDefaultValues(), $this->formFields));
    }

    /**
     * @return mixed
     */

    protected function pdf($id)
    {
        $sig = Signatura::find($id);
        return response()->file($sig->routeFile);
    }

    protected function iniBotones()
    {
        $this->panel->setBotonera([],['pdf','show']);
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


}
