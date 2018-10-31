<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\Grupo;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\FctConvalidacion;
use DB;
use Styde\Html\Facades\Alert;
use Intranet\Botones\Panel;
use Intranet\Entities\Documento;
use Illuminate\Support\Facades\Session;

class FctAlumnoController extends IntranetController
{

    protected $perfil = 'profesor';
    protected $model = 'AlumnoFct';
    protected $gridFields = ['Nombre', 'Centro','Instructor','desde','hasta','horas','periode'];
    protected $profile = false;

    public function search()
    {
        return AlumnoFct::misFcts()->esAval()->orderBy('idAlumno')->orderBy('desde')->get();
    }

    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('fctAl.delete'));
        $this->panel->setBoton('grid', new BotonImg('fctAl.edit',['where'=>['asociacion', '==', '1']]));
        $this->panel->setBoton('grid', new BotonImg('fctAl.pdf',['where'=>['asociacion', '==', '1']]));
        $this->panel->setBoton('grid', new BotonImg('fctAl.pdfInstructor',['img'=>'fa-file-pdf-o','where'=>['asociacion', '==', '1']]));
        $this->panel->setBoton('grid', new BotonImg('fctAl.email',['orWhere'=>['correoAlumno','==','0','correoInstructor','==','0']]));
        $this->panel->setBoton('index', new BotonBasico("fct.create", ['class' => 'btn-info','roles' => config('roles.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fctal.convalidacion", ['class' => 'btn-info','roles' => config('roles.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pg0301.print",['roles' => config('roles.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0301.print",['roles' => config('roles.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0401.print",['roles' => config('roles.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0402.print",['roles' => config('roles.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0601.print",['roles' => config('roles.rol.tutor')]));
        $find = Documento::where('propietario', AuthUser()->FullName)->where('tipoDocumento','Qualitat')
                ->where('curso',Curso())->first();
        if (!$find) $this->panel->setBoton('index', new BotonBasico("fct.upload", ['class' => 'btn-info','roles' => config('roles.rol.tutor')]));
        else $this->panel->setBoton('index', new BotonBasico("documento.$find->id.edit", ['class' => 'btn-info','roles' => config('roles.rol.tutor')]));
        Session::put('redirect', 'FctAlumnoController@index');
    }
        //

    public function convalidacion()
    {
        $elemento = new FctConvalidacion();
        $default = $elemento->fillDefautOptions(); // ompli caracteristiques dels camps
        $modelo = 'Fct';
        return view($this->chooseView('create'), compact('elemento', 'default', 'modelo'));
    }
} 