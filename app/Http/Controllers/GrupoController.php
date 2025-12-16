<?php
namespace Intranet\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Intranet\Botones\BotonImg;
use Intranet\Entities\Alumno;
use Intranet\Entities\AlumnoGrupo;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Curso;
use Intranet\Entities\Grupo;
use Intranet\Entities\Horario;
use Intranet\Http\Traits\Imprimir;
use Intranet\Jobs\SendEmail;
use Intranet\Services\SecretariaService;
use Jenssegers\Date\Date;
use SebastianBergmann\Comparator\Exception;
use Styde\Html\Facades\Alert;

/**
 * Class GrupoController
 * @package Intranet\Http\Controllers
 */
class GrupoController extends IntranetController
{

    const DIRECCION ='roles.rol.direccion';
    const TUTOR ='roles.rol.tutor';
    const ORIENTADOR ='roles.rol.orientador';


    use Imprimir;

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Grupo';
    /**
     * @var array
     */
    protected $gridFields = ['codigo', 'nombre', 'Xtutor', 'Xciclo'  ,'Torn'];
    protected $parametresVista = ['modal' => [  'selAlumGrup']];





    /**
     * @return \Illuminate\Database\Eloquent\Collection|Grupo[]|mixed
     */
    protected function search(){

        return esRol(AuthUser()->rol,config(self::DIRECCION)) || esRol(AuthUser()->rol,config(self::ORIENTADOR))  ?
                Grupo::with('Ciclo')->with('Tutor')->with('Tutor.Sustituye')->get():
                Grupo::with('Ciclo')->MisGrupos()->get();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function detalle($id)
    {
        return redirect()->route('alumnogrupo.index', ['grupo' => $id]);
    }


    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBotonera([], ['pdf', 'horario']);
        $this->panel->setBoton('grid', new BotonImg('grupo.detalle', ['img' => 'fa-group']));
        $this->panel->setBoton('grid', new BotonImg('grupo.carnet', ['roles' => config(self::DIRECCION)]));
        $this->panel->setBoton('grid', new BotonImg('grupo.carnet', ['roles' => config(self::TUTOR),'where'=>['tutor','==',AuthUser()->dni]]));
        $this->panel->setBoton('grid', new BotonImg('grupo.edit', ['roles' => config(self::DIRECCION)]));
        $this->panel->setBoton('grid',new BotonImg('equipo.grupo',['img' => 'fa-graduation-cap']));
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'grupo.list',
                    [
                         'img' => 'fa-file-excel-o',
                        'class' => 'selecciona',
                     ]
            )
        );



        if (AuthUser()->xdepartamento === 'Fol' && date('Y-m-d') > config('variables.certificatFol')) {
            $this->panel->setBoton('grid',new BotonImg('grupo.fol',['img' => 'fa-square-o','where'=>['fol','==', 0]]));
            $this->panel->setBoton('grid',new BotonImg('grupo.fol',['img' => 'fa-check','where'=>['fol','==', 1]]));
        }

        $this->panel->setBoton('grid',new BotonImg('direccion.fol',
            ['img' => 'fa-file-word-o','roles' => config(self::DIRECCION),'where'=>['fol','==', 1]]));
        $cursos = Curso::Activo()->get();
        foreach ($cursos as $curso) {
            if (($curso->aforo == 0) || ($curso->NAlumnos < $curso->aforo * config('variables.reservaAforo'))){
                $this->panel->setBoton('grid', new BotonImg("alumnocurso.registerGrupo/" . $curso->id, ['text' => trans('messages.generic.register') . $curso->titulo, 'img' => 'fa-institution']));

            }
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function horario($id)
    {
        $horario = Horario::HorarioGrupo($id);
        $titulo = Grupo::findOrFail($id)->nombre;
        return view('horario.grupo', compact('horario', 'titulo'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function asigna()
    {
        $todos = Grupo::all();
        foreach ($todos as $uno) {
            if ($uno->ciclo == ''){
                $ciclo = Ciclo::select('id')
                        ->where('codigo', '=', substr($uno->codigo, 1, 4))
                        ->first();
                if ($ciclo) {
                    $uno->idCiclo = $ciclo->id;
                    $uno->save();
                }
            }
        }
        return back();
    }

    /**
     * @param $grupo
     * @return mixed
     */
    public function pdf($grupo)
    {
        return $this->hazPdf('pdf.alumnos.fotoAlumnos',AlumnoGrupo::where('idGrupo',$grupo)->orderBy('subGrupo')->orderBy('posicion','desc')->get()->groupBy('subGrupo'), Grupo::find($grupo))->stream();
    }

    /**
     * @param $grupo
     * @return mixed

    public function fse($grupo)
    {
        return $this->hazPdf('pdf.reunion.actaFSE',$this->alumnos($grupo), Grupo::find($grupo) )->stream();
    }*/

    /**
     * @param $grupo
     * @return mixed
     */
    public function carnet($grupo)
    {
        return $this->hazPdf('pdf.carnet', Alumno::QGrupo($grupo)
            ->OrderBy('apellido1')
            ->OrderBy('apellido2')
            ->get(), [Date::now()->format('Y'), 'Alumnat - Student'], 'portrait', [85.6, 53.98])->stream();
    }

    public function list(Request $request)
    {
        // Cerca el grup i els alumnes associats
        $ids = array( );
        foreach ($request->toArray() as $nia => $value){
             if ($value === 'on') {
                $ids[] = $nia;
            }
        }
        $alumnos =  Alumno::whereIn('nia',$ids)->get()->sortBy( 'nameFull');
        $alumnes = hazArray($alumnos,'nameFull');

        // Combina el nom del grup amb els noms dels alumnes
        $nomsAlumnes = implode('; ', $alumnes);


        // Retorna la llista d'alumnes separada per ;
        return response($nomsAlumnes, 200)
            ->header('Content-Type', 'text/plain');
    }

    /*
    public function list($idGrupo)
    {
        $grupo = Grupo::find($idGrupo);
        $alumnos = hazArray($grupo->Alumnos->sortBy('nameFull'),'nameFull');
        $gr = array('grupo' => $grupo->codigo.' - '.$grupo->nombre);
        $columna = array_merge($gr,$alumnos);
        $xls = new ExcelService(storage_path('/tmp/'.$idGrupo.'.xlsx'));
        $xls->render($columna);
        return response()->download(
            storage_path('/tmp/'.$idGrupo.'.xlsx'), // Ajusta el camí segons la teva estructura
            'alumnes_'.$idGrupo.'.xlsx', // Aquest serà el nom del fitxer per al client
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'] // Aquest és el Content-Type per a fitxers .xlsx
        );
    }
    */

    /**
     * @param $grupo
     * @return mixed
     */
    public function certificados($grupo)
    {
        try {
            $sService = new SecretariaService();
        } catch (\Exception $e) {
            echo 'No hi ha connexió amb el servidor de matrícules';
            exit();
        }
        $grupo = Grupo::find($grupo);
        $datos['ciclo'] = $grupo->Ciclo;
        $remitente = ['email' => cargo('secretario')->email, 'nombre' => cargo('secretario')->FullName];
        $count = 0;

        foreach ($grupo->Alumnos as $alumno){
            if ($alumno->fol == 1){

                try {
                    $id = $alumno->nia;
                    if (file_exists(storage_path("tmp/fol_$id.pdf"))) {
                        unlink(storage_path("tmp/fol_$id.pdf"));
                    }
                    self::hazPdf('pdf.alumnos.'.$grupo->Ciclo->normativa, [$alumno], cargaDatosCertificado($datos),
                        'portrait')->save(storage_path("tmp/fol_$id.pdf"));
                    $attach = ["tmp/fol_$id.pdf" => 'application/pdf'];
                    $document = array();
                    $document['title'] = 15;
                    $document['dni'] = $alumno->dni;
                    $document['alumne'] = trim($alumno->shortName);
                    $document['route'] = "tmp/fol_$id.pdf";
                    $document['name'] = "fol_$id.pdf";
                    $document['size'] = filesize(storage_path("tmp/fol_$id.pdf"));
                    $sService->uploadFile($document);
                    dispatch(new SendEmail($alumno->email, $remitente, 'email.fol', $alumno, $attach));
                    $count++;
                } catch (\Exception $e){
                    Alert::danger($e->getMessage());
                }
            }
        }
        $grupo->fol = 2;
        $grupo->save();
        if ($count){
            Alert::info("$count Correus enviats");
        } else {
            Alert::info("Cap Correu enviat");
        }
        return back();
    }

    /**
     * @param $alumno
     * @return mixed
     */
    public function certificado($alumno)
    {
        $grupo = Alumno::findOrFail($alumno)->Grupo->first();
        $datos['ciclo'] = $grupo->Ciclo;  
        return $this->hazPdf('pdf.alumnos.'.$grupo->Ciclo->normativa, Alumno::where('nia',$alumno)->get(),cargaDatosCertificado($datos),'portrait')->stream();
    }

    public function checkFol($id)
    {
        $grupo = Grupo::findOrFail($id);
        $grupo->fol = ($grupo->fol==0)?1:0;
        $grupo->save();
        return back();

    }

}
