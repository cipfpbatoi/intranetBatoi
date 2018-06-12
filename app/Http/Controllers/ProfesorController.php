<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Http\Controllers\Auth\PerfilController;
use Intranet\Entities\Profesor;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Cache;
use Intranet\Entities\Departamento;
use Intranet\Entities\Horario;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Styde\Html\Facades\Alert;
use Intranet\Jobs\SendEmail;


class ProfesorController extends PerfilController
{
    /*     * *********************************************
     * model -> es el modelo de la base de datos
     * vista -> por defecto se utiliza la vista Intranet de la forma intranet.metodo
     *       -> si para un metodo se cambia la vista se puede indicar de dos formas
     *       -> si solo pongo el nombre se supono que cargo la vista modelo.nombre
     *       -> puedo ponerla entera ejem: intranet.panel
     * gridfields -> los campos que se muestran en el listado de la vista index
     */

use traitAutorizar,
    traitImprimir;

    protected $model = 'Profesor';
    protected $vista = ['show' => 'perfil', 'edit' => 'perfil'];
    protected $gridFields = ['Xdepartamento', 'FullName', 'Xrol'];
    protected $perfil = 'profesor';

    public function index()
    {
        Session::forget('redirect');
        $todos = Profesor::orderBy('apellido1')
                ->Activo()
                ->get();
        $departamentos = Profesor::join('departamentos', 'profesores.departamento', '=', 'departamentos.id')
                ->select('departamentos.*')
                ->distinct()
                ->get();
        foreach ($departamentos as $departamento) {
            $this->panel->setPestana($departamento->depcurt, false, 'profile.profesor', ['Xdepartamento', $departamento->depcurt]);
        }
        $this->iniBotones();
        return $this->grid($todos);
    }

    public function update(Request $request, $id)
    {
        $new = Profesor::find($id);
        parent::update($request, $new);
        return back();
    }

    public function departamento()
    {
        $todos = Profesor::where('departamento', '=', AuthUser()->departamento)
                ->Activo()
                ->orderBy('apellido1', 'asc')
                ->orderBy('apellido2', 'asc')
                ->get();
        $this->iniBotones();
        $this->panel->setPestana('profile', true, 'profile.profesor', null, null, 1);
        $this->panel->setBoton('index', new BotonBasico("profesor.colectivo", ['class' => 'colectivo btn btn-primary'], true));
        Session::put('colectivo', AuthUser()->departamento);
        return $this->grid($todos);
    }

    public function equipoDirectivo()
    {
        $todos = Profesor::Activo()
                ->orderBy('apellido1', 'asc')
                ->orderBy('apellido2', 'asc')
                ->get();
        $equipo = $todos->filter(function($item) {
            if (esRol($item->rol, config('constants.rol.direccion')))
                return $item;
        });
        $this->panel->setPestana('profile', true, 'profile.profesor', null, null, 1);
        $this->panel->setBoton('profile', new BotonIcon('profesor.mensaje', ['icon' => 'fa-bell', 'class' => 'mensaje btn-success']));
        $this->panel->setBoton('profile', new BotonIcon('profesor.horario', ['icon' => 'fa-user', 'class' => 'btn-success']));

        Session::put('colectivo', 'Equipo directivo');
        return $this->grid($equipo);
    }

    public function equipo($grupo)
    {
        $todos = Profesor::orderBy('apellido1', 'asc')
                ->orderBy('apellido2', 'asc')
                ->Grupo($grupo)
                ->get();
        $this->panel->setPestana('profile', true, 'profile.profesor', null, null, 1);
        $this->panel->setBoton('index', new BotonBasico("profesor.colectivo", ['class' => 'colectivo btn btn-primary'], true));
        $this->panel->setBoton('profile', new BotonIcon('profesor.mensaje', ['icon' => 'fa-bell', 'class' => 'mensaje btn-success']));
        Session::put('colectivo', $grupo);
        return $this->grid($todos);
    }

    public function miApiToken()
    {
        $remitente = ['nombre' => 'Intranet', 'email' => 'intranet@cipfpbatoi.es'];
        dispatch(new SendEmail(AuthUser()->email, $remitente, 'email.apitoken', Profesor::find(AuthUser()->dni)));
        Alert::info('Correu enviat');
        return back();
    }

    public function avisaColectivo(Request $request)
    {
        if (Session::get('colectivo')) {
            foreach (Profesor::where('departamento', Session::get('colectivo'))->get() as $profesor) {
                avisa($profesor->dni, $request->explicacion != '' ? $request->explicacion : 'Te ha dado un toque.');
            }
            foreach (Profesor::Grupo(Session::get('colectivo'))->get() as $profesor) {
                avisa($profesor->dni, $request->explicacion != '' ? $request->explicacion : 'Te ha dado un toque.');
            }
        }
        return back();
    }

    public function alerta(Request $request, $id)
    {
        avisa($id, $request->explicacion != '' ? $request->explicacion : 'Te ha dado un toque.');
        return back();
    }

    

