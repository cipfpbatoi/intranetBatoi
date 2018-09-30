<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Intranet\Entities\Dual;
use Intranet\Entities\Profesor;
use Illuminate\Support\Facades\Session;
use Intranet\Botones\BotonBasico;
use Intranet\Botones\BotonImg;
use Intranet\Botones\Panel;
use Intranet\Entities\Documento;

class DualController extends FctController
{

    protected $perfil = 'profesor';
    protected $model = 'Dual';
    protected $gridFields = [ 'Nombre', 'Centro','desde', 'fin', 'periode','qualificacio', 'projecte','horas','desde','hasta','id'];
    protected $grupo;
    protected $vista = ['show' => 'fct'];
    

    protected $modal = false;

    
    public function index(){
        Session::forget('pestana');
        return parent::index();
    }
    
    
    protected function iniBotones()
    {
        //$this->panel->setBotonera();
        $this->panel->setBoton('grid', new BotonImg('dual.delete'));
        $this->panel->setBoton('grid', new BotonImg('dual.edit'));
        $this->panel->setBoton('grid', new BotonImg('dual.show'));
        $this->panel->setBoton('grid', new BotonImg('dual.anexevii',['img' => 'fa-file-word-o']));
        $this->panel->setBoton('index', new BotonBasico("dual.create", ['class' => 'btn-info','roles' => config('roles.rol.tutor')]));
//        $find = Documento::where('propietario', AuthUser()->FullName)->where('tipoDocumento','Qualitat')
//                ->where('curso',Curso())->first();
//        if (!$find) $this->panel->setBoton('index', new BotonBasico("fct.upload", ['class' => 'btn-info','roles' => config('roles.rol.tutor')]));
//        else $this->panel->setBoton('index', new BotonBasico("documento.$find->id.edit", ['class' => 'btn-info','roles' => config('roles.rol.tutor')]));
        Session::put('redirect', 'DualController@index');
    }

    

    public function search()
    {
        return Dual::misFcts()->where('asociacion',3)->get();
    }
    
    
    public function anexevii($id)
    {
        $fct = Dual::findOrFail($id);
        $secretario = Profesor::find(config('contacto.secretario'));
        $director = Profesor::find(config('contacto.director'));
        $dades = ['date' => FechaString(FechaPosterior($fct->hasta)),
            'consideracion' => $secretario->sexo === 'H' ? 'En' : 'Na',
            'secretario' => $secretario->FullName,
            'centro' => config('contacto.nombre'),
            'codigo' => config('contacto.codi'),
            'poblacion' => config('contacto.poblacion'),
            'provincia' => config('contacto.provincia'),
            'director' => $director->FullName
        ];
        
        $pdf = $this->hazPdf('dual.anexe_vii', $fct,$dades,'landscape','a4',10);
        return $pdf->stream();
    }
        
    
    
   

}
