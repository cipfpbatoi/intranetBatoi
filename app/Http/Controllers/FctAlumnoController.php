<?php

namespace Intranet\Http\Controllers;


use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\AlumnoFctAval;
use Intranet\Entities\Grupo;
use Intranet\Entities\Profesor;
use Intranet\Entities\FctConvalidacion;
use DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Intranet\Services\FormBuilder;
use mikehaertl\pdftk\Pdf;

class FctAlumnoController extends IntranetController
{
    use traitImprimir;

    const ROLES_ROL_TUTOR = 'roles.rol.tutor';
    protected $perfil = 'profesor';
    protected $model = 'AlumnoFct';
    protected $gridFields = ['Nombre', 'Centro','Instructor','desde','hasta','horas','periode'];
    protected $profile = false;
    protected $titulo = [];
    protected $parametresVista = ['modal' => ['seleccion']];
    protected $modal = true;


    public function search()
    {
        return AlumnoFctAval::misFcts()->esAval()->orderBy('idAlumno')->orderBy('desde')->get();
    }

    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('alumnofct.delete'));
        $this->panel->setBoton('grid', new BotonImg('alumnofct.edit',['where'=>['asociacion', '==', '1']]));
        $this->panel->setBoton('grid', new BotonImg('alumnofct.show',['where'=>['asociacion', '==', '1']]));
        $this->panel->setBoton('grid', new BotonImg('alumnofct.pdf',['where'=>['asociacion', '==', '1']]));
        $this->panel->setBoton('grid', new BotonImg('alumnofct.pdf',['where'=>['asociacion', '==', '2']]));


        $this->panel->setBoton('index', new BotonBasico("fct.create", ['class' => 'btn-info','roles' => config(self::ROLES_ROL_TUTOR)]));
        $this->panel->setBoton('index', new BotonBasico("alumnofct.convalidacion", ['class' => 'btn-info','roles' => config(self::ROLES_ROL_TUTOR)]));
        $this->panel->setBoton('index', new BotonBasico("fct", ['class' => 'btn-info','roles' => config(self::ROLES_ROL_TUTOR)]));
        $this->panel->setBoton('index', new BotonBasico("fct.pg0301.print",['class'=>'btn-secondary selecciona','roles' => config(self::ROLES_ROL_TUTOR),'data-url'=>'/api/documentacionFCT/pg0301']));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0401.print",['class'=>'btn-secondary selecciona' ,'roles' => config(self::ROLES_ROL_TUTOR),'data-url'=>'/api/documentacionFCT/pr0401']));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0402.print",['class'=>'btn-secondary selecciona' , 'roles' => config(self::ROLES_ROL_TUTOR),'data-url'=>'/api/documentacionFCT/pr0402']));
        $this->panel->setBoton('index', new BotonBasico("fct.pasqua.print",['class' => 'selecciona btn-secondary','data-url'=> "/api/documentacionFCT/pasqua",'roles' => config(self::ROLES_ROL_TUTOR)]));
        Session::put('redirect', 'FctAlumnoController@index');
    }
        //

    public function nuevaConvalidacion()
    {
        $elemento = new FctConvalidacion();
        $formulario = new FormBuilder($elemento,[
            'idAlumno' => ['type' => 'select'],
            'asociacion' => ['type' => 'hidden'],
            'horas' => ['type' => 'text'],
        ]);
        $modelo = $this->model;
        return view($this->chooseView('create'), compact('formulario', 'modelo'));
    }

    public function storeConvalidacion(Request $request)
    {
        DB::transaction(function() use ($request){
            $idAlumno = $request['idAlumno'];
            $elementos = FctConvalidacion::where('idColaboracion',$request->idColaboracion)
                    ->where('asociacion',$request->asociacion)
                    ->get();
            $id = null;
            foreach ($elementos as $elemento){
                    if ($elemento->Periode == PeriodePractiques(Hoy())){
                        $id = $elemento->id;
                        break;
                    }
                }
            if (!$id){ 
                $elemento = new FctConvalidacion();
                $this->validateAll($request, $elemento);
                $id = $elemento->fillAll($request);
            } 
            $elemento->Alumnos()->attach($idAlumno,['desde'=> FechaInglesa(Hoy()),'horas'=>$request->horas,'calificacion' => 2,'correoAlumno'=>1]);

            return $id;
        });
        
        return $this->redirect();
    }
    
    public function show($id)
    {
        $fct = AlumnoFct::findOrFail($id);
        return redirect("/fct/$fct->idFct/show");
    }
    
    public function pdf($id)
    {
        $fct = AlumnoFct::findOrFail($id);
        if ($fct->asociacion == 1) {
            return self::preparePdf($id)->stream();
        }
        if ($fct->asociacion == 2) {
            return response()->file(self::prepareExem(($id)));
        }

    }

    public static function prepareExem($id)
    {
        $fct = AlumnoFct::findOrFail($id);
        $alumno = $fct->Alumno;
        $tutor = AuthUser();
        $grupo = Grupo::where('tutor', '=', AuthUser()->dni)->first();
        /*$grupo = $fct->Alumno->Grupo->first();
        $cicle = $grupo->Ciclo;
        $tutor = $grupo->Tutor;
        $cdept = $cicle->departament->Jefe;
        $director = Profesor::find(config(fileContactos().'.director'));*/
        if (file_exists(storage_path("tmp/exencion_$id.pdf"))) {
            unlink(storage_path("tmp/exencion_$id.pdf"));
        }
        $file = storage_path("tmp/exencion_$id.pdf");

        $pdf = new Pdf('fdf/InformeExencionFCT.pdf');
        $arr['untitled1'] = "NIA: $alumno->nia - $alumno->fullName -".$alumno->telef1??$alumno->telef2." - $alumno->email";
        $arr['untitled2'] = config('contacto.nombre').' '.config('contacto.codi') ;
        $arr['untitled3'] = $grupo->Ciclo->vliteral;
        $arr['untitled4'] = $tutor->fullName.' - '.$tutor->dni.' - '.$tutor->telef1??$tutor->telef2.' - '.$tutor->email;
        $arr['untitled18'] = config('contacto.poblacion');
        $arr['untitled19'] = day(Hoy());
        $arr['untitled20'] = month(Hoy());
        $arr['untitled21'] = year(Hoy());

        $arr['untitled23'] = $tutor->fullName;

        $pdf->fillform($arr)
                ->saveAs($file);
        return storage_path("tmp/exencion_$id.pdf");;

    }
    /*
    public static function prepareExem($id){
        $fct = AlumnoFct::findOrFail($id);
        $grupo = $fct->Alumno->Grupo->first();
        $cicle = $grupo->Ciclo;
        $tutor = $grupo->Tutor;
        $cdept = $cicle->departament->Jefe;
        $director = Profesor::find(config(fileContactos().'.director'));
        $dades = ['date' => FechaString($fct->hasta),
            'cicle' => $cicle,
            'tutor' => $tutor,
            'cdept' => $cdept,
            'modulos' => $grupo->Modulos,
            'centro' => config('contacto.nombre'),
            'poblacion' => config('contacto.poblacion'),
            'provincia' => config('contacto.provincia'),
            'director' => $director
        ];
        return self::hazPdf($cicle->normativa=='LOE'?'pdf.fct.exempcio_loe':'pdf.fct.exempcio_logse', $fct, $dades);
    }
    */

    public static function preparePdf($id){
        $fct = AlumnoFct::findOrFail($id);
        $secretario = Profesor::find(config(fileContactos().'.secretario'));
        $director = Profesor::find(config(fileContactos().'.director'));
        $dades = ['date' => FechaString($fct->hasta),
            'consideracion' => $secretario->sexo === 'H' ? 'En' : 'Na',
            'secretario' => $secretario->FullName,
            'centro' => config('contacto.nombre'),
            'poblacion' => config('contacto.poblacion'),
            'provincia' => config('contacto.provincia'),
            'director' => $director->FullName
        ];
        return self::hazPdf('pdf.fct.certificatsFCT', [$fct], $dades);
    }

    /**
    public function email($id)
    {
        // CARREGANT DADES
        $elemento = AlumnoFct::findOrFail($id);


        // MANE ELS TREBALLS
        if ($elemento->Alumno->email != '' && config('curso.enquestesAutomatiques')){
            $remitente = ['email' => AuthUser()->email, 'nombre' => AuthUser()->FullName, 'id' => AuthUser()->dni];
            dispatch(new SendEmail($elemento->Alumno->email, $remitente, 'email.fct.alumno', $elemento));
            Alert::info('Correu enviat');
            return back();
        }

        Alert::info("L'alumne no té correu. Revisa-ho");
        return back();
    }
     */
    
    public function pg0301($id){
       $fct = AlumnoFct::find($id);
       $fct->pg0301 = $fct->pg0301?0:1;
       $fct->save();
       return redirect()->action('PanelPG0301Controller@indice',['id' => $fct->Grup]);
    }

} 