    public function carnet($profesor)
    {
        return $this->hazPdf('pdf.carnet', Profesor::where('dni',$profesor)->get(), [Date::now()->format('Y'), 'Professor - Teacher'], 'portrait', [85.6, 53.98])->stream();
    }

    public function tarjeta($profesor)
    {
        $profesor = Profesor::findOrFail($profesor);
        $cargo = 'Professor';
        if (esRol($profesor->rol, config('constants.rol.direccion')))
            switch ($profesor->dni) {
                case config('constants.contacto.director'): $cargo = 'Director';
                    break;
                case config('constants.contacto.secretario'): $cargo = 'Secretària';
                    break;
                case config('constants.contacto.vicedirector'): $cargo = 'ViceDirector';
                    break;
                case config('constants.contacto.jefeEstudios'): $cargo = "Cap d'Estudis";
                    break;
                case config('constants.contacto.jefeEstudios2'): $cargo = "Cap d'Estudis";
                    break;
            }
        if ($cargo == 'Professor' && esRol($profesor->rol, config('constants.rol.tutor'))) 
                $cargo .= ' - Tutor';
        return $this->hazPdf('pdf.tarjeta', $profesor,  $cargo, 'portrait','a4',2)->stream();
    }

    

    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('profesor.horario'));
        $this->panel->setBoton('grid', new BotonImg('profesor.edit', ['roles' => config('constants.rol.direccion')]));
        $this->panel->setBoton('grid', new BotonImg('profesor.carnet', ['roles' => config('constants.rol.direccion')]));
        $this->panel->setBoton('grid', new BotonImg('profesor.muestra', ['roles' => config('constants.rol.direccion')]));
        $this->panel->setBoton('grid', new BotonImg('profesor.horario-cambiar', ['img' => 'fa-th', 'roles' => config('constants.rol.direccion')
            ,'where' => ['dni','existe','/horarios/$.json']]));
        $this->panel->setBoton('grid', new BotonImg('profesor.horario-cambiar', ['img' => 'fa-th-large', 'roles' => config('constants.rol.direccion')
            ,'where' => ['dni','noExiste','/horarios/$.json']]));
         $this->panel->setBoton('grid', new BotonImg('horario.cambiar', ['img' => 'fa-flash', 'roles' => config('constants.rol.administrador')
            ]));
        $this->panel->setBoton('grid', new BotonImg('profesor.change', ['img' => 'fa-user','roles' => config('constants.rol.administrador')]));
        $this->panel->setBoton('profile', new BotonIcon('profesor.horario', ['icon' => 'fa-user', 'class' => 'btn-success']));
        $this->panel->setBoton('profile', new BotonIcon('profesor.mensaje', ['icon' => 'fa-bell', 'class' => 'mensaje btn-success']));
        $this->panel->setBoton('profile', new BotonIcon('profesor.carnet', ['icon' => 'fa-credit-card', 'where' => ['dni', '==', AuthUser()->dni]]));
        $this->panel->setBoton('profile', new BotonIcon('profesor.tarjeta', ['icon' => 'fa-file-image-o', 'where' => ['dni', '==', AuthUser()->dni]]));
    }

    protected function horario($id)
    {
        $horario = Horario::HorarioSemanal($id);
        $profesor = Profesor::findOrFail($id);
        return view('horario.profesor', compact('horario', 'profesor'));
    }

    protected function horarioCambiar($id)
    {
        $horario = Horario::HorarioSemanal($id);
        $profesor = Profesor::findOrFail($id);
        return view('horario.profesor-cambiar', compact('horario', 'profesor'));
    }
    //-----------------------------
    //impressió de tots els horaris
    //-----------------------------
    protected function imprimirHorarios()
    {
        $profesores = Profesor::Activo()->get();
        $horarios = [];
        $observaciones = [];
        foreach ($profesores as $profesor){
            if (Storage::disk('local')->exists('/horarios/'.$profesor->dni.'.json')){
                    if (isset(json_decode(Storage::disk('local')->get('/horarios/'.$profesor->dni.'.json'))->obs))
                        $observaciones[$profesor->dni] = json_decode(Storage::disk('local')->get('/horarios/'.$profesor->dni.'.json'))->obs;
                    else
                        $observaciones[$profesor->dni] = '';
                    $horarios[$profesor->dni] = Horario::HorarioSemanal($profesor->dni);
            }
        }
        return $this->hazPdf('pdf.horarios', $horarios,$observaciones)->stream();
    }
    
    //-------------------------------
    // canvi de professor en calent -
    //-------------------------------
    protected function change($idProfesor)
    {
        Session::put('userChange', AuthUser()->dni);
        Auth::login(Profesor::find($idProfesor));
        return redirect('/');
    }
    protected function backChange()
    {
        Auth::login(Profesor::find(Session::get('userChange')));
        Session::forget('userChange');
        return redirect('/');
    }

}
