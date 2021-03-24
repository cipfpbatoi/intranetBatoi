<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonBasico;
use Intranet\Botones\BotonImg;
use Intranet\Entities\Horario;
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


class PanelListadoEntregasController extends BaseController
{
    use traitImprimir;
    
    protected $model = 'Modulo_grupo';
    protected $gridFields = ['literal','profesor','seguimiento'];
    protected $parametresVista = ['modal' => ['infDpto']];

    public function search()
    {
        $modulos = hazArray(Modulo_ciclo::whereIn('idModulo',hazArray(Horario::distinct()->get(),'modulo'))
            ->where('idDepartamento',AuthUser()->departamento)->get(),'id');
        return Modulo_grupo::with('Grupo')
            ->with('resultados')
            ->with('ModuloCiclo')
            ->whereIn('idModuloCiclo',$modulos)->get();
    }

    public function iniBotones()
    {
        if (!$this->faltan()){
            if ($reunion = $this->existeInforme())
            {
                $this->panel->setBoton('index',new BotonBasico('Infdepartamento.edit',['roles' => config('roles.rol.jefe_dpto'),'id'=>'generar']));
            }
            else
            {
                $this->panel->setBoton('index',new BotonBasico('Infdepartamento.create',['roles' => config('roles.rol.jefe_dpto'),'id'=>'generar']));
            }
        }
        else {
            $this->panel->setBoton('index',new BotonBasico('Infdepartamento.avisa',['roles' => config('roles.rol.jefe_dpto')]));
        }
        if ($reunion = $this->existeInforme())
        {
            $this->panel->setBoton('index',new BotonBasico('Infdepartamento.pdf.'.$reunion->id,['roles' => config('roles.rol.jefe_dpto')]));
        }
         $this->panel->setBoton('grid',new BotonImg('Infdepartamento.aviso',['img' => 'fa-bell','where' => ['seguimiento','==',0],'roles' => config('roles.rol.jefe_dpto')]));
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
                        'tags' => TipoReunion::literal($reunion->tipo) . ',' . config('auxiliares.numeracion')[$reunion->numero],
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
        if (file_exists(storage_path('/app/' . $nomComplet)))
        {
            unlink(storage_path('/app/' . $nomComplet));
        }
        $pdf->save(storage_path('/app/' . $nomComplet));

        return back();
    }
    
    public function avisaTodos(){
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
        foreach ($modulo->profesores() as $profesor){
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
        return Reunion::Select('id', 'numero')->Tipo(10)->where('numero',evaluacion() +19)->Convocante(AuthUser()->dni)->first();
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
        } else {
            $programaciones = null;
        }
        return $this->hazPdf('pdf.infDep', $actividades, compact('resultados', 'observaciones', 'trimestre', 'proyectos', 'programaciones'));
    }
    private function faltan()
    {
        $empty = 0;
        foreach (Modulo_grupo::whereIn('idModuloCiclo', hazArray(Modulo_ciclo::where('idDepartamento', AuthUser()->departamento)->get(), 'id', 'id'))->get() as $modulo)
        {
            if ($modulo->seguimiento == 0) {
                $empty++;
            }
        }
        return $empty;
    }
    
    
    
    
}