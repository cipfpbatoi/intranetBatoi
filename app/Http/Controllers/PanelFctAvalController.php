<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\Grupo;
use Intranet\Entities\AlumnoFctAval;
use DB;
use Styde\Html\Facades\Alert;
use Intranet\Botones\Panel;
use Intranet\Entities\Documento;
use Illuminate\Support\Facades\Session;

class PanelFctAvalController extends IntranetController
{

    protected $perfil = 'profesor';
    protected $model = 'AlumnoFctAval';
    protected $gridFields = ['Nombre', 'Qualificacio', 'Projecte', 'periode','hasta'];
    protected $profile = false;

    public function search()
    {
        $nombres = AlumnoFctAval::select('idAlumno')->distinct()->misFcts()->esAval()->get()->toArray();
        $todas = collect();
        foreach ($nombres as $nombre){
            $todas->push(AlumnoFctAval::misFcts()->esAval()->where('idAlumno',$nombre['idAlumno'])->orderBy('idAlumno')->first());
        }
        return $todas;
        
    }

    protected function iniBotones()
    {

        $this->panel->setPestana('Resum', false, 'profile.resumenfct');
        
        // Botó de qualitat
        $find = Documento::where('propietario', AuthUser()->FullName)->where('tipoDocumento', 'Qualitat')
                        ->where('curso', Curso())->first();
        if (!$find)
            $this->panel->setBoton('index', new BotonBasico("fct.upload", ['class' => 'btn-info', 'roles' => config('roles.rol.tutor')]));
        else
            $this->panel->setBoton('index', new BotonBasico("documento.$find->id.edit", ['class' => 'btn-info', 'roles' => config('roles.rol.tutor')]));
        Session::put('redirect', 'PanelFctAvalController@index');

        //Botó d'acta
        if (Grupo::QTutor()->first() && Grupo::QTutor()->first()->acta_pendiente == false) {
            $this->panel->setBoton('index', new BotonBasico("fct.acta", ['class' => 'btn-info', 'roles' => config('roles.rol.tutor')]));
        } else
            Alert::message("L'acta pendent esta en procés", 'info');
        
        // Botons avaluar FCT
        $this->panel->setBoton('grid', new BotonImg('fct.apte', ['img' => 'fa-hand-o-up', 'where' => ['calificacion', '!=', '1', 'actas', '==', 0, 'asociacion', '==', 1]]));
        $this->panel->setBoton('grid', new BotonImg('fct.noApte', ['img' => 'fa-hand-o-down', 'where' => ['calProyecto', '<', '5', 'calificacion', '!=', '0', 'actas', '==', 0, 'asociacion', '==', 1]]));

        //Botons Projecte
        if (Grupo::QTutor()->first() && Grupo::QTutor()->first()->proyecto) {
            $this->panel->setBoton('grid', new BotonImg('fct.proyecto', ['img' => 'fa-file', 'roles' => config('roles.rol.tutor'),
                'where' => ['calProyecto', '<', '1', 'actas', '<', 2]]));
            $this->panel->setBoton('grid', new BotonImg('fct.noProyecto', ['img' => 'fa-toggle-off', 'roles' => config('roles.rol.tutor'),
                'where' => ['calProyecto', '<', '0', 'actas', '<', 2]]));
            $this->panel->setBoton('grid', new BotonImg('fct.nuevoProyecto', ['img' => 'fa-toggle-on', 'roles' => config('roles.rol.tutor'),
                'where' => ['calProyecto', '<', '5', 'calProyecto', '>=', 0, 'actas', '==', 2]]));
            $this->panel->setBoton('grid', new BotonImg('fct.modificaNota', ['img' => 'fa-edit', 'roles' => config('roles.rol.tutor'),
                'where' => ['calProyecto', '>=', 0, 'actas', '<', 2]]));
        }
        $this->panel->setBoton('grid', new BotonImg('fct.empresa', ['img' => 'fa-square-o', 'roles' => config('roles.rol.tutor'),
            'where' => ['insercion', '==', '0','asociacion','==',1]]));
        $this->panel->setBoton('grid', new BotonImg('fct.empresa', ['img' => 'fa-check-square-o', 'roles' => config('roles.rol.tutor'),
            'where' => ['insercion', '==', '1','asociacion','==',1]]));

    }
    
    

    protected function apte($id)
    {
        $fct = AlumnoFctAval::find($id);
        $fct->calificacion = 1;
        $fct->save();

        return back();
    }

    protected function noApte($id)
    {
        $fct = AlumnoFctAval::find($id);
        $fct->calificacion = 0;
        $fct->calProyecto = null;
        $fct->save();

        return back();
    }

    protected function noProyecto($id)
    {
        $fct = AlumnoFctAval::find($id);
        $fct->calProyecto = 0;
        $fct->save();

        return back();
    }

    protected function nuevoProyecto($id)
    {
        $fct = AlumnoFctAval::find($id);
        $fct->calProyecto = null;
        $fct->actas = 1;
        $fct->save();

        return back();
    }
     public function empresa($id){
       $fct = AlumnoFctAval::find($id);
       $fct->insercion = $fct->insercion?0:1;
       $fct->save();
       return $this->redirect();
    }

    public function demanarActa()
    {
        $grupo = Grupo::QTutor()->first();
        if ($grupo->acta_pendiente)
            Alert::message("L'acta pendent esta en procés", 'info');
        else {
            $fcts = AlumnoFctAval::MisFcts()->NoAval()->get();
            $testigo = false;
            foreach ($fcts as $fct) {
                if ($grupo->proyecto) {
                    if (isset($fct->calProyecto)) {
                        $fct->actas = 3;
                        $testigo = true;
                        $fct->save();
                    }
                } else {
                    if (isset($fct->calificacion)) {
                        $fct->actas = 3;
                        $testigo = true;
                        $fct->save();
                    }
                }
            }
            if ($testigo) {
                $grupo->acta_pendiente = 1;
                $grupo->save();
                avisa(config('contacto.jefeEstudios2'), "Acta pendent grup $grupo->nombre", config('contacto.host.web')."/direccion/$grupo->codigo/acta");
                Alert::message('Acta demanada', 'info');
            } else
                Alert::message('No tens nous alumnes per ser avaluats', 'warning');
        }
        return back();
    }

} 