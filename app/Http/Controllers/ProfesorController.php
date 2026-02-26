<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Application\Horario\HorarioService;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\UI\Botones\BotonBasico;
use Intranet\UI\Botones\BotonIcon;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Alumno;
use Intranet\Entities\Departamento;
use Intranet\Entities\Falta_profesor;
use Intranet\Http\Controllers\Auth\PerfilController;
use Intranet\Http\Requests\ProfesorUpdateRequest;
use Intranet\Http\Traits\Autorizacion;
use Intranet\Http\Traits\Core\Imprimir;
use Intranet\Mail\Comunicado;
use Intranet\Services\UI\FormBuilder;
use Illuminate\Support\Carbon;
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

    private ?ProfesorService $profesorService = null;
    private ?HorarioService $horarioService = null;
    private ?GrupoService $grupoService = null;

    public function __construct(
        ?ProfesorService $profesorService = null,
        ?HorarioService $horarioService = null,
        ?GrupoService $grupoService = null
    )
    {
        parent::__construct();
        $this->profesorService = $profesorService;
        $this->horarioService = $horarioService;
        $this->grupoService = $grupoService;
    }

    private function profesores(): ProfesorService
    {
        if ($this->profesorService === null) {
            $this->profesorService = app(ProfesorService::class);
        }

        return $this->profesorService;
    }

    private function horarios(): HorarioService
    {
        if ($this->horarioService === null) {
            $this->horarioService = app(HorarioService::class);
        }

        return $this->horarioService;
    }

    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
    }


    public function index()
    {
        Session::forget('redirect');
        $todos = $this->profesores()->plantillaOrderedWithDepartamento();
        $this->iniBotones();
        return $this->grid($todos);
    }



    public function departamento()
    {
        Session::forget('redirect');
        $departamentos = Departamento::query()
            ->where('didactico', 1)
            ->where('id', '!=', 99)
            ->orderBy('depcurt')
            ->get();
        $departamentosIds = $departamentos->pluck('id')->all();

        $sesionActual = (int) (sesion(Hora(now())) ?? 1);
        $diaActual = (string) (nameDay(hoy()) ?? 'L');

        $todos = $this->profesores()->activosByDepartamentosWithHorario($departamentosIds, $diaActual, $sesionActual);

        $fichajesHoy = Falta_profesor::query()
            ->select('idProfesor', 'id', 'salida')
            ->where('dia', hoy())
            ->orderBy('id')
            ->get()
            ->groupBy('idProfesor')
            ->map(function ($fichajes) {
                $ultim = $fichajes->last();
                return $ultim !== null && $ultim->salida === null;
            });

        foreach ($todos as $profesor) {
            $profesor->inside = (bool) ($fichajesHoy[$profesor->dni] ?? false);
        }

        foreach ($departamentos as $departamento) {
            if($departamento->id == AuthUser()->departamento) {
                $this->panel->setPestana($departamento->depcurt, true, self::PROFILE_PROFESOR, ['Xdepartamento', $departamento->depcurt], null, 1, $this->parametresVista);
            }
            else {
                $this->panel->setPestana($departamento->depcurt, false, 'profile.profesorRes', ['Xdepartamento', $departamento->depcurt],null,null,$this->parametresVista);
            }
        }
        $this->iniProfileBotones();
        return $this->grid($todos);
    }

    public function fse()
    {
        $grupo = $this->grupos()->largestByTutor(AuthUser()->dni);
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
        $todos = $this->profesores()->activosOrdered();
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
        return $this->grid($this->profesores()->byGrupo($grupo));
    }
    
    public function update(Request $request, $id)
    {
        $this->validate($request, (new ProfesorUpdateRequest())->rules());
        $new = $this->profesores()->findOrFail((string) $id);
        $this->authorize('update', $new);
        parent::update($request, $new);
        return back();
    }

    public function miApiToken()
    {
        $remitente = ['nombre' => 'Intranet', 'email' => config('contacto.host.email')];
        $user = AuthUser();
        $profesor = $this->profesores()->find($user->dni);

        Mail::to($user->email)->send(new Comunicado($remitente, $profesor, 'email.apitoken'));

        Alert::info('Correu enviat');
        return back();
    }
    

    public function avisaColectivo(Request $request)
    {
        if (Session::get('colectivo')) {
            if (strlen(Session::get('colectivo'))<4) {
                foreach ($this->profesores()->byDepartamento(Session::get('colectivo')) as $profesor) {
                    avisa($profesor->dni, $request->explicacion != '' ? $request->explicacion : 'Te ha dado un toque.');
                }
            } else {
                foreach ($this->profesores()->byGrupo(Session::get('colectivo')) as $profesor) {
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
        $record = $this->profesores()->find((string) $profesor);
        $profesores = $record ? collect([$record]) : collect();
        return $this->hazPdf('pdf.carnet', $profesores, [Carbon::now()->format('Y'), 'Professorat - Teacher'], 'portrait', [85.6, 53.98])->stream();
    }

    public function tarjeta($profesor)
    {
        $profesor = $this->profesores()->findOrFail((string) $profesor);
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
        $horario = $this->horarios()->semanalByProfesor((string) $id);
        $profesor = $this->profesores()->findOrFail((string) $id);
        return view('horario.profesor', compact('horario', 'profesor'));
    }

    //-----------------------------
    //impressió de tots els horaris
    //-----------------------------
    protected function imprimirHorarios()
    {
        $profesores = $this->profesores()->activosOrdered();
        $horarios = [];
        $observaciones = [];
        foreach ($profesores as $profesor){
            $ruta = '/horarios/'.$profesor->dni.'.json';
            if (Storage::disk('local')->exists($ruta)){
                    $json = json_decode(Storage::disk('local')->get($ruta));
                    $observaciones[$profesor->dni] = $json->obs ?? '';
                    $horarios[$profesor->dni] = $this->horarios()->semanalByProfesor((string) $profesor->dni);
            }
        }
        return $this->hazPdf('pdf.horarios', $horarios,$observaciones)->stream();
    }
    
    //-------------------------------
    // canvi de professor en calent -
    //-------------------------------
    protected function change($idProfesor)
    {
        $profesor = $this->profesores()->find((string) $idProfesor);
        if (!$profesor) {
            Alert::danger('Professor no trobat');
            return back();
        }

        Session::put('userChange', AuthUser()->dni);
        Auth::login($profesor);
        return redirect()->route('home.profesor');
    }
    protected function backChange()
    {
        $dniOriginal = Session::get('userChange');
        $profesor = $dniOriginal ? $this->profesores()->find((string) $dniOriginal) : null;
        if (!$profesor) {
            Session::forget('userChange');
            Alert::danger('No s\'ha pogut restaurar la sessió original');
            return redirect()->route('home.profesor');
        }

        Auth::login($profesor);
        Session::forget('userChange');
        return redirect()->route('home.profesor');
    }

}
