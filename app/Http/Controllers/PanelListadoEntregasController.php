<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonBasico;
use Intranet\Botones\BotonImg;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Horario;
use Intranet\Entities\Profesor;
use Intranet\Services\GestorService;
use Styde\Html\Facades\Alert;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Modulo_ciclo;
use Intranet\Entities\Resultado;
use Intranet\Entities\Reunion;
use Illuminate\Http\Request;
use Intranet\Entities\OrdenReunion;
use Intranet\Entities\TipoReunion;
use Intranet\Entities\Actividad;
use Intranet\Entities\Documento;
use Intranet\Entities\Programacion;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;
use Intranet\Entities\Poll\Vote;


class PanelListadoEntregasController extends BaseController
{
    use traitImprimir;

    const ROLES_ROL_JEFE_DPTO = 'roles.rol.jefe_dpto';
    protected $model = 'Modulo_grupo';
    protected $gridFields = ['literal','seguimiento','profesor'];
    protected $parametresVista = ['modal' => ['infDpto']];

    public function search()
    {
        // Obtenim els mòduls rellevants
        $modulos = hazArray(
            Modulo_ciclo::whereIn(
                'idModulo',
                hazArray(Horario::distinct()->get(), 'modulo')
            )->where(
                'idDepartamento',
                AuthUser()->departamento
            )->get(),
            'id'
        );

        // Retornem només aquells modulo_grupo que tenen alumnes
        return Modulo_grupo::with('Grupo', 'resultados', 'ModuloCiclo')
            ->whereIn('idModuloCiclo', $modulos)
            ->whereHas('Grupo', function ($query) {
                $query->whereHas('Alumnos'); // Comprovar que el grupo té alumnes
            })
            ->get();
    }

    public function iniBotones()
    {
        if (!$this->faltan()){
            if ($this->existeInforme())
            {
                $this->panel->setBoton('index',new BotonBasico('Infdepartamento.edit',['roles' => config(self::ROLES_ROL_JEFE_DPTO),'id'=>'generar']));
            }
            else
            {
                $this->panel->setBoton('index',
                    new BotonBasico(
                        'Infdepartamento.create',
                        [
                            'roles' => config(self::ROLES_ROL_JEFE_DPTO),
                            'id'=>'generar'
                        ]
                    )
                );
            }
        }
        else {
            $this->panel->setBoton('index',new BotonBasico('Infdepartamento.avisa',['roles' => config(self::ROLES_ROL_JEFE_DPTO)]));
        }
        if ($reunion = $this->existeInforme())
        {
            $this->panel->setBoton('index', new BotonBasico('Infdepartamento.pdf.'.$reunion->id,['roles' => config(self::ROLES_ROL_JEFE_DPTO)]));
        }
        $this->panel->setBoton('grid', new BotonImg('Infdepartamento.aviso', ['img' => 'fa-bell', 'where' => ['seguimiento', '!=', 1], 'roles' => config(self::ROLES_ROL_JEFE_DPTO)]));
    }
    
    
    public function hazInformeTrimestral(Request $request)
    {
        $pdf = $this->hazPdfInforme($request->observaciones, $request->trimestre, $request->proyectos);

        // cree reunio
        DB::transaction(function () use ($pdf, $request) {
            $reunion = new Reunion();
            $reunion->tipo = 10;
            $reunion->curso = Curso();
            $reunion->fecha = Ayer();
            $reunion->descripcion = 'Informe Trimestral';
            $reunion->idProfesor = AuthUser()->dni;
            $reunion->idEspacio = 'Intranet';
            $reunion->archivada = 1;
            $reunion->numero = 20 + $request->trimestre;
            $reunion->save();
            // cree ordre del dia
            $orden = new OrdenReunion();
            $orden->idReunion = $reunion->id;
            $orden->orden = 1;
            $orden->descripcion = 'Observacions';
            $orden->resumen = $request->observaciones;
            $orden->save();

            if (isset($request->proyectos)) {
                $orden = new OrdenReunion();
                $orden->idReunion = $reunion->id;
                $orden->orden = 2;
                $orden->descripcion = 'Projectes';
                $orden->resumen = $request->proyectos;
                $orden->save();
            }

            $nom = 'Informe_' . $reunion->id . '.pdf';
            $directorio = 'gestor/' . Curso() . '/Reunion';
            $nomComplet = $directorio . '/' . $nom;
            $pdf->save(storage_path('/app/' . $nomComplet));
            $reunion->fichero = $nomComplet;
            $reunion->save();

            $gestor = new GestorService($reunion);
            $gestor->save(
                    ['propietario' => AuthUser()->FullName,
                        'tipoDocumento' => 'Acta',
                        'descripcion' => $reunion->descripcion,
                        'fichero' => $reunion->fichero,
                        'supervisor' => $reunion->idProfesor,
                        'grupo' => str_replace(' ', '_', $reunion->Xgrupo),
                        'tags' => TipoReunion::find($reunion->tipo)->vliteral . ',' . config('auxiliares.numeracion')[$reunion->numero],
                        'created_at' => new Date($reunion->fecha),
                        'rol' => config('roles.rol.profesor')]);

        });
        return back();
    }

