<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\IntranetController;


use DB;
use Illuminate\Support\Facades\Session;
use Intranet\UI\Botones\BotonConfirmacion;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Adjunto;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\AlumnoFctAval;
use Intranet\Entities\Documento;
use Intranet\Entities\Grupo;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Exceptions\IntranetException;
use Intranet\Http\Traits\Core\DropZone;
use Intranet\Services\Document\FDFPrepareService;
use Intranet\Services\School\SecretariaService;
use Styde\Html\Facades\Alert;


/**
 * Class PanelFctAvalController
 * @package Intranet\Http\Controllers
 */
class PanelFctAvalController extends IntranetController
{
    use DropZone;

    const ROLES_ROL_TUTOR = 'roles.rol.tutor';
    const ROLES_ROL_CAPAC = 'roles.rol.jefe_practicas';

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'AlumnoFctAval';
    /**
     * @var array
     */
    protected $gridFields = ['Nombre', 'Qualificacio', 'Projecte', 'hasta'];
    /**
     * @var bool
     */
    protected $profile = false;

    /**
     * @return \Illuminate\Support\Collection|mixed
     */
    public function search()
    {
        $nombres = AlumnoFctAval::select('idAlumno')->distinct()->misFcts()->esAval()->get()->toArray();
        $todas = collect();
        foreach ($nombres as $nombre) {
            $todas->push(AlumnoFctAval::misFcts()->esAval()->where('idAlumno', $nombre['idAlumno'])
                ->orderByDesc('idSao')
                ->first());
        }
        return $todas;
        
    }


