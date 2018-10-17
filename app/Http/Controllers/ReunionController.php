<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Reunion;
use Intranet\Entities\Departamento;
use Intranet\Entities\Profesor;
use Intranet\Entities\Alumno;
use Intranet\Entities\Asistencia;
use Response;
use Exception;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Intranet\Botones\BotonBasico;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonImg;
use Intranet\Entities\TipoReunion;
use Intranet\Entities\OrdenReunion;
use Jenssegers\Date\Date;
use Styde\Html\Facades\Alert;
use Intranet\Entities\Documento;
use Intranet\Jobs\SendEmail;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Grupo;
use Intranet\Botones\Panel;
use Illuminate\Support\Facades\DB;

class ReunionController extends IntranetController
{

    use traitImprimir;

    protected $perfil = 'profesor';
    protected $model = 'Reunion';
    protected $gridFields = ['XGrupo', 'XTipo', 'Xnumero', 'descripcion', 'fecha', 'curso', 'id'];
    protected $modal = true;

    protected function search()
    {
        return Reunion::MisReuniones()->get();
    }

    public function store(Request $request)
    {
        $elemento = Reunion::find($this->realStore($request));
        $contador = 1;
        //dd(TipoReunion::ordenes($elemento->tipo));
        foreach (TipoReunion::ordenes($elemento->tipo) as $key => $texto) {
            if (strpos($texto, '->')) {
                $consulta = explode('->', $texto,3);
                $clase = $this->namespace . $consulta[0];
                $funcion = $consulta[1];
                $campo = $consulta[2];
                //dd("$clase::$funcion()");
                foreach ($clase::$funcion()->get() as $element){
                    $orden = new OrdenReunion;
                    $orden->idReunion = $elemento->id;
                    $orden->orden = $contador++;
                    $orden->descripcion = $element->$campo;
                    $orden->resumen = TipoReunion::resumen($elemento->tipo) != null ? TipoReunion::resumen($elemento->tipo).$orden->orden : '';
                    $orden->save();  
                }
            } else {
                $orden = new OrdenReunion;
                $orden->idReunion = $elemento->id;
                $orden->orden = $contador++;
                $orden->descripcion = $texto;
                $orden->resumen = TipoReunion::resumen($elemento->tipo) != null ? TipoReunion::resumen($elemento->tipo)[$key] : '';
                $orden->save();
            }
        }
        if ($elemento->fichero != '')
            return back();
        return redirect()->route('reunion.update', ['reunion' => $elemento->id]);
    }

    public function edit($id)
    {
        $elemento = Reunion::find($id);
        if ($elemento->fichero != '')
            return parent::edit($id);
        else {
            $ordenes = OrdenReunion::where('idReunion', '=', $id)->get();
            $Profesores = Profesor::select('dni', 'apellido1', 'apellido2', 'nombre')
                    ->OrderBy('apellido1')
                    ->OrderBy('apellido2')
                    ->get();
            foreach ($Profesores as $profesor) {
                $tProfesores[$profesor->dni] = $profesor->apellido1 . ' ' . $profesor->apellido2 . ',' . $profesor->nombre;
            }
            $sProfesores = $elemento->profesores()->orderBy('apellido1')->orderBy('apellido2')->get(['dni', 'apellido1', 'apellido2', 'nombre']);

            $elemento->setInputType('tipo', ['type' => 'hidden']);
            $elemento->setInputType('grupo', ['type' => 'hidden']);
            $default = $elemento->fillDefautOptions();
            $modelo = $this->model;
            return view('reunion.asistencia', compact('elemento', 'default', 'modelo', 'tProfesores', 'sProfesores', 'ordenes'));
        }
    }

    public function altaProfesor(Request $request, $reunion_id)
    {
        $reunion = Reunion::find($reunion_id);
        $reunion->profesores()->syncWithoutDetaching([$request->idProfesor => ['asiste' => 1]]);
        return redirect()->route('reunion.update', ['reunion' => $reunion_id]);
    }