    public function modificaInformeTrimestral(Request $request)
    {

        $pdf = $this->hazPdfInforme($request->observaciones, $request->trimestre,$request->proyectos);
        $oR = OrdenReunion::where('idReunion', $request->reunion)
                ->where('orden', 1)
                ->first();
        $oR->resumen = $request->observaciones;
        $oR->save();
        if (isset($request->proyectos)) {
            $oR = OrdenReunion::where('idReunion', $request->reunion)
                    ->where('orden', 2)
                    ->first();
            if ($oR) {
                $oR->resumen = $request->proyectos;
                $oR->save();
            } else {
                $orden = new OrdenReunion();
                $orden->idReunion = $request->reunion;
                $orden->orden = 2;
                $orden->descripcion = 'Projectes';
                $orden->resumen = $request->proyectos;
                $orden->save();
            }

        }

        $nom = 'Informe_' . $request->reunion . '.pdf';
        $directorio = 'gestor/' . Curso() . '/Reunion';
        $nomComplet = $directorio . '/' . $nom;
        if (file_exists(storage_path('/app/' . $nomComplet))) {
            unlink(storage_path('/app/' . $nomComplet));
        }
        $pdf->save(storage_path('/app/' . $nomComplet));

        return back();
    }
    
    public function avisaTodos()
    {
        foreach (Modulo_grupo::whereIn('idModuloCiclo', hazArray(Modulo_ciclo::where('idDepartamento', AuthUser()->departamento)->get(), 'id', 'id'))->get() as $modulo)
        {
            if ($modulo->seguimiento == 0) {
                $this->avisaFaltaEntrega($modulo->id);
            }
        }
        return back();     
    }
    
    public function avisaFaltaEntrega($id)
    {
        $modulo = Modulo_grupo::find($id);
        foreach ($modulo->profesores() as $profesor) {
                $texto = "Et falta per omplir el seguiment de l'avaluacio '" .
                        "' del mòdul '$modulo->Xmodulo' del Grup '$modulo->Xgrupo'";
                avisa($profesor['idProfesor'], $texto);
        }
        Alert::info('Aviss enviat');
        return back();
    }
    
    protected function pdf($id)
    {
        return response()->file(storage_path('app/' . Reunion::findOrFail($id)->fichero));
    }
    
