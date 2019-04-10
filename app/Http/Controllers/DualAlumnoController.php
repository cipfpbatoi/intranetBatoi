<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\Grupo;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Profesor;
use Intranet\Entities\FctConvalidacion;
use DB;
use Styde\Html\Facades\Alert;
use Intranet\Botones\Panel;
use Intranet\Entities\Documento;
use Illuminate\Support\Facades\Session;
use Intranet\Jobs\SendEmail;
use Illuminate\Http\Request;

/**
 * Class DualAlumnoController
 * @package Intranet\Http\Controllers
 */
class DualAlumnoController extends FctAlumnoController
{
    use traitImprimir;

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'AlumnoFct';
    /**
     * @var array
     */
    protected $gridFields = ['Nombre', 'Centro','Instructor','desde','hasta','horas','periode'];
    /**
     * @var bool
     */
    protected $profile = false;
    /**
     * @var array
     */
    protected $titulo = [];
    /**
     * @var array
     */
    //protected $parametresVista = ['before' => [] , 'modal' => ['anexeVI']];

    /**
     * @return mixed
     */
    public function search()
    {
        return AlumnoFct::misDual()->orderBy('idAlumno')->orderBy('desde')->get();
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('dual.delete'));
        $this->panel->setBoton('grid', new BotonImg('dual.edit'));
        $this->panel->setBoton('grid', new BotonImg('dual.pdf.anexe_vii'));
        $this->panel->setBoton('grid', new BotonImg('dual.pdf.anexe_va'));
        $this->panel->setBoton('grid', new BotonImg('dual.pdf.anexe_vb'));
        $this->panel->setBoton('index', new BotonBasico("dual.create", ['class' => 'btn-info']));
        $this->panel->setBoton('index', new BotonBasico("dual.anexeVI", ['class' => 'btn-info','id' => 'anexoVI']));
        Session::put('redirect', 'DualAlumnoController@index');
    }
        //


    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function show($id)
    {
        $fct = AlumnoFct::findOrFail($id);
        return redirect("/fct/$fct->idFct/show");
    }
    
//    public function pdf($id)
//    {
//        $fct = AlumnoFct::findOrFail($id);
//        $secretario = Profesor::find(config('contacto.secretario'));
//        $director = Profesor::find(config('contacto.director'));
//        $dades = ['date' => FechaString(FechaPosterior($fct->hasta)),
//            'consideracion' => $secretario->sexo === 'H' ? 'En' : 'Na',
//            'secretario' => $secretario->FullName,
//            'centro' => config('contacto.nombre'),
//            'codigo' => config('contacto.codi'),
//            'poblacion' => config('contacto.poblacion'),
//            'provincia' => config('contacto.provincia'),
//            'director' => $director->FullName
//        ];
//        
//        $pdf = $this->hazPdf('dual.anexe_vii', $fct,$dades,'landscape','a4',10);
//        return $pdf->stream();
//    }

    /**
     * @param $id
     * @param string $informe
     * @return mixed
     */
    public function informe($id, $informe='anexe_vii')
    {
        $informe = 'dual.'.$informe;
        $fct = AlumnoFct::findOrFail($id);
        $secretario = Profesor::find(config('contacto.secretario'));
        $director = Profesor::find(config('contacto.director'));
        $dades = ['date' => FechaPosterior($fct->hasta),
            'consideracion' => $secretario->sexo === 'H' ? 'En' : 'Na',
            'secretario' => $secretario->FullName,
            'centro' => config('contacto.nombre'),
            'codigo' => config('contacto.codi'),
            'poblacion' => config('contacto.poblacion'),
            'provincia' => config('contacto.provincia'),
            'director' => $director->FullName
        ];
        
        $pdf = $this->hazPdf($informe, $fct,$dades,'landscape','a4',10);
        return $pdf->stream();
    }
    
    
} 