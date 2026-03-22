<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\AlumnoFct\AlumnoFctService;
use Intranet\Http\Controllers\Core\ModalController;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\UI\Botones\BotonBasico;
use Intranet\Entities\Signatura;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Expediente;
use Intranet\Entities\TipoExpediente;
use Intranet\Services\Signature\DigitalSignatureService;
use Intranet\Services\UI\FormBuilder;

/**
 * Class PanelExpedienteController
 * @package Intranet\Http\Controllers
 */
class SignaturaAlumneController extends ModalController
{
    private ?AlumnoFctService $alumnoFctService = null;

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
    protected $parametresVista = ['modal' => ['upload']];

    public function __construct(?AlumnoFctService $alumnoFctService = null)
    {
        parent::__construct();
        $this->alumnoFctService = $alumnoFctService;
    }

    private function alumnoFcts(): AlumnoFctService
    {
        if ($this->alumnoFctService === null) {
            $this->alumnoFctService = app(AlumnoFctService::class);
        }

        return $this->alumnoFctService;
    }



    public function index()
    {

        $alumnoFcts = $this->alumnoFcts()->byAlumno((string) authUser()->nia);
        $this->signatures = new Collection();
        foreach ($alumnoFcts as $alumnoFct){
            if ($signatura = $alumnoFct->Signatures->where('tipus','A1')->first()) {
                $this->signatures->add($signatura);
            }
        }
        $this->titulo = ['quien' => authUser()->fullName];
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
        $this->panel->setBotonera([],['pdf']);
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'signatura.upload',
                ['img'=>'fa-upload up','where' => ['tipus','==','A3','signed',">=", '2']]
            )
        );

    }


}
