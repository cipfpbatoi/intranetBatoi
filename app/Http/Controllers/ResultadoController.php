<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Curso;
use Intranet\Entities\Curso_alumno;
use Intranet\Botones\BotonImg;
use Intranet\Entities\Modulo;
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

class ResultadoController extends IntranetController
{

    use traitImprimir;

    protected $perfil = 'profesor';
    protected $model = 'Resultado';
    protected $gridFields = ['XGrupo', 'XModulo','XEvaluacion', 'Xprofesor' ];
    protected $modal = true;

    protected function iniBotones()
    {
        $this->panel->setBotonera(['create'], ['delete', 'edit']);
    }

    
    public function search()
    {
        $misGrupos = Grupo::select('codigo')->MisGrupos()->get()->toarray();
        $misModulos = Modulo::select('codigo')->MisModulos()->get()->toarray();
        return Resultado::whereIn('idGrupo', $misGrupos)
                        ->whereIn('idModulo', $misModulos)
                        ->get();
    }
    /* 
     * store (Request) return redirect
     * guarda els valors del formulari
     */
    public function store(Request $request)
    {
        $this->realStore($request);
        if (($request->evaluacion == 3) &&
             ($programacion = Programacion::where('idModulo',$request->idModulo)->first()->id))
             return redirect ("/programacion/$programacion/seguimiento");         
        else return $this->redirect();
    }

    public function listado()
    {
        $grupo = Grupo::select('codigo', 'nombre')->QTutor()->first();
        if ($grupo) {
            $resultados = Resultado::QGrupo($grupo->codigo)->orderBy('idModulo')
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
        $trimestre = evaluacion();
        $trimestres = [];
        $panel = new Panel('Resultado', null, null, false);
        for ($i=1; $i< $trimestre;$i++){
            $panel->setPestana($i."_trimestre", ($i == $trimestre-1), '.resultado.partials.trimestre', ['trimestre', $i]);
            $trimestres[] = $i;
        }
        $faltan = $this->FaltaEntrega($trimestres);
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
            $texto = "$falta->nombre, et falta per omplir el seguiment de l'avaluacio '" . config('constants.nombreEval')[config("constants.trimestres.$evaluacion")[$this->curso($falta->grupo)]] .
                    "' del mòdul '$falta->modulo' del Grup '$falta->grupo'";
            avisa($falta->idProfesor, $texto);
            $cont++;
        }
        Alert::info($cont . ' Avisos enviats');
        return back();
    }

    public function hazInformeTrimestral(Request $request)
    {
        $pdf = $this->hazPdfInforme($request->observaciones, $request->trimestre,$request->proyectos);
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
            
            if (isset($request->proyectos)){
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
        if (isset($request->proyectos)){
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

    private function hazPdfInforme($observaciones, $trimestre,$proyectos=null)
    {
        $dep = AuthUser()->departamento;
        $fechas = config("constants.evaluaciones.$trimestre");
        $actividades = Actividad::QueDepartamento($dep)
                ->where('desde', '>=', $fechas[0])
                ->where('hasta', '<=', $fechas[1])
                ->where('estado', 3)
                ->orderBy('desde')
                ->get();
        $primero = Resultado::QueDepartamento($dep)
                        ->TrimestreCurso($trimestre, 1)->get();
        $segundo = Resultado::QueDepartamento($dep)
                        ->TrimestreCurso($trimestre, 2)->get();
        $resultados = $primero
                ->concat($segundo)
                ->sortBy('idGrupo');
        if ($trimestre == 3){
            $programaciones = Programacion::Departamento(AuthUser()->departamento)
                    ->whereNotNull('propuestas')
                    ->get();
        } else $programaciones = null;
        return $this->hazPdf('pdf.infDep', $actividades, compact('resultados', 'observaciones', 'trimestre','proyectos','programaciones'));
    }

    private function FaltaEntrega($trimestres)
    {
        $faltan = collect();
        $modulos = Horario::ModulosActivos(esRol(AuthUser()->rol, config('constants.rol.jefe_dpto')) ? AuthUser()->departamento : null);
        foreach ($trimestres as $trimestre){
            $evaluaciones = config("constants.trimestres.$trimestre");
            $this->llenaFaltan($faltan, $modulos, $trimestre, $evaluaciones);
        }    

        return $faltan;
    }

    private function llenaFaltan(&$faltan, $modulos, $trimestre, $evaluaciones)
    {
        foreach ($evaluaciones as $curso => $evaluacion)
            if ($evaluacion)
                foreach ($modulos as $modulo)
                    if (Resultado::where('idModulo', $modulo->modulo)->where('idGrupo', $modulo->idGrupo)->where('evaluacion', $evaluacion)->first() == null)
                        if ($this->curso($modulo->idGrupo) == $curso) {
                            $elemento = new Faltan();
                            $elemento->trimestre = $trimestre;
                            $elemento->modulo = Modulo::find($modulo->modulo)->literal;
                            $elemento->idProfesor = $modulo->idProfesor;
                            $elemento->nombre = Profesor::find($modulo->idProfesor)->shortName;
                            $elemento->grupo = Grupo::find($modulo->idGrupo)->nombre;
                            $elemento->idResultado = null;
                            $faltan->push($elemento);
                        }
    }

    private function curso($codigo)
    {
        return substr($codigo, 0, 1);
    }

}

class Faltan
{

    public $trimestre, $modulo, $idProfesor, $nombre, $grupo;

}
