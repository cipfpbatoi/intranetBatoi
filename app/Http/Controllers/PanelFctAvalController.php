<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\AlumnoFct\AlumnoFctAvalService;
use Intranet\Application\AlumnoFct\AlumnoFctService;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Http\Controllers\Core\IntranetController;
use Intranet\Presentation\Crud\AlumnoFctAvalCrudSchema;


use DB;
use Illuminate\Support\Facades\Session;
use Intranet\UI\Botones\BotonConfirmacion;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Adjunto;
use Intranet\Entities\Documento;
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

    private ?AlumnoFctAvalService $alumnoFctAvalService = null;
    private ?GrupoService $grupoService = null;
    private ?AlumnoFctService $alumnoFctService = null;

    const ROLES_ROL_TUTOR = 'roles.rol.tutor';
    const ROLES_ROL_CAPAC = 'roles.rol.jefe_practicas';

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'AlumnoFct';
    protected $dropzoneModel = 'alumnofctaval';
    /**
     * @var array
     */
    protected $gridFields = AlumnoFctAvalCrudSchema::GRID_FIELDS;
    protected $formFields = AlumnoFctAvalCrudSchema::FORM_FIELDS;
    /**
     * @var bool
     */
    protected $profile = false;

    public function __construct(?GrupoService $grupoService = null, ?AlumnoFctService $alumnoFctService = null)
    {
        parent::__construct();
        $this->grupoService = $grupoService;
        $this->alumnoFctService = $alumnoFctService;
    }

    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
    }

    private function alumnoFcts(): AlumnoFctService
    {
        if ($this->alumnoFctService === null) {
            $this->alumnoFctService = app(AlumnoFctService::class);
        }

        return $this->alumnoFctService;
    }

    private function avals(): AlumnoFctAvalService
    {
        if ($this->alumnoFctAvalService === null) {
            $this->alumnoFctAvalService = app(AlumnoFctAvalService::class);
        }

        return $this->alumnoFctAvalService;
    }

    /**
     * @return \Illuminate\Support\Collection|mixed
     */
    public function search()
    {
        return $this->avals()->latestByProfesor(AuthUser()->dni);
        
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
        $this->avals()->apte((int) $id);

        return back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function noApte($id)
    {
        $grupo = $this->grupos()->firstByTutor(AuthUser()->dni);
        $this->avals()->noApte((int) $id, (bool) ($grupo?->proyecto));

        return back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function noAval($id)
    {
        $this->avals()->noAval((int) $id);

        return back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function noProyecto($id)
    {
        $this->avals()->noProyecto((int) $id);

        return back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function nullProyecto($id)
    {
        $this->avals()->nullProyecto((int) $id);


        return back();
    }
    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function nuevoProyecto($id)
    {
        $this->avals()->nuevoProyecto((int) $id);

        return back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function empresa($id)
    {
       $this->avals()->toggleInsercion((int) $id);
       return $this->redirect();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function demanarActa()
    {
        $grupos = $this->grupos()->qTutor(AuthUser()->dni);
        if ($grupos->isEmpty()) {
            Alert::message('No tens grups assignats', 'warning');
            return back();
        }

        $result = $this->avals()->requestActaForTutor(AuthUser()->dni, $grupos);
        $pendents = $result['pendents'];
        $demanades = $result['demanades'];
        $senseAlumnes = $result['senseAlumnes'];

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
     *
     */
    private function setActaB(): void
    {
        $grupo = $this->grupos()->firstByTutor(AuthUser()->dni);
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
        $grupo = $this->grupos()->firstByTutor(AuthUser()->dni);
        if ($grupo && $grupo->proyecto) {
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

        $fct = $this->alumnoFcts()->findOrFail((int) $id); //cerque el que toca
        $document['title'] = 10;
        $document['dni'] = $fct->Alumno->dni;
        $document['alumne'] = trim($fct->Alumno->shortName);


        $fcts = $this->alumnoFcts()->byAlumnoWithA56((string) $fct->idAlumno); //mira tots els de l'alumne

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
        $grupos = $this->grupos()->byCurso(2)->sortBy('idCiclo')->values();
        $ciclos = $this->avals()->estadistiques($grupos);
        return view('fct.estadisticas', compact('ciclos', 'grupos'));
    }

}
