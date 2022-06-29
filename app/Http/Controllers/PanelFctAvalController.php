<?php

namespace Intranet\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Intranet\Botones\BotonConfirmacion;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\Adjunto;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Grupo;
use Intranet\Entities\AlumnoFctAval;
use DB;
use Intranet\Jobs\UploadFiles;
use Intranet\Mail\Comunicado;
use Intranet\Services\SecretariaService;
use Styde\Html\Facades\Alert;
use Intranet\Entities\Documento;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Profesor;
use Intranet\Entities\Fct;

/**
 * Class PanelFctAvalController
 * @package Intranet\Http\Controllers
 */
class PanelFctAvalController extends IntranetController
{
    use traitDropZone;

    const ROLES_ROL_TUTOR = 'roles.rol.tutor';

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
    protected $gridFields = ['Nombre', 'Qualificacio', 'Projecte', 'periode','hasta'];
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
        foreach ($nombres as $nombre){
            $todas->push(AlumnoFctAval::misFcts()->esAval()->where('idAlumno',$nombre['idAlumno'])->orderBy('idAlumno')->first());
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
        $this->setQualityB();
        $this->setActaB();
        $this->panel->setBoton('grid', new BotonImg('fct.apte', ['img' => 'fa-hand-o-up', 'where' => ['calificacion', '!=', '1', 'actas', '==', 0, 'asociacion', '==', 1]]));
        $this->panel->setBoton('grid', new BotonImg('fct.noApte', ['img' => 'fa-hand-o-down', 'where' => ['calProyecto', '<', '5', 'calificacion', '!=', '0', 'actas', '==', 0, 'asociacion', '==', 1]]));
        $this->panel->setBoton('grid', new BotonImg('fct.noAval', ['img' => 'fa-recycle', 'where' => ['calProyecto', '<', '5', 'calificacion', '!=', null, 'actas', '==', 0, 'asociacion', '==', 1]]));
        $this->panel->setBoton('grid', new BotonImg('fct.noAval', ['img' => 'fa-recycle', 'where' => ['calProyecto', '<', '1', 'calificacion', '==', 0, 'actas', '>', 0, 'asociacion', '==', 1]]));
        $this->setProjectB();
        $this->panel->setBoton('grid', new BotonImg('fct.insercio', ['img' => 'fa-square-o', 'roles' => config(self::ROLES_ROL_TUTOR),
            'where' => ['insercion', '==', '0','asociacion','==',1,'calificacion', '==', '1']]));
        $this->panel->setBoton('grid', new BotonImg('fct.insercio', ['img' => 'fa-check-square-o', 'roles' => config(self::ROLES_ROL_TUTOR),
            'where' => ['insercion', '==', '1','asociacion','==',1,'calificacion', '==', '1']]));
        $this->panel->setBoton('grid', new BotonImg('fct.link', ['where' => ['calificacion','>=',1,'asociacion','==',1]]));


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
        DB::transaction(function () use ($id){
            $fct = AlumnoFctAval::find($id);
            $fct->calProyecto = null;
            $fct->save();

            $doc = Documento::where('tipoDocumento','Proyecto')
                ->where('curso',Curso())
                ->whereNull('idDocumento')
                ->where('propietario',$fct->fullName)
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
    public function empresa($id){
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
        $grupo = Grupo::QTutor()->first();

        if ($grupo->acta_pendiente) {
            Alert::message("L'acta pendent esta en procés", 'info');
            return back();
        }

        if ($this->lookForStudents($grupo->proyecto)) {
            $grupo->acta_pendiente = 1;
            $grupo->save();
            avisa(config('contacto.jefeEstudios2'), "Acta pendent grup $grupo->nombre", config('contacto.host.web')."/direccion/$grupo->codigo/acta");
            Alert::message('Acta demanada', 'info');
            return back();
        }

        Alert::message('No tens nous alumnes per ser avaluats', 'warning');
        return back();
    }

    /**
     * @param $projectNeeded
     * @return bool
     */
    private function lookForStudents($projectNeeded){
        $found = false;
        foreach (AlumnoFctAval::MisFcts()->NoAval()->get() as $fct) {
            if ($projectNeeded){
                if (isset($fct->calProyecto)) {
                    $fct->actas = 3;
                    $fct->save();
                    $found = true;
                }
            }
            else
                if (isset($fct->calificacion)) {
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
    private function setQualityB(): void
    {
        $find = Documento::where('propietario', AuthUser()->FullName)->where('tipoDocumento', 'FCT')
                                    ->where('curso', Curso())->first();
        if (!$find) {
            $documents = Adjunto::where('route',"profesor/".AuthUser()->dni)->count();
            $fcts = Fct::misFcts()->where('correoInstructor',0)->count();
            if ($documents || $fcts){
                $this->panel->setBoton('index', new BotonBasico("fct.dropzone.".AuthUser()->dni, ['class' => 'btn-info', 'roles' => config(self::ROLES_ROL_TUTOR)]));
            } else {
                $this->panel->setBoton('index', new BotonBasico("fct.upload", ['class' => 'btn-info', 'roles' => config(self::ROLES_ROL_TUTOR)]));
            }
        }
        else {
            $this->panel->setBoton('index', new BotonBasico("documento.$find->id.edit", ['class' => 'btn-info', 'roles' => config(self::ROLES_ROL_TUTOR)]));
        }
    }

    /**
     *
     */
    private function setActaB(): void
    {
        if (Grupo::QTutor()->first() && !Grupo::QTutor()->first()->acta_pendiente ) {
            $this->panel->setBoton('index', new BotonConfirmacion("fct.acta", ['class' => 'btn-info', 'roles' => config(self::ROLES_ROL_TUTOR)]));
        }
        else {
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
            $this->panel->setBoton('grid', new BotonImg('fct.proyecto', ['img' => 'fa-file', 'roles' => config(self::ROLES_ROL_TUTOR),
                'where' => ['calProyecto', '<', '1', 'actas', '<', 2,'calificacion', '==', '1']]));
            $this->panel->setBoton('grid', new BotonImg('fct.noProyecto', ['img' => 'fa-toggle-off', 'roles' => config(self::ROLES_ROL_TUTOR),
                'where' => ['calProyecto', '<', '0', 'actas', '<', 2,'calificacion', '==', '1']]));
            $this->panel->setBoton('grid', new BotonImg('fct.nullProyecto', ['img' => 'fa-minus-circle', 'roles' => config(self::ROLES_ROL_TUTOR),
                'where' => ['calProyecto', '>=', '0', 'actas', '<', 2,'calificacion', '==', '1']]));
            // Convalidats
            $this->panel->setBoton('grid', new BotonImg('fct.proyecto', ['img' => 'fa-file', 'roles' => config(self::ROLES_ROL_TUTOR),
                'where' => ['calProyecto', '<', '1', 'actas', '<', 2,'asociacion', '==', '2']]));
            $this->panel->setBoton('grid', new BotonImg('fct.noProyecto', ['img' => 'fa-toggle-off', 'roles' => config(self::ROLES_ROL_TUTOR),
                'where' => ['calProyecto', '<', '0', 'actas', '<', 2,'asociacion', '==', '2']]));
            $this->panel->setBoton('grid', new BotonImg('fct.nullProyecto', ['img' => 'fa-minus-circle', 'roles' => config(self::ROLES_ROL_TUTOR),
                'where' => ['calProyecto', '>=', '0', 'actas', '<', 2,'asociacion', '==', '2']]));

            $this->panel->setBoton('grid', new BotonImg('fct.nuevoProyecto', ['img' => 'fa-toggle-on', 'roles' => config(self::ROLES_ROL_TUTOR),
                'where' => ['calProyecto', '<', '5', 'calProyecto', '>=', 0, 'actas', '==', 2]]));
            $this->panel->setBoton('grid', new BotonImg('fct.modificaNota', ['img' => 'fa-edit', 'roles' => config(self::ROLES_ROL_TUTOR),
                'where' => ['calProyecto', '>', 0, 'actas', '<', 2]]));
        }
    }

    public function linkQuality($id){
        $registre = Profesor::findOrFail($id);
        $quien = $registre->fullName;
        $modelo = strtolower('Profesor');
        $botones = [
            'volver' => back()->getTargetUrl(),
            'final' => "/fct/$id/upload"
        ];
        return view('dropzone.index',compact('modelo','id','quien','botones'));

    }

    private function tipoDocument($title){
        $tipos = ['A5'=>'10','A6'=>'11','AVI'=>'11','AV'=>'10','AN.VI'=>'11','AN.V'=>'10',
            'ANEXO5'=>'10','ANEXO6'=>'11','ANNEXVI'=>'11','ANNEXV'=>'10'];

        foreach ($tipos as $key => $tipo){
            if (str_contains(strtoupper($title),$key)){
                return $tipos[$key];
            }
        }
        return null;
    }

    public function send($id){
        $document = array();
        try {
            $fct = AlumnoFct::findOrFail($id);
            $this->SService = new SecretariaService();
        } catch (\Exception $e) {
            Alert::danger($e->getMessage());
            return back();
        }
        foreach(Adjunto::where('route','alumnofctaval/'.$id)->where('extension','pdf')->get() as $key => $adjunto){
            $document[$key]['title'] = $this->tipoDocument($adjunto->title);
            $document[$key]['file'] = $adjunto->route;
            $document[$key]['name'] = $adjunto->name;
            $document[$key]['size'] = $adjunto->size;
            $document[$key]['dni'] = $fct->Alumno->dni;
            $document[$key]['fct'] = $fct;
            $tutor = $adjunto->owner;
        }
        if (count($document) == 2) {
            if (!isset($document[0]['title'])|| !$document[1]['title']){
                if ($document[0]['size'] > $document[1]['size']){
                    $document[0]['title'] = '10';
                    $document[1]['title'] = '11';
                } else {
                    $document[0]['title'] = '11';
                    $document[1]['title'] = '10';
                }
            }
            try {
                $this->SService->uploadA56($document);
                Alert::success('Documentos enviados correctamente');
            } catch (\Exception $e) {
                Alert::danger($e->getMessage());
            }
        } else {
            Alert::danger("Hi ha ".count($document)." documents");
        }
        return back();
    }
} 