    public static function existeInforme()
    {
        return Reunion::Select('id', 'numero')
            ->Tipo(10)
            ->where('numero', evaluacion() +19)
            ->Convocante(AuthUser()->dni)
            ->first();
    }
    private function hazPdfInforme($observaciones, $trimestre, $proyectos = null)
    {

        $dep = AuthUser()->departamento;
        $fechas = config("curso.evaluaciones.$trimestre");
        $actividades = Actividad::Departamento($dep)
                ->where('desde', '>=', $fechas[0])
                ->where('hasta', '<=', $fechas[1])
                ->where('estado', 3)
                ->orderBy('desde')
                ->get();
        $todos = Resultado::Departamento($dep)->with('ModuloGrupo')->get();
        $profesores = hazArray(
            Profesor::where('departamento', $dep)->get(),
            'dni',
            'dni'
        );
        $totales = array();

        $resultados = collect();
        foreach ($todos as $resultado) {
            $modulo = $resultado->ModuloGrupo;
            if ($modulo) {
                $curso = $modulo->Grupo->curso;
                $tipoCiclo = $modulo->ModuloCiclo->Ciclo->tipo;
                if (config("curso.trimestres.$tipoCiclo.$trimestre.$curso") == $resultado->evaluacion) {
                    $resultados->add($resultado);
                }
            }
        }
        $resultados->sortBy('Modulo');

        if ($trimestre == 3) {
            $programaciones = Programacion::Departamento(AuthUser()->departamento)
                    ->whereNotNull('propuestas')
                    ->get();
            // matriculas
            foreach (Ciclo::where('departamento', $dep)->get() as $ciclo) {
                foreach ($ciclo->Grupos as $grupo) {
                    if (isset($totales[$ciclo->ciclo]['matriculas'])) {
                        $totales[$ciclo->ciclo]['matriculas'] += count($grupo->Alumnos);
                        $totales[$ciclo->ciclo]['fct'] += $grupo->AvalFct;
                        $totales[$ciclo->ciclo]['insercio'] += $grupo->Colocados;
                    } else {
                        $totales[$ciclo->ciclo]['matriculas'] = count($grupo->Alumnos);
                        $totales[$ciclo->ciclo]['fct'] = $grupo->AvalFct;
                        $totales[$ciclo->ciclo]['insercio'] = $grupo->Colocados;
                        $totales[$ciclo->ciclo]['votesAlFct'] = 0;
                        $totales[$ciclo->ciclo]['sumAlFct'] = 0;
                        $totales[$ciclo->ciclo]['votesTuFct'] = 0;
                        $totales[$ciclo->ciclo]['sumTuFct'] = 0;
                        $totales[$ciclo->ciclo]['votesSatis'] = 0;
                        $totales[$ciclo->ciclo]['sumSatis'] = 0;
                    }
                }

                $centres = array();
                foreach ($ciclo->fcts as $fct) {
                    $centres[$fct->Colaboracion->idCentro] = 1;
                    foreach (Vote::where('option_id', 21)->where('idOption1', $fct->id)->get() as $vote) {
                        $totales[$ciclo->ciclo]['votesAlFct'] += $vote->value;
                        $totales[$ciclo->ciclo]['sumAlFct'] += 1;
                    }
                    foreach (Vote::where('option_id',26)->where('idOption1', $fct->id)->get() as $vote) {
                        $totales[$ciclo->ciclo]['votesTuFct'] += $vote->value;
                        $totales[$ciclo->ciclo]['sumTuFct'] += 1;
                    }
                }
                $totales[$ciclo->ciclo]['centres'] = count($centres);
            }

            foreach (Vote::where('option_id', 35)->whereIn('idOption2', $profesores)->get() as $vote) {
                $mg = Modulo_grupo::find($vote->idOption1);
                if (isset($totales[$mg->ModuloCiclo->Ciclo->ciclo]['votesSatis'])) {
                    $totales[$mg->ModuloCiclo->Ciclo->ciclo]['votesSatis'] += $vote->value;
                    $totales[$mg->ModuloCiclo->Ciclo->ciclo]['sumSatis'] += 1;
                } else {
                    $totales[$mg->ModuloCiclo->Ciclo->ciclo]['votesSatis'] = $vote->value;
                    $totales[$mg->ModuloCiclo->Ciclo->ciclo]['sumSatis'] = 1;
                }

            }
        } else {
            $programaciones = null;
        }
        $fecha = Hoy();
        return $this->hazPdf(
            'pdf.memoriaDepartament',
            $actividades,
            compact(
                'resultados',
                'observaciones',
                'trimestre',
                'proyectos',
                'programaciones',
                'fecha',
                'totales'
            )
        );
    }

    private function faltan()
    {
        // Obtenim els ID dels Modulo_ciclo rellevants
        $moduloCicloIds = hazArray(
            Modulo_ciclo::where('idDepartamento', AuthUser()->departamento)->get(),
            'id',
            'id'
        );

        // Recuperem els Modulo_grupo i filtrem en memòria
        $empty = Modulo_grupo::whereIn('idModuloCiclo', $moduloCicloIds)
            ->whereHas('Grupo', function ($query) {
                $query->whereHas('Alumnos'); // Assegurem que el grup té almenys un alumne
            })
            ->get()
            ->filter(function ($modulo) {
                return $modulo->seguimiento == false; // Utilitzem l'accessor calculat aquí
            })
            ->count();

        return $empty;
    }
}