    /**
     *
     */
    protected function iniBotones()
    {
        Session::put('redirect', 'PanelFctAvalController@index');
        $this->panel->setPestana('Resum', false, 'profile.resumenfct');
        $this->setActaB();
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'fct.apte',
                [
                    'img' => 'fa-hand-o-up',
                    'where' => [
                        'calificacion', '!=', '1',
                        'actas', '==', 0,
                        'asociacion', '<>', 2
                    ]
                ]
            ));
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'fct.noApte',
                [
                    'img' => 'fa-hand-o-down',
                    'where' => [
                        'calProyecto', '<', '5',
                        'calificacion', '!=', '0',
                        'actas', '==', 0, 'asociacion', '<>', 2
                    ]
                ]
            ));
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'fct.noAval',
                [
                    'img' => 'fa-recycle',
                    'where' => [
                        'calProyecto', '<', '5',
                        'calificacion', '!=', null,
                        'actas', '==', 0, 'asociacion', '<', 3
                    ]
                ]
            ));
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'fct.noAval',
                [
                    'img' => 'fa-recycle',
                    'where' => [
                        'calProyecto', '<', '1',
                        'calificacion', '==', 0,
                        'actas', '>', 0, 'asociacion', '<>', 2
                    ]
                ]
            ));
        $this->setProjectB();
        $this->panel->setBoton(
            'grid',
            new BotonImg('fct.insercio', ['img' => 'fa-square-o', 'roles' => config(self::ROLES_ROL_TUTOR),
            'where' => ['insercion', '==', '0','asociacion','<',3,'calificacion', '==', '1']]
            ));
        $this->panel->setBoton(
            'grid',
            new BotonImg('fct.insercio', ['img' => 'fa-check-square-o', 'roles' => config(self::ROLES_ROL_TUTOR),
            'where' => ['insercion', '==', '1','asociacion','<',3,'calificacion', '==', '1']]
            ));
    }


    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function apte($id)
    {
        $fct = AlumnoFctAval::find($id);
        $fct->calificacion = 1;
        $fct->save();

        return back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function noApte($id)
    {
        $grupo = Grupo::QTutor()->first();

        $fct = AlumnoFctAval::find($id);
        $fct->calificacion = 0;
        $fct->calProyecto = $grupo->proyecto?0:null;
        $fct->save();

        return back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function noAval($id)
    {
        $fct = AlumnoFctAval::find($id);
        $fct->calificacion = null;
        $fct->calProyecto = null;
        $fct->actas = 0;
        $fct->save();

        return back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function noProyecto($id)
    {
        $fct = AlumnoFctAval::find($id);
        $fct->calProyecto = 0;
        $fct->save();

        return back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function nullProyecto($id)
    {
        DB::transaction(function () use ($id) {
            $fct = AlumnoFctAval::find($id);
            $fct->calProyecto = null;
            $fct->save();

            $doc = Documento::where('tipoDocumento', 'Proyecto')
                ->where('curso', Curso())
                ->whereNull('idDocumento')
                ->where('propietario', $fct->fullName)
                ->first();
            if ($doc) {
                $doc->deleteDoc();
            }
        });


        return back();
    }
    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function nuevoProyecto($id)
    {
        $fct = AlumnoFctAval::find($id);
        $fct->calProyecto = null;
        $fct->actas = 1;
        $fct->save();

        return back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function empresa($id)
    {
       $fct = AlumnoFctAval::find($id);
       $fct->insercion = $fct->insercion?0:1;
       $fct->save();
       return $this->redirect();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function demanarActa()
    {
        $grupos = Grupo::QTutor()->get();
        if ($grupos->isEmpty()) {
            Alert::message('No tens grups assignats', 'warning');
            return back();
        }

        $pendents = [];
        $demanades = [];
        $senseAlumnes = [];

        foreach ($grupos as $grupo) {
            if ($grupo->acta_pendiente) {
                $pendents[] = $grupo->nombre;
                continue;
            }

            if ($this->lookForStudents($grupo->proyecto, $grupo)) {
                $grupo->acta_pendiente = 1;
                $grupo->save();
                avisa(
                    config('avisos.jefeEstudios2'),
                    "Acta pendent grup $grupo->nombre",
                    config('contacto.host.web')."/direccion/$grupo->codigo/acta"
                );
                $demanades[] = $grupo->nombre;
            } else {
                $senseAlumnes[] = $grupo->nombre;
            }
        }

        if ($demanades) {
            Alert::message('Acta demanada: '.implode(', ', $demanades), 'info');
        }
        if ($pendents) {
            Alert::message("L'acta pendent esta en procés: ".implode(', ', $pendents), 'info');
        }
        if ($senseAlumnes && !$demanades) {
            Alert::message('No tens nous alumnes per ser avaluats', 'warning');
        }

        return back();
    }

    /**
     * @param $projectNeeded
     * @return bool
     */
    private function lookForStudents($projectNeeded, $grupo = null)
    {

        $found = false;
        $query = AlumnoFctAval::Avaluables()->NoAval();
        if ($grupo) {
            $query->Grupo($grupo);
        }
        foreach ($query->get() as $fct) {
            if ($projectNeeded) {
                if (isset($fct->calProyecto)) {
                    $fct->actas = 3;
                    $fct->save();
                    $found = true;
                }
            } elseif (isset($fct->calificacion)) {
                    $fct->actas = 3;
                    $fct->save();
                    $found = true;
            }
        }
        return $found;
    }



    /**
     *
     */
    private function setActaB(): void
    {
        $grupo = Grupo::QTutor()->first();
        if ($grupo && !$grupo->acta_pendiente  ) {
            if ($grupo->curso == 2) {
                $this->panel->setBoton(
                    'index',
                    new BotonConfirmacion("fct.acta", ['class' => 'btn-info', 'roles' => config(self::ROLES_ROL_TUTOR)]
                    ));
            }
        } else {
            Alert::message("L'acta pendent esta en procés", 'info');
        }
    }

    /**
     *
     */
    private function setProjectB(): void
    {
        if (Grupo::QTutor()->first() && Grupo::QTutor()->first()->proyecto) {
            // Aprovats
            $this->panel->setBoton(
                'grid',
                new BotonImg(
                    'fct.proyecto',
                    [
                        'img' => 'fa-file', 'roles' => config(self::ROLES_ROL_TUTOR),
                        'where' => [
                            'calProyecto', '<', '1',
                            'actas', '<', 2,
                            'calificacion', '==', '1'
                        ]
                    ]
                )
            );
            $this->panel->setBoton(
                'grid',
                new BotonImg(
                    'fct.noProyecto',
                    [
                        'img' => 'fa-toggle-off',
                        'roles' => config(self::ROLES_ROL_TUTOR),
                        'where' => [
                            'calProyecto', '<', '0',
                            'actas', '<', 2,
                            'calificacion', '==', '1'
                        ]
                    ]
                )
            );
            $this->panel->setBoton(
                'grid',
                new BotonImg(
                    'fct.nullProyecto',
                    [

                       'img' => 'fa-minus-circle', 'roles' => config(self::ROLES_ROL_TUTOR),
                        'where' => [
                            'calProyecto', '>=', '0',
                            'actas', '<', 2,
                            'calificacion', '==', '1',
                            'asociacion', '<>', '2'
                        ]
                    ]
                )
            );
            // Convalidats
            $this->panel->setBoton(
                'grid',
                new BotonImg(
                    'fct.proyecto',
                    [
                        'img' => 'fa-file',
                        'roles' => config(self::ROLES_ROL_TUTOR),
                        'where' => [
                            'calProyecto', '<', '1',
                            'actas', '<', 2,
                            'asociacion', '==', '2'
                        ]
                    ]
                )
            );
            $this->panel->setBoton(
                'grid',
                new BotonImg(
                    'fct.noProyecto',
                    [
                        'img' => 'fa-toggle-off',
                        'roles' => config(self::ROLES_ROL_TUTOR),
                        'where' => [
                            'calProyecto', '<', '0',
                            'actas', '<', 2,
                            'asociacion', '==', '2'
                        ]
                    ]
                )
            );
            $this->panel->setBoton(
                'grid',
                new BotonImg(
                    'fct.nullProyecto',
                    [
                        'img' => 'fa-minus-circle',
                        'roles' => config(self::ROLES_ROL_TUTOR),
                        'where' => [
                            'calProyecto', '>=', '0',
                            'actas', '<', 2,
                            'asociacion', '==', '2'
                        ]
                    ]
                )
            );

            $this->panel->setBoton(
                'grid',
                new BotonImg(
                    'fct.nuevoProyecto',
                    [
                        'img' => 'fa-toggle-on',
                        'roles' => config(self::ROLES_ROL_TUTOR),
                        'where' => [
                            'calProyecto', '<', '5',
                            'calProyecto', '>=', 0,
                            'actas', '==', 2
                        ]
                    ]
                ));
            $this->panel->setBoton(
                'grid',
                new BotonImg(
                    'fct.modificaNota',
                    [
                        'img' => 'fa-edit',
                        'roles' => config(self::ROLES_ROL_TUTOR),
                        'where' => [
                            'calProyecto', '>', 0,
                            'actas', '<', 2
                        ]
                    ]
                )
            );
        }
    }

    public function linkQuality($id)
    {
        $registre = app(ProfesorService::class)->findOrFail((string) $id);
        $quien = $registre->fullName;
        $modelo = strtolower('Profesor');
        $ara = new \DateTime();
        $inici = new \DateTime(date('Y') . '-06-15');
        $fi = new \DateTime(date('Y') . '-08-31');
        $botones = [
            'volver' => ['link' => back()->getTargetUrl()],
        ];
        if ($ara >= $inici && $ara <= $fi && userIsAllow(config(self::ROLES_ROL_CAPAC)))  {
            $botones['final'] = [
                    'link' =>"/fct/$id/upload",
                    'message' => "Este procediment l'has de fer quan tingues tota
                     la documentació de totes les FCT completes.
                      Una vegada fet no es pot tornar arrere."
                ];
        }
        return view('dropzone.index', compact('modelo', 'id', 'quien', 'botones'));

    }


    public function send($id)
    {
        $document = array();

        $fct = AlumnoFct::findOrFail($id); //cerque el que toca
        $document['title'] = 10;
        $document['dni'] = $fct->Alumno->dni;
        $document['alumne'] = trim($fct->Alumno->shortName);


        $fcts = AlumnoFct::where('idAlumno', $fct->idAlumno)->where('a56', '>', 0)->get(); //mira tots els de l'alumne

        foreach ($fcts as $key => $fct) {  // cerque els adjunts
            $adjuntos[$key] = Adjunto::where('route', 'alumnofctaval/'.$fct->id)
                ->where('extension', 'pdf')
                ->orderBy('created_at', 'desc')
                ->get()
                ->first();
        }

        if (count($adjuntos) == 1) { // si soles hi ha un
            $document['route'] =
                'app/public/adjuntos/'.
                $adjuntos[0]->route.'/'.
                $adjuntos[0]->title.'.'.$adjuntos[0]->extension;
            $document['name'] = $adjuntos[0]->title.'.'.$adjuntos[0]->extension;
            $document['size'] = $adjuntos[0]->size;
        } else {
            $size = 0;
            foreach ($adjuntos as $key => $adjunto) {
                $files[$key] =
                    storage_path(
                        'app/public/adjuntos/'.
                        $adjuntos[$key]->route.'/'.
                        $adjuntos[$key]->title.'.'.$adjuntos[$key]->extension
                    );
                $size += $adjuntos[$key]->size;
            }
            $document['route'] = FDFPrepareService::joinPDFs($files, $document['dni']);
            $document['name'] = $document['dni'].'.pdf';
            $document['size'] = $size;
        }


        try {
            $sService = new SecretariaService();
            $sService->uploadFile($document);
            foreach ($fcts as $fct) {
                $fct->a56 = 2;
                $fct->save();
            }
            Alert::success('Document enviat correctament');
        } catch (IntranetException $e) {
            Alert::danger($e->getMessage());
        }

        return back();
    }

    public function estadistiques()
    {
        $grupos = Grupo::where('curso',2)->orderBy('idCiclo')->get();
        $ciclos = [];
        foreach ($grupos as $grupo) {
            $ciclo = $grupo->idCiclo;
            $ciclos[$ciclo]['matriculados'] =
                isset($ciclos[$ciclo]['matriculados']) ?
                $ciclos[$ciclo]['matriculados'] + $grupo->matriculados : $grupo->matriculados;
            $ciclos[$ciclo]['resfct'] = isset($ciclos[$ciclo]['resfct']) ?
                $ciclos[$ciclo]['resfct'] + $grupo->AprobFct : $grupo->AprobFct;
            $ciclos[$ciclo]['exentos'] = isset($ciclos[$ciclo]['exentos']) ?
                $ciclos[$ciclo]['exentos'] + $grupo->exentos : $grupo->exentos;
            $ciclos[$ciclo]['respro'] = isset($ciclos[$ciclo]['respro']) ?
                $ciclos[$ciclo]['respro'] + $grupo->AprobPro : $grupo->AprobPro;
            $ciclos[$ciclo]['avalpro'] = isset($ciclos[$ciclo]['avalpro']) ?
                $ciclos[$ciclo]['avalpro'] + $grupo->AvalPro : $grupo->AvalPro;
            $ciclos[$ciclo]['resempresa'] = isset($ciclos[$ciclo]['resempresa']) ?
                $ciclos[$ciclo]['resempresa'] + $grupo->colocados : $grupo->colocados;
            $ciclos[$ciclo]['avalfct'] = isset($ciclos[$ciclo]['avalfct']) ?
                $ciclos[$ciclo]['avalfct'] + $grupo->avalFct : $grupo->avalFct;
        }
        return view('fct.estadisticas', compact('ciclos', 'grupos'));
    }

}