    public function borrarProfesor($reunion_id, $profesor_id)
    {

        $reunion = Reunion::find($reunion_id);
        $reunion->profesores()->detach($profesor_id);
        return redirect()->route('reunion.update', ['reunion' => $reunion_id]);
    }

    public function altaOrden(Request $request, $reunion_id)
    {
        if ($request->orden == '') {
            $max = OrdenReunion::where('idReunion', '=', $reunion_id)->max('orden');
            $request->merge(['orden' => $max + 1]);
        }
        $orden = OrdenReunion::create($request->all());
        return redirect()->route('reunion.update', ['reunion' => $reunion_id]);
    }

    public function borrarOrden($reunion_id, $orden_id)
    {
        OrdenReunion::find($orden_id)->delete();
        return redirect()->route('reunion.update', ['reunion' => $reunion_id]);
    }

    public function notify($id)
    {
        $elemento = Reunion::findOrFail($id);
        $profesores = Asistencia::where('idReunion', '=', $id)->get();
        if (haVencido($elemento->fecha))
            $mensaje = "Ja està disponible l'acta de la reunió " . $elemento->descripcion . " del dia " . $elemento->fecha;
        else
            $mensaje = "Estas convocat a la reunió:  " . $elemento->descripcion . ' el dia ' . $elemento->fecha . ' a ' .
                    $elemento->Espacio->descripcion;
        $enlace = "/reunion/" . $id . "/pdf";
        foreach ($profesores as $profe)
            avisa($profe->idProfesor, $mensaje, $enlace);
        return back();
    }

    public function email($id)
    {
        $elemento = Reunion::findOrFail($id);
        //esborra fitxer si ja estaven
        if (file_exists(storage_path("tmp/Reunion_$id.pdf")))
            unlink(storage_path("tmp/Reunion_$id.pdf"));
        if (file_exists(storage_path("tmp/invita_$id.ics")))
            unlink(storage_path("tmp/invita_$id.ics"));
        //guarda fitxers i construix variable
        $this->construye_pdf($id)->save(storage_path("tmp/Reunion_$id.pdf"));
        if (!haVencido($elemento->fecha)) {
            file_put_contents(storage_path("tmp/invita_$id.ics"), $this->do_ics($elemento->id));
            $attach = ["tmp/Reunion_$id.pdf" => 'application/pdf', "tmp/invita_$id.ics" => 'text/calendar'];
        } else
            $attach = ["tmp/Reunion_$id.pdf" => 'application/pdf'];
        
        $asistentes = Asistencia::where('idReunion', '=', $id)->get();
        $remitente = ['email' => $elemento->Responsable->email, 'nombre' => $elemento->Responsable->FullName];
        foreach ($asistentes as $asistente) {
            if (!haVencido($elemento->fecha)) 
                dispatch(new SendEmail($asistente->Profesor->email, $remitente, 'email.convocatoria', $elemento, $attach));
            else
                dispatch(new SendEmail($asistente->Profesor->email, $remitente, 'email.reunion', $elemento, $attach));
        }
        Alert::info('Correus enviats');
        return back();
    }

    protected function iniBotones()
    {
        $this->panel->setBotonera(['create'], ['pdf']);
        $actual = AuthUser()->dni;
        $this->panel->setBoton('grid', new BotonImg('reunion.edit', ['img' => 'fa-pencil', 'where' => ['idProfesor', '==', $actual, 'archivada', '==', '0']]));
        $this->panel->setBoton('grid', new BotonImg('reunion.delete', ['where' => ['idProfesor', '==', $actual, 'archivada', '==', '0']]));
        $this->panel->setBoton('grid', new BotonImg('reunion.notification', ['where' => ['idProfesor', '==', $actual, 'fichero', '==', '', 'archivada', '==', '0']]));
        $this->panel->setBoton('grid', new BotonImg('reunion.email', ['where' => ['idProfesor', '==', $actual, 'fichero', '==', '']]));
        $this->panel->setBoton('grid', new BotonImg('reunion.ics', ['img' => 'fa-calendar', 'where' => ['fecha', 'posterior', Date::yesterday()]]));
        $this->panel->setBoton('grid', new BotonImg('reunion.saveFile', ['where' => ['idProfesor', '==', $actual, 'archivada', '==', '0', 'fecha', 'anterior', Date::yesterday()]]));
    }

