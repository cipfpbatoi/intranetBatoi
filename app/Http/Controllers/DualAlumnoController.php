<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Profesor;
use DB;

use Illuminate\Support\Facades\Session;
use mikehaertl\pdftk\Pdf;
use Jenssegers\Date\Date;

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
        $this->panel->setBoton('grid', new BotonImg('dual.anexo',['img'=>'fa-phone']));
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

    public function printAnexeXIII($id){
        $pdf = new Pdf('fdf/ANEXO_XIII.pdf');
        $pdf->fillform($this->makeArrayPdfAnexoXIII($id));
        return $pdf->send("dualXIII_$id".'pdf');
    }

    /**
     * @param $array
     * @return mixed
     */
    private function makeArrayPdfAnexoXIII($id)
    {
        $fct = AlumnoFct::findOrFail($id);
        $array[1] = Profesor::find(config('contacto.secretario'))->fullName;
        $array[2] = config('contacto.nombre');
        $array[3] = config('contacto.codi');
        $array[4] = $fct->Alumno->fullName;
        $array[5] = $fct->Alumno->dni;
        $array[6] = $fct->horas;
        $array[7] = $fct->Fct->Colaboracion->Ciclo->vliteral;
        $array[8] = $array[1];
        $array[9] = config('contacto.nombre');
        $array[10] = config('contacto.codi');
        $array[11] = $fct->Alumno->fullName;
        $array[12] = $fct->Alumno->dni;
        $array[13] = $fct->horas;
        $array[14] = $fct->Fct->Colaboracion->Ciclo->cliteral;
        $array[15] = $fct->Fct->Centro;
        $array[16] = $fct->Fct->Colaboracion->Centro->direccion;
        $array[17] = $fct->horas;
        $array[18] = $fct->desde."/".$fct->hasta;
        $array[19] = 1;
        $array[20] = 'Dissenyador web';
        $array[27] = config('contacto.poblacion');
        $fc1 = new Date();
        Date::setlocale('ca');
        $array[28] = $fc1->format('d');
        $array[29] = $fc1->format('F');
        $array[30] = $fc1->format('Y');
        $array[31] = $array[1];
        $array[32] = Profesor::find(config('contacto.director'))->fullName;

        $array[33] = $array[1];
        $array[34] = config('contacto.nombre');
        $array[35] = config('contacto.codi');
        $array[36] = $fct->Alumno->fullName;
        $array[37] = $fct->Alumno->dni;
        $array[38] = $fct->horas;
        $array[39] = $fct->Fct->Colaboracion->Ciclo->vliteral;
        $array[40] = $array[1];
        $array[41] = config('contacto.nombre');
        $array[42] = config('contacto.codi');
        $array[43] = $fct->Alumno->fullName;
        $array[44] = $fct->Alumno->dni;
        $array[45] = $fct->horas;
        $array[46] = $fct->Fct->Colaboracion->Ciclo->cliteral;
        $array[47] = $fct->Fct->Centro;
        $array[48] = $fct->Fct->Colaboracion->Centro->direccion;
        $array[49] = $fct->horas;
        $array[50] = $fct->desde."/".$fct->hasta;
        $array[51] = 1;
        $array[52] = 'Dissenyador web';
        $array[53] = config('contacto.poblacion');
        $fc1 = new Date();
        Date::setlocale('ca');
        $array[54] = $fc1->format('d');
        $array[55] = $fc1->format('F');
        $array[56] = $fc1->format('Y');
        $array[57] = $array[1];
        $array[58] = Profesor::find(config('contacto.director'))->fullName;

        return $array;
    }
    
    
} 