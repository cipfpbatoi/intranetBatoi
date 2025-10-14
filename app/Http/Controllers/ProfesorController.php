<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Intranet\Botones\BotonBasico;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonImg;
use Intranet\Entities\Alumno;
use Intranet\Entities\Departamento;
use Intranet\Entities\Grupo;
use Intranet\Entities\Horario;
use Intranet\Entities\Profesor;
use Intranet\Http\Controllers\Auth\PerfilController;
use Intranet\Http\Traits\Autorizacion;
use Intranet\Http\Traits\Imprimir;
use Intranet\Mail\Comunicado;
use Jenssegers\Date\Date;
use Styde\Html\Facades\Alert;


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

use Autorizacion,
    Imprimir;

    const PROFILE_PROFESOR = 'profile.profesor';
    protected $model = 'Profesor';
    protected $vista = ['show' => 'perfil', 'edit' => 'perfil'];
    protected $gridFields = ['Xdepartamento', 'FullName', 'Xrol','fecha_baja','Substitut'];
    protected $perfil = 'profesor';
    protected $parametresVista = ['modal' => ['detalle','aviso']];

    public function index()
    {
        Session::forget('redirect');
        $todos = Profesor::orderBy('apellido1')
                ->with('Departamento')
                ->Plantilla()
                ->get();
        $this->iniBotones();
        return $this->grid($todos);
    }

    public function departamento()
    {
        Session::forget('redirect');
        $todos = Profesor::orderBy('apellido1')
                ->with('Departamento')
                ->with('Horari.Ocupacion')
                ->with('Horari.Modulo')
                ->Activo()
                ->get();
        $departamentos = Departamento::where('didactico',1)->get();

        foreach ($departamentos as $departamento) {
            if ($departamento->id != 99 ) {
                if($departamento->id == AuthUser()->departamento) {
                    $this->panel->setPestana($departamento->depcurt, true, self::PROFILE_PROFESOR, ['Xdepartamento', $departamento->depcurt], null, 1, $this->parametresVista);
                }
                else {
                    $this->panel->setPestana($departamento->depcurt, false, 'profile.profesorRes', ['Xdepartamento', $departamento->depcurt],null,null,$this->parametresVista);
                }
            }
        }
        $this->iniProfileBotones();
        return $this->grid($todos);
    }

    public function fse()
    {
        $grupo = Grupo::where('tutor', '=', AuthUser()->dni)->largestByAlumnes()->first();
        if (isset($grupo)) {
            return $this->hazPdf(
                'pdf.reunion.actaFSE',
                Alumno::misAlumnos()->OrderBy('apellido1')->OrderBy('apellido2')->get(),
                $grupo
            )->stream();
        } else {
            Alert::danger('No trobe el teu grup');
            return back();
        }

    }


    public function equipoDirectivo()
    {
        $equipo = $this->rol( config('roles.rol.direccion'));
        return $this->grid($equipo);
    }

    public function comissio()
    {
        $equipo = $this->rol( config('roles.rol.comissio_IiC'));
        return $this->grid($equipo);
    }

    public function rol($rol)
    {
        $this->panel->setPestana(
            config("roles.lor.$rol"),
            true,
            self::PROFILE_PROFESOR,
            null,
            null,
            1,
            $this->parametresVista
        );
        $this->panel->setBoton(
            'profile',
            new BotonIcon(
                'profesor.mensaje',
                ['icon' => 'fa-bell', 'class' => 'mensaje btn-success']
            )
        );
        $this->panel->setBoton(
            'profile',
            new BotonIcon(
                'profesor.horario',
                ['icon' => 'fa-user', 'class' => 'btn-success']
            )
        );
        $todos = Profesor::Activo()
            ->orderBy('apellido1', 'asc')
            ->orderBy('apellido2', 'asc')
            ->get();
        return $todos->filter(function ($item) use ($rol) {
            if (esRol($item->rol, $rol)) {
                return $item;
            }
        });
    }

    public function equipo($grupo)
    {
        $this->panel->setPestana('profile', true, self::PROFILE_PROFESOR, null, null, 1,$this->parametresVista);
        $this->panel->setBoton('index', new BotonBasico("profesor.colectivo", ['class' => 'colectivo btn btn-primary'], true));
        $this->panel->setBoton('profile', new BotonIcon('profesor.mensaje', ['icon' => 'fa-bell', 'class' => 'mensaje btn-success']));
        Session::put('colectivo', $grupo);
        return $this->grid(Profesor::orderBy('apellido1', 'asc')->orderBy('apellido2', 'asc')
                ->Grupo($grupo)->get());
    }
    
    public function update(Request $request, $id)
    {
        $new = Profesor::find($id);
        parent::update($request, $new);
        return back();
    }

    public function miApiToken()
    {
        $remitente = ['nombre' => 'Intranet', 'email' => config('contacto.host.email')];
        $user = AuthUser();
        $profesor = Profesor::find($user->dni);

        Mail::to($user->email)->send(new Comunicado($remitente, $profesor, 'email.apitoken'));

        Alert::info('Correu enviat');
        return back();
    }
    

    public function avisaColectivo(Request $request)
    {
        if (Session::get('colectivo')) {
            if (strlen(Session::get('colectivo'))<4) {
                foreach (Profesor::where('departamento', "=", Session::get('colectivo'))->get() as $profesor) {
                    avisa($profesor->dni, $request->explicacion != '' ? $request->explicacion : 'Te ha dado un toque.');
                }
            } else {
                foreach (Profesor::Grupo(Session::get('colectivo'))->get() as $profesor) {
                    avisa($profesor->dni, $request->explicacion != '' ? $request->explicacion : 'Te ha dado un toque.');
                }
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
        return $this->hazPdf('pdf.carnet', Profesor::where('dni',$profesor)->get(), [Date::now()->format('Y'), 'Professorat - Teacher'], 'portrait', [85.6, 53.98])->stream();
    }

    public function tarjeta($profesor)
    {
        $profesor = Profesor::findOrFail($profesor);
        $cargo = 'Professorat';
        if (esRol($profesor->rol, config('roles.rol.direccion'))) {
            switch ($profesor->dni) {
                case config('avisos.director'): $cargo = 'Director';
                    break;
                case config('avisos.secretario'): $cargo = 'Secretària';
                    break;
                case config('avisos.vicedirector'): $cargo = 'ViceDirector';
                    break;
                case config('avisos.jefeEstudios'): $cargo = "Cap d'Estudis";
                    break;
                case config('avisos.jefeEstudios2'): $cargo = "Cap d'Estudis";
                    break;
                default: $cargo = 'Professorat';
            }
        }
        if ($cargo == 'Professorat' && esRol($profesor->rol, config('roles.rol.tutor'))) {
            $cargo .= ' - Tutor';
        }
        return $this->hazPdf('pdf.tarjeta', $profesor,  $cargo, 'portrait','a4',2)->stream();
    }

    

    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('profesor.horario'));
        $this->panel->setBoton('grid', new BotonImg('profesor.edit', inRol('direccion')));
        $this->panel->setBoton('grid', new BotonImg('profesor.carnet',inRol('direccion')));
        $this->panel->setBoton('grid', new BotonImg('profesor.muestra', inRol('direccion')));
        $this->panel->setBoton('grid', new BotonImg('profesor.horario-cambiar', ['img' => 'fa-th', 'roles' => config('roles.rol.direccion')
            ,'where' => ['dni','existe','/horarios/$.json']]));
        $this->panel->setBoton('grid', new BotonImg('profesor.horario-cambiar', ['img' => 'fa-th-large', 'roles' => config('roles.rol.direccion')
            ,'where' => ['dni','noExiste','/horarios/$.json']]));
         $this->panel->setBoton('grid', new BotonImg('horario.cambiar', ['img' => 'fa-flash', 'roles' => config('roles.rol.administrador')
            ]));
        $this->panel->setBoton('grid', new BotonImg('profesor.change', ['img' => 'fa-user','roles' => config('roles.rol.administrador')]));
     }
    protected function iniProfileBotones()
    {
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
                    if (isset(json_decode(Storage::disk('local')->get('/horarios/'.$profesor->dni.'.json'))->obs)) {
                        $observaciones[$profesor->dni] = json_decode(Storage::disk('local')->get('/horarios/'.$profesor->dni.'.json'))->obs;
                    }
                    else {
                        $observaciones[$profesor->dni] = '';
                    }
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