    public function pdf($id)
    {
        $elemento = Reunion::findOrFail($id);
        if ($elemento->fichero != '')
            if (file_exists(storage_path('/app/' . $elemento->fichero)))
                return response()->file(storage_path('/app/' . $elemento->fichero));
            else {
                Alert::message('No trobe fitxer', 'danger');
                return back();
            } else {
            if ($elemento->archivada)
                $this->saveFile($id);
            return $this->construye_pdf($id)->stream();
        }
    }

    public function saveFile($id)
    {
        $elemento = $this->class::find($id);
        if ($elemento->fichero != '')
            $nomComplet = $elemento->fichero;
        else {
            $nom = 'Acta_' . $elemento->id . '.pdf';
            $directorio = 'gestor/' . Curso() . '/' . $this->model;
            $nomComplet = $directorio . '/' . $nom;
            if (!file_exists(storage_path('/app/' . $nomComplet)))
                $this->construye_pdf($id)->save(storage_path('/app/' . $nomComplet));
        }
        $elemento->archivada = 1;
        $elemento->fichero = $nomComplet;
        DB::transaction(function () use ($elemento) {
            Documento::crea($elemento, ['propietario' => $elemento->Creador->FullName,
                'tipoDocumento' => 'Acta',
                'descripcion' => $elemento->descripcion,
                'fichero' => $elemento->fichero,
                'supervisor' => $elemento->Creador->FullName,
                'grupo' => str_replace(' ', '_', $elemento->Xgrupo),
                'tags' => TipoReunion::literal($elemento->tipo),
                'created_at' => new Date($elemento->fecha),
                'rol' => config('roles.rol.profesor')]);
            $elemento->save();
        });
        return back();
    }

    public function listado($dia = null)
    {
        foreach (Grupo::all() as $grupo)
            foreach ([2, 5, 6, 7, 9] as $tipo) {
                $reuniones[$grupo->nombre][$tipo] = Reunion::Convocante($grupo->tutor)->Tipo($tipo)->Archivada()->get();
            }
        return view('reunion.control', compact('reuniones'));
    }

    public function avisaFaltaActa(Request $request)
    {
        $cont = 0;
        if ($request->quien) $grupos = Grupo::where('curso',$request->quien)->get();
        else $grupos = Grupo::all();
        
        foreach ($grupos as $grupo) {
            if (!Reunion::Convocante($grupo->tutor)->Tipo($request->tipo)->Numero($request->numero)->Archivada()->count()) {
                $texto = 'Et falta per fer i/o arxivar la reunio ' . TipoReunion::literal($request->tipo) . ' ';
                $texto .= $request->numero > 0 ? config('auxiliares.numeracion')[$request->numero] : '';
                avisa($grupo->tutor, $texto);
                $cont++;
            }
        }
        Alert::info($cont . ' Avisos enviats');
        return back();
    }

    private function construye_pdf($id)
    {
        $elemento = Reunion::findOrFail($id);
        $hoy = new Date($elemento->fecha);
        $elemento->dia = FechaString($hoy);
        $elemento->hora = $hoy->format('H:i');
        $hoy = new Date($elemento->updated_at);
        $elemento->hoy = haVencido($elemento->fecha) ? $elemento->dia : FechaString($hoy);

        $ordenes = OrdenReunion::where('idReunion', '=', $id)->get();
        $informe = haVencido($elemento->fecha) ? 'pdf.reunion.' . TipoReunion::acta($elemento->tipo) : 'pdf.reunion.' . TipoReunion::convocatoria($elemento->tipo);
        $orientacion = 'portrait';
        $pdf = $this->hazPdf($informe, $ordenes, $elemento, $orientacion, 'a4');
        return $pdf;
    }

}
