<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Curso;
use Intranet\Entities\Curso_alumno;
use Intranet\Botones\BotonImg;
use Intranet\Entities\Modulo;
use Intranet\Entities\Modulo_ciclo;
use Intranet\Entities\Departamento;
use Intranet\Entities\Grupo;
use Intranet\Entities\Resultado;
use Jenssegers\Date\Date;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Horario;
use Intranet\Entities\Profesor;
use Intranet\Entities\Reunion;
use Intranet\Entities\OrdenReunion;
use Intranet\Entities\TipoReunion;
use Intranet\Entities\Actividad;
use Intranet\Entities\Documento;
use Intranet\Botones\Panel;
use Styde\Html;
use Illuminate\Support\Facades\DB;
use Intranet\Entities\Programacion;
use Intranet\Entities\Modulo_grupo;

class ResultadoController extends IntranetController
{

    use traitImprimir;

    protected $perfil = 'profesor';
    protected $model = 'Resultado';
    protected $gridFields = ['Modulo', 'XEvaluacion', 'XProfesor'];
    protected $modal = true;

    protected function iniBotones()
    {
        $this->panel->setBotonera(['create'], ['delete', 'edit']);
    }

    public function search()
    {
        $misModulos = hazArray(Modulo_Grupo::MisModulos(), 'id', 'id');
        return Resultado::whereIn('idModuloGrupo', $misModulos)
                        ->get();
    }

    /*
     * store (Request) return redirect
     * guarda els valors del formulari
     */

    public function store(Request $request)
    {
        $modulogrupo = Modulo_grupo::find($request->idModuloGrupo);
        if ($modulogrupo) {
            $this->realStore($request);
            if ($request->evaluacion == 3) {
                $programacion = Programacion::where('idModuloCiclo', $modulogrupo->idModuloCiclo)->where('curso', Curso())->first()->id;
                return redirect("/programacion/$programacion/seguimiento");
            } else
                return $this->redirect();
        }
        else {
            Alert::danger("Eixe mòdul no es dona en eixe grup");
            return $this->redirect();
        }
    }

    public function listado()
    {
        $grupo = Grupo::select('codigo', 'nombre')->QTutor()->first();
        if ($grupo) {
            $resultados = Resultado::QGrupo($grupo->codigo)->orderBy('idModuloGrupo')
                            ->orderBy('evaluacion')->get();
            $datosInforme = $grupo->nombre;
            return $this->hazPdf('pdf.resultado', $resultados, $datosInforme)->stream();
        } else {
            Alert::danger(trans("messages.generic.nogroup"));
            return back();
        }
    }

//    public function listadoEntregas($evaluacion = 1)
//    {
//        $faltan = $this->FaltaEntrega($evaluacion);
//        $informes = $this->informesExistentes();
//        return view('resultado.control', compact('faltan', 'evaluacion','informes'));
//    }

    public function listadoEntregas()
    {
        $trimestre = evaluacion() - 1;
        $panel = new Panel('Resultado', null, null, false);
        $panel->setPestana($trimestre . "_trimestre", 1, '.resultado.partials.trimestre', ['trimestre', $trimestre]);

//        for ($i=$trimestre-1; $i < $trimestre;$i++){
//            $panel->setPestana($i."_trimestre", ($i == $trimestre-1), '.resultado.partials.trimestre', ['trimestre', $i]);
//            $trimestres[] = $i;
//        }
        $faltan = $this->FaltaEntrega($trimestre);
        $informes = $this->informesExistentes();
        $panel->setElementos($faltan);
        $panel->setTitulo([]);
        return view('resultado.control', compact('panel', 'informes'));
    }

    public function avisaFaltaEntrega($evaluacion)
    {
        $faltan = $this->FaltaEntrega([$evaluacion]);
        $cont = 0;
        foreach ($faltan as $falta) {
            $texto = "$falta->nombre, et falta per omplir el seguiment de l'avaluacio '" . config('constants.nombreEval')[config("curso.trimestres.$evaluacion")[$this->curso($falta->grupo)]] .
                    "' del mòdul '$falta->modulo' del Grup '$falta->grupo'";
            avisa($falta->idProfesor, $texto);
            $cont++;
        }
        Alert::info($cont . ' Avisos enviats');
        return back();
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

            $documento = Documento::crea($reunion, ['propietario' => AuthUser()->FullName,
                        'tipoDocumento' => 'Acta',
                        'descripcion' => $reunion->descripcion,
                        'fichero' => $reunion->fichero,
                        'supervisor' => $reunion->idProfesor,
                        'grupo' => str_replace(' ', '_', $reunion->Xgrupo),
                        'tags' => TipoReunion::literal($reunion->tipo) . ',' . config('constants.numeracion')[$reunion->numero],
                        'created_at' => new Date($reunion->fecha),
                        'rol' => config('constants.rol.profesor')]);
        });
        return back();
    }

    public function modificaInformeTrimestral(Request $request)
    {
        $pdf = $this->hazPdfInforme($request->observaciones, $request->trimestre);
        $oR = OrdenReunion::where('idReunion', $request->reunion)
                ->where('orden', 1)
                ->first();
        $oR->resumen = $request->observaciones;
        $oR->save();
        if (isset($request->proyectos)) {
            $oR = OrdenReunion::where('idReunion', $request->reunion)
                    ->where('orden', 2)
                    ->first();
            $oR->resumen = $request->proyectos;
            $oR->save();
        }

        $nom = 'Informe_' . $request->reunion . '.pdf';
        $directorio = 'gestor/' . Curso() . '/Reunion';
        $nomComplet = $directorio . '/' . $nom;
        if (file_exists(storage_path('/app/' . $nomComplet)))
            unlink(storage_path('/app/' . $nomComplet));
        $pdf->save(storage_path('/app/' . $nomComplet));

        return back();
    }

    protected function verInformeTrimestral($id)
    {
        return response()->file(storage_path('app/' . Reunion::findOrFail($id)->fichero));
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
        $primero = Resultado::Departamento($dep)
                ->TrimestreCurso($trimestre, 1)
                ->get();
        $segundo = Resultado::Departamento($dep)
                ->TrimestreCurso($trimestre, 2)
                ->get();
        $resultados = $primero
                ->concat($segundo)
                ->sortBy('Modulo');
        if ($trimestre == 3) {
            $programaciones = Programacion::Departamento(AuthUser()->departamento)
                    ->whereNotNull('propuestas')
                    ->get();
        } else
            $programaciones = null;
        return $this->hazPdf('pdf.infDep', $actividades, compact('resultados', 'observaciones', 'trimestre', 'proyectos', 'programaciones'));
    }

    private function FaltaEntrega($trimestre)
    {
        $modulos = Modulo_grupo::whereIn('idModuloCiclo', hazArray(Modulo_ciclo::where('idDepartamento', AuthUser()->departamento)->get(), 'id', 'id'))->get();
        $faltan = collect();
        $evaluaciones = config("curso.trimestres.$trimestre");
        foreach ($evaluaciones as $curso => $evaluacion)
            if ($evaluacion)
                foreach ($modulos as $moduloG)
                    if (Resultado::where('idModuloGrupo', $moduloG->id)->where('evaluacion', $evaluacion)->first() == null)
                        if ($this->curso($moduloG->idGrupo) == $curso) {
                            $elemento = new Faltan();
                            $elemento->trimestre = $trimestre;
                            $elemento->modulo = $moduloG->ModuloCiclo->Modulo->literal;
                            $elemento->profesores = $moduloG->Profesores();
                            $elemento->grupo = $moduloG->Grupo->nombre;
                            $elemento->idResultado = null;
                            $faltan->push($elemento);
                        }
        return $faltan;
    }

    private function curso($codigo)
    {
        return substr($codigo, 0, 1);
    }

    private function informesExistentes()
    {
        $informes = [];
        foreach (Reunion::Select('id', 'numero')->Tipo(10)->Convocante(AuthUser()->dni)->get() as $reunion) {
            $informes[$reunion->numero - 20] = $reunion->id;
        }
        return $informes;
    }

}

class Faltan
{

    public $trimestre, $modulo, $profesores, $grupo;

}
