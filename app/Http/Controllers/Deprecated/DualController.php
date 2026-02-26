<?php

namespace Intranet\Http\Controllers\Deprecated;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Application\Horario\HorarioService;
use Intranet\Http\Controllers\Core\ModalController;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\UI\Botones\BotonBasico;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Alumno;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Documento;
use Intranet\Entities\Dual;
use Intranet\Entities\Fct;
use Intranet\Exceptions\IntranetException;
use Intranet\Http\Requests\DualRequest;
use Intranet\Http\Traits\Core\Imprimir;
use Illuminate\Support\Carbon;
use mikehaertl\pdftk\Pdf;
use Styde\Html\Facades\Alert;


/**
 * Class DualAlumnoController
 * @package Intranet\Http\Controllers
 *
 * @deprecated Flux legacy de FP Dual.
 *
 * Es manté només per compatibilitat de consulta/gestió antiga.
 * La creació de nous registres s'ha deshabilitat.
 */
class DualController extends ModalController
{
    use Imprimir;

    private ?GrupoService $grupoService = null;

    const CONTACTO_NOMBRE = 'contacto.nombre';
    const CONTACTO_CODI = 'contacto.codi';
    const CONTACTO_POBLACION = 'contacto.poblacion';
    const CONTACTO_PROVINCIA = 'contacto.provincia';
    const OPCIÓN_1 = 'Opción1';
    const D_M_Y = "d/m/y";


    /**
     * @var string
     */
    protected $model = 'Fct';
    /**
     * @var array
     */
    protected $gridFields = ['Nombre', 'Centro','Instructor','desde','hasta','horas','beca'];
    /**
     * @var bool
     */
    protected $profile = false;
    /**
     * @var array
     */
    protected $titulo = [];
    /**
     * @var array
     */
    protected $formFields = ['idAlumno' => ['type' => 'select'],
        'idColaboracion' => ['type' => 'select'],
        'idInstructor' => ['type' => 'select'],
        'asociacion' => ['type' => 'hidden'],
        'desde' => ['type' => 'date'],
        'hasta' => ['type' => 'date'],
        'horas' => ['type' => 'text'],
        'beca' => ['type' => 'text']];




    /**
     * @return mixed
     */
    public function search()
    {
        return AlumnoFct::misDual()->orderBy('idAlumno')->orderBy('desde')->get();
    }

    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $user = AuthUser()->dni;
        $grupo = $this->grupos()->firstByTutorDual(AuthUser()->dni);
        if ($grupo){
            $ciclo = $grupo->ciclo;
            if ($ciclo->CompleteDual){
                $this->panel->setBoton(
                    'index',
                    new BotonBasico('cicloDual.edit', ['text'=>'Edita Paràmetres de Cicle', 'class' => 'btn-info'])
                );
            }
            else {
                $this->panel->setBoton(
                    'index',
                    new BotonBasico('cicloDual.edit', ['text'=>'Edita Paràmetres de Cicle', 'class' => 'btn-warning'])
                );
            }
        }

        $this->panel->setBoton('grid', new BotonImg('dual.delete'));
        $this->panel->setBoton('grid', new BotonImg('dual.edit'));
        $this->panel->setBoton('grid', new BotonImg('dual.informe',['img'=>'fa-file-zip-o']));
        $this->panel->setBoton('index', new BotonBasico("dual.anexeVI", ['class' => 'btn-info','id' => 'anexoVI']));
        $this->panel->setBoton('index', new BotonBasico("dual.anexeXIV", ['class' => 'btn-info','id' => 'anexoXIV']));

        Session::put('redirect', 'Deprecated\\DualController@index');
    }
        //


    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function show($id)
    {
        $fct = AlumnoFct::findOrFail($id);
        return redirect("/fct/$fct->idFct/show");
    }


    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(DualRequest $request, $id)
    {
        DB::transaction(function() use ($request, $id) {
            $alumno = AlumnoFct::findOrFail($id);
            $elemento = $alumno->Dual;

            $alumno->desde = FechaInglesa($request['desde']);
            $alumno->hasta = FechaInglesa($request['hasta']);
            $alumno->horas = $request['horas'];
            $alumno->beca = $request['beca'];
            $alumno->save();
            $elemento->idInstructor = $request['idInstructor'];
            $elemento->save();
        });

        return $this->redirect();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        Alert::warning('Dual està deprecated: no es permet crear nous registres.');
        return $this->redirect();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(DualRequest $request)
    {
        Alert::warning('Dual està deprecated: no es permet crear nous registres.');
        return $this->redirect();
    }

    public function destroy($id)
    {
        if ($elemento = AlumnoFct::findOrFail($id)) {
            $elemento->delete();
        }
        return $this->redirect();
    }
    /**
     * @param $id
     * @param string $informe
     * @return mixed
     */
    public function informe($fct, $informe='anexe_vii',$stream=true,$data=null)
    {
        $id = is_object($fct)?$fct->id:$fct;
        $fct = is_object($fct)?$fct:AlumnoFct::findOrFail($id);
        $informe = 'dual.'.$informe;
        $secretario = cargo('secretario');
        $director = cargo('director');
        $fechaDocument = $data??FechaPosterior($fct->hasta);
        $dades = ['date' => $fechaDocument,
            'consideracion' => $secretario->sexo === 'H' ? 'En' : 'Na',
            'secretario' => $secretario->FullName,
            'centro' => config(self::CONTACTO_NOMBRE),
            'codigo' => config(self::CONTACTO_CODI),
            'poblacion' => config(self::CONTACTO_POBLACION),
            'provincia' => config(self::CONTACTO_PROVINCIA),
            'director' => $director->FullName
        ];

        $orientacion = substr($informe,5,5)==='anexe'?'landscape':'portrait';
        $pdf = $this->hazPdf($informe, $fct,$dades,$orientacion,'a4',10);
        if ($stream) {
            return $pdf->stream();
        } else {
            $file = storage_path("tmp/dual$id/$informe".'.pdf');
            if (!file_exists($file)){
                $pdf->save($file);
            }
            return $file;
        }
    }


    protected function getGestor($doc, $ciclo)
    {
        $documento = Documento::where('tags',"$doc,$ciclo")->where('tipoDocumento','Dual')->first();
        if ($documento) {
            return storage_path('app/' . $documento->fichero);
        }
    }



    private function chooseAction($fct, $document, &$zip, $data)
    {
        $ciclo = $fct->Fct->Colaboracion->Ciclo->acronim;
        $carpeta_autor = $fct->Fct->Centro."/010_FaseAutoritzacioConveni/";
        $carpeta_firma = $fct->Fct->Centro."/020_FaseFirmaConveni_".$fct->Alumno->dualName."/";
        $carpeta_formacio = $fct->Fct->Centro."/040_FormacioEmpresa_".$fct->Alumno->dualName."/";
        $carpeta_final = $fct->Fct->Centro."/050_InformesFinals/";
        switch ($document) {
            case 'covid':
                $zip->addFile($this->informe($fct,'covid',false,$data),$carpeta_firma."ConformitatAlumne_Covid19_v20201005.pdf"); break;
            case 'declaracioResponsable':
                $zip->addFile($this->informe($fct,'declaracioResponsable',false,$data),$carpeta_firma."ConformitatEmpresa_Covid19_v20201005.pdf"); break;
            case 'beca':
                $zip->addFile($this->informe($fct,'beca',false,$data),$carpeta_firma."Beca.pdf"); break;
            case 'justAl':
                $zip->addFile($this->informe($fct,'justAl',false,$data),$carpeta_firma."JustificanteEntregaCalendario_a_Alumno.pdf");break;
            case 'justEm':
                $zip->addFile($this->informe($fct,'justEm',false,$data),$carpeta_firma."JustificanteEntregaCalendario_a_Empresa.pdf");break;
            case 'DOC1':
                $zip->addFile($this->printDOC1($fct,$data),$carpeta_firma."DOCUMENTO 1 DATOS BÁSICOS PARA EL PROGRAMA DE FORMACIÓN.pdf");break;
            case 'DOC2':
                $zip->addFile($this->getGestor('DOC2',$ciclo),$carpeta_firma."DOCUMENTO 2 CUADRO HORARIO DEL CICLO EN FP DUAL.odt");break;
            case 'DOC3a' :
                $zip->addFile($this->getGestor('DOC3',curso()),$carpeta_firma."DOCUMENTO 3 CALENDARIO ANUAL CENTRO EMPRESA ".curso().".odt");break;
            case 'DOC3b' :
                $zip->addFile($this->getGestor('DOC3',cursoAnterior()),$carpeta_firma."DOCUMENTO 3 CALENDARIO ANUAL CENTRO EMPRESA ".cursoAnterior().".odt");break;
            case 'DOC4' :
                $zip->addFile($this->printDOC4($fct),$carpeta_firma."DOCUMENTO 4 HORARIO DEL CICLO FORMATIVO EN EL CENTRO.pdf");break;
            case 'DOC5' :
                $zip->addFile($this->getGestor('DOC5',$ciclo),$carpeta_firma."DOCUMENTO 5 PROGRAMA DE FORMACIÓN DE MÓDULOS EN DUAL.odt");break;
            case 'conveni':
                $zip->addFile($this->printConveni($fct,$data),$carpeta_autor."CONVENI AMB L'EMPRESA COLABORADORA.pdf");break;
            case 'annexiv':
                $zip->addFile($this->printAnexeIV($fct,$data),$carpeta_autor."ANEXO IV DECLARACION RESPONSABLE DE L'EMPRESA COLABORADORA.pdf");break;
            case 'annexii' :
                $zip->addFile($this->printAnexeXII($fct,$data),$carpeta_firma."ANEXO XII CONFORMIDAD DEL ALUMNADO.pdf");break;
            case 'annexv':
                $zip->addFile($this->certificado($fct,$data),$carpeta_firma."ANEXO V CERTIFICADO DE RIESGOS LABORALES.pdf");break;
            case 'annexevii':
                if ($data != null ) {
                    $zip->addFile($this->printAnexeVII($fct, $data), $carpeta_formacio . "ANEXO_VII.pdf");
                } else {
                    $fc1 = new Carbon($fct->desde);
                    $fc2 = new Carbon($fct->hasta);
                    $i = 1;
                    while ($fc1 < $fc2){
                        $zip->addFile($this->printAnexeVII($fct, $fc1,$i), $carpeta_formacio . "ANEXO_VII_".$i.".pdf");
                        $fc1->addDays(28);
                        $i++;
                    }
                }
                break;
            case 'annexva':
                $zip->addFile($this->informe($fct,'anexe_va',false,$data),$carpeta_formacio."ANEXO_V-A.pdf");break;
            case 'annexvb':
                $zip->addFile($this->informe($fct,'anexe_vb',false,$data),$carpeta_formacio."ANEXO_V-B.pdf");break;
            case 'annexiii':
                $zip->addFile($this->printAnexeXIII($fct,$data),$carpeta_formacio."ANEXO_XIII.pdf");break;
            case 'justificants' :
                $zip->addFile($this->informe($fct,'justificant_alumne',false,$data),$carpeta_formacio."Justificant_alumne.pdf");
                $zip->addFile($this->informe($fct,'justificant_empresa',false,$data),$carpeta_formacio."Justificant_empresa.pdf");
                $zip->addFile($this->informe($fct,'justificant_instructor',false,$data),$carpeta_formacio."Justificant_instructor.pdf");break;
            default : break;
        }
    }


    public function certificado($fct, $date)
    {
        $grupo = $fct->Alumno->Grupo->first();
        $id = $fct->id;
        $datos['ciclo'] = $grupo->Ciclo;
        $pdf =  $this->hazPdf('pdf.alumnos.'.$grupo->Ciclo->normativa,Alumno::where('nia',$fct->Alumno->nia)->get(),cargaDatosCertificado($datos),'portrait');
        $file = storage_path("tmp/dual$id/anexe_v".'.pdf');
        if (!file_exists($file)){
            $pdf->save($file);
        }
        return $file;
    }

    protected function getInforme($id){
        return view('dual.informe',compact('id'));
    }

    private function deleteDir($folder)
    {
        $files = glob("$folder*"); //obtenemos todos los nombres de los ficheros
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            } //elimino el fichero
        }
        rmdir($folder);
    }

    protected function putInforme($id,Request $request){
        $input = $request->all();
        $fct = AlumnoFct::findOrFail($id);
        $folder = storage_path("tmp/dual$id/");
        $zip_file = storage_path("tmp/dual_".$fct->Alumno->dualName.".zip");
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }
        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        foreach ($input as $index => $value) {
            if ($index !== '_token') {
                $this->chooseAction($fct, $index, $zip,$request->data);
            }
        }
        $zip->close();
        $this->deleteDir($folder);

        return response()->download($zip_file);
    }

    public function printAnexeXII($fct, $data)
    {
        $id = $fct->id;
        $file = storage_path("tmp/dual$id/anexo_xii.pdf");
        if (!file_exists($file)) {
            $pdf = new Pdf('fdf/ANEXO_XII.pdf');
            $pdf->fillform($this->makeArrayPdfAnexoXII($fct,$data))
                ->saveAs($file);
        }
        return $file;
    }

    public function printAnexeIV($fct, $data)
    {
        $id = $fct->id;
        $file = storage_path("tmp/dual$id/anexo_iv.pdf");
        if (!file_exists($file)) {
            $pdf = new Pdf('fdf/ANEXO_IV.pdf');
            $pdf->fillform($this->makeArrayPdfAnexoIV($fct, $data))
                ->saveAs($file);
        }
        return $file;
    }

    protected function makeArrayPdfAnexoIV($fct,$data){
        $array[1] = $fct->Fct->Colaboracion->Centro->Empresa->nombre;
        $array[2] = $fct->Fct->Colaboracion->Centro->Empresa->cif;
        $array[3] = $fct->Fct->Colaboracion->Centro->Empresa->direccion;
        $array[4] = $fct->Fct->Colaboracion->Centro->Empresa->localidad;
        $array[5] = 'Alacant';
        $array[6] = 'Espanya';
        $array[7] = $fct->Fct->Colaboracion->Centro->Empresa->codiPostal;
        $array[8] = $fct->Fct->Colaboracion->Centro->Empresa->telefono;
        $array[9] = $fct->Fct->Centro;
        $array[10] = $fct->Fct->Colaboracion->Centro->direccion;
        $array[11] = 'Alacant';
        $array[12] = 'Espanya';
        $array[13] = $fct->Fct->Colaboracion->Centro->codiPostal;
        $array[14] = $fct->Fct->Colaboracion->Centro->telefono;
        $array[15] = $fct->Fct->Colaboracion->Centro->Empresa->gerente;
        $array[16] = $fct->Fct->Colaboracion->Ciclo->vliteral;
        if ($fct->Fct->Colaboracion->Ciclo->tipo == 1) {
            $array[17] = 'Sí';
        }
        else {
            $array[19] = 'Sí';
        }
        $array[18] = 'Sí';
        $array[21] = substr($fct->Fct->Colaboracion->Ciclo->Departament->vliteral,12);
        $array[22] = 'Sí';
        $array[24] = config(self::CONTACTO_NOMBRE);
        $array[25] = config(self::CONTACTO_CODI);
        $array[26] = 'Sí';
        $array[28] = config(self::CONTACTO_POBLACION);
        $array[29] = config(self::CONTACTO_PROVINCIA);
        $array[30] = config('contacto.email');
        $fc1 = new Carbon($data);
        Carbon::setLocale('ca');
        $array[31] = config(self::CONTACTO_POBLACION);
        $array[32] = $fc1->format('d');
        $array[33] = $fc1->format('F');
        $array[34] = $fc1->format('Y');
        $array[35] = $fct->Fct->Colaboracion->Centro->Empresa->gerente;

        return $array;
    }

    public function printConveni($fct, $data)
    {
        $id = $fct->id;
        $file = storage_path("tmp/dual$id/conveni.pdf");
        if (!file_exists($file)) {
            $pdf = new Pdf('fdf/Conveni.pdf');
            $pdf->fillform($this->makeArrayPdfConveni($fct,$data))
                ->saveAs($file);
        }
        return $file;
    }

    protected function makeArrayPdfCOnveni($fct,$data){
        $array[1] = $fct->Fct->Colaboracion->Centro->Empresa->nombre;
        if ($fct->Fct->Colaboracion->Ciclo->tipo == 1) {
            $array['CORRESPONENT AL CICLE FORMATIU 1'] = 'GRAU MITJA';
            $array['CORRESPONDIENTE AL CICLO FORMATIVO 1'] = 'GRADO MEDIO';
        } else {
            $array['CORRESPONENT AL CICLE FORMATIU 1'] = 'GRAU SUPERIOR';
            $array['CORRESPONDIENTE AL CICLO FORMATIVO 1'] = 'GRADO SUPERIOR';
        }
        $array['CORRESPONENT AL CICLE FORMATIU 2'] = $fct->Fct->Colaboracion->Ciclo->vliteral;
        $array['CORRESPONDIENTE AL CICLO FORMATIVO 2'] = $fct->Fct->Colaboracion->Ciclo->cliteral;


        $array['undefined_5'] = $fct->Fct->Colaboracion->Centro->codiPostal;
        $array[11] =  $fct->Fct->Colaboracion->Centro->Empresa->gerente;
        $array['undefined_4'] = explode(',',$fct->Fct->Colaboracion->Centro->direccion)[0]??'';

        $array['acceptar'] = config(self::CONTACTO_NOMBRE);

        $array['este conveni precisa el contingut i abast'] =  $fct->Fct->Colaboracion->Ciclo->dataSignaturaDual->format('d/m/Y')??'';
        $array['AA'] = $fct->Fct->Colaboracion->Centro->Empresa->localidad;
        $array['undefined_2'] = $fct->Fct->Colaboracion->Centro->Empresa->cif;
        $array['Província de'] = 'Alacant';
        $array['CP'] = explode(',',$fct->Fct->Colaboracion->Centro->direccion)[1]??'';

        return $array;
    }


    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function printDOC4($fct)
    {
        $id = $fct->id;
        $file = storage_path("tmp/dual$id/doc4.pdf");
        if (!file_exists($file)){
            $grupo = $fct->Alumno->Grupo->first();
            $horario = app(HorarioService::class)->semanalByGrupo((string) $grupo->codigo);
            $turno = isset($horario['L'][2]) ? 'mati':'vesprada';
            $ciclo = $fct->Fct->Colaboracion->Ciclo->vliteral;
            $dades = compact('grupo','ciclo','turno');
            $pdf = $this->hazPdf('dual.doc4', $horario,$dades,'portrait','a4',10);
            $pdf->save($file);

        }
        return $file;
    }



    /**
     * @param $array
     * @return mixed
     */
    private function makeArrayPdfAnexoXII($fct,$data)
    {
        $array[1] = $fct->Alumno->fullName;
        $array[2] = $fct->Alumno->dni;
        $array[3] = $fct->Fct->Colaboracion->Ciclo->vliteral;
        $array[4] =$fct->Fct->Colaboracion->Ciclo->tipo == 1?'Mitjà':'Superior';
        $array[5] = substr($fct->Fct->Colaboracion->Ciclo->Departament->vliteral,12);
        $array[6] = config(self::CONTACTO_NOMBRE);
        $array[7] = config(self::CONTACTO_CODI);
        $array[8] = curso();

        $array[9] = $array[1];
        $array[10] = $array[2];
        $array[11] = $fct->Fct->Colaboracion->Ciclo->cliteral;
        $array[12] =$fct->Fct->Colaboracion->Ciclo->tipo == 1?'Medio':'Superior';
        $array[13] = $array[5];
        $array[14] = $array[6];
        $array[15] = $array[7];
        $array[16] = $array[8];
        $fc1 = new Carbon($data);
        Carbon::setLocale('ca');
        $array[17] = config(self::CONTACTO_POBLACION);
        $array[18] = $fc1->format('d');
        $array[19] = $fc1->format('F');
        $array[20] = $fc1->format('Y');
        $array[21] = $array[1];
        $array[23] = $fct->Fct->Centro;
        $array[24] = explode(',',$fct->Fct->Colaboracion->Centro->direccion)[0]??$fct->Fct->Colaboracion->Centro->direccion;
        $array[25] = explode(',',$fct->Fct->Colaboracion->Centro->direccion)[1]??'';
        $array[26] = $fct->Fct->Colaboracion->Centro->codiPostal;
        $array[27] = $fct->Fct->Colaboracion->Centro->localidad;
        $array[28] = 'Alacant';
        $array[29] = 'Espanya';
        $array[30] = $array[23];
        $array[31] = $array[24];
        $array[32] = $array[25];
        $array[33] = $array[26];
        $array[34] = $array[27];
        $array[35] = 'Alacant';
        $array[36] = 'Espanya';
        $array[37] = $array[17];
        $array[38] = $array[18];
        $array[39] = $array[19];
        $array[40] = $array[20];
        $array[41] = $array[1];

        return $array;
    }


    public function printAnexeXIII($fct,$data){
        $id = $fct->id;
        $file = storage_path("tmp/dual$id/anexo_xiiI.pdf");
        if (!file_exists($file)) {
            $pdf = new Pdf('fdf/ANEXO_XIII.pdf');
            $pdf->fillform($this->makeArrayPdfAnexoXIII($fct,$data))
                ->saveAs($file);
        }
        return $file;
    }
    public function printJustificants($fct,$data){
        $id = $fct->id;
        $file = storage_path("tmp/dual$id/justificants.pdf");
        if (!file_exists($file)) {
            $pdf = new Pdf('fdf/ANEXO_XIII.pdf');
            $pdf->fillform($this->makeArrayPdfAnexoXIII($fct,$data))
                ->saveAs($file);
        }
        return $file;
    }


    private function makeArrayPdfAnexoXIII($fct,$data)
    {
        $array[1] = cargo('secretario')->fullName;
        $array[2] = config(self::CONTACTO_NOMBRE);
        $array[3] = config(self::CONTACTO_CODI);
        $array[4] = $fct->Alumno->fullName;
        $array[5] = $fct->Alumno->dni;
        $array[6] = $fct->horas;
        $array[7] = $fct->Fct->Colaboracion->Ciclo->vliteral;
        $array[8] = $array[1];
        $array[9] = config(self::CONTACTO_NOMBRE);
        $array[10] = config(self::CONTACTO_CODI);
        $array[11] = $fct->Alumno->fullName;
        $array[12] = $fct->Alumno->dni;
        $array[13] = $fct->horas;
        $array[14] = $fct->Fct->Colaboracion->Ciclo->cliteral;
        $array[15] = $fct->Fct->Centro;
        $array[16] = $fct->Fct->Colaboracion->Centro->direccion;
        $array[17] = $fct->horas;
        $array[18] = $fct->desde."/".$fct->hasta;
        $array[19] = 1;
        $array[20] = $fct->Fct->Colaboracion->Ciclo->llocTreball;
        $array[27] = config(self::CONTACTO_POBLACION);
        $fc1 = new Carbon();
        Carbon::setLocale('ca');
        $array[28] = $fc1->format('d');
        $array[29] = $fc1->format('F');
        $array[30] = $fc1->format('Y');
        $array[31] = $array[1];
        $array[32] = cargo('director')->fullName;

        $array[33] = $array[1];
        $array[34] = config(self::CONTACTO_NOMBRE);
        $array[35] = config(self::CONTACTO_CODI);
        $array[36] = $fct->Alumno->fullName;
        $array[37] = $fct->Alumno->dni;
        $array[38] = $fct->horas;
        $array[39] = $fct->Fct->Colaboracion->Ciclo->vliteral;
        $array[40] = $array[1];
        $array[41] = config(self::CONTACTO_NOMBRE);
        $array[42] = config(self::CONTACTO_CODI);
        $array[43] = $fct->Alumno->fullName;
        $array[44] = $fct->Alumno->dni;
        $array[45] = $fct->horas;
        $array[46] = $fct->Fct->Colaboracion->Ciclo->cliteral;
        $array[47] = $fct->Fct->Centro;
        $array[48] = $fct->Fct->Colaboracion->Centro->direccion;
        $array[49] = $fct->horas;
        $array[50] = $fct->desde."/".$fct->hasta;
        $array[51] = 1;
        $array[52] = $fct->Fct->Colaboracion->Ciclo->llocTreball;
        $array[53] = config(self::CONTACTO_POBLACION);
        $fc1 = new Carbon($data);
        Carbon::setLocale('ca');
        $array[54] = $fc1->format('d');
        $array[55] = $fc1->format('F');
        $array[56] = $fc1->format('Y');
        $array[57] = $array[1];
        $array[58] = cargo('director')->fullName;

        return $array;
    }

    public function printDOC1($fct,$data){
        $id = $fct->id;
        $file = storage_path("tmp/dual$id/doc1".'.pdf');
        if (!file_exists($file)) {
            $pdf = new Pdf('fdf/DOC_1.pdf');
            $pdf->fillform($this->makeArrayPdfDOC1($fct,$data))
                ->saveAs($file);
        }
        return $file;
    }

    /**
     * @param $array
     * @return mixed
     */
    private function makeArrayPdfDOC1($fct,$data)
    {
        $array['Texto3'] = config(self::CONTACTO_NOMBRE);
        $array['Texto5'] = config(self::CONTACTO_CODI);
        $array['Texto6'] = config('contacto.telefono');
        $array['Texto7'] = config('contacto.telefono');
        $array['Texto8'] = config('contacto.direccion');
        $array['Texto9'] = config(self::CONTACTO_POBLACION);
        $array['Texto10'] = config(self::CONTACTO_PROVINCIA);
        $array['Texto11'] = config('contacto.postal');
        $array['Texto4'] = config('contacto.email');
        $array['Texto12'] = cargo('director')->fullName;
        $array['Grupo1'] = self::OPCIÓN_1;
        $array['Texto13'] = $fct->Fct->Colaboracion->Ciclo->vliteral;
        $array['Texto14'] = substr($fct->Fct->Colaboracion->Ciclo->Departament->vliteral,12);
        $array['Grupo2'] = $fct->Fct->Colaboracion->Ciclo->tipo == 1? self::OPCIÓN_1 :'Opción 2';
        $array['Grupo3'] = self::OPCIÓN_1;
        $array['Texto15'] = $fct->Fct->Colaboracion->Centro->Empresa->nombre;
        $array['Texto16'] = $fct->Fct->Colaboracion->Centro->Empresa->cif;
        $array['Texto17'] = $fct->Fct->Colaboracion->Centro->Empresa->telefono;
        $array['Texto18'] = $array['Texto17'];
        $array['Texto19'] = $fct->Fct->Colaboracion->Centro->Empresa->email;
        $array['Texto20'] = $fct->Fct->Colaboracion->Centro->Empresa->direccion;
        $array['Texto21'] = $fct->Fct->Colaboracion->Centro->Empresa->localidad;
        $array['Texto22'] = 'Alacant';
        $array['Texto23'] = $fct->Fct->Colaboracion->Centro->codiPostal;
        $array['Texto24'] = 'Espanya';
        $array['Texto25'] = $fct->Fct->Colaboracion->Centro->direccion;
        $array['Texto26'] = $fct->Fct->Colaboracion->Centro->localidad;
        $array['Texto27'] = 'Alacant';
        $array['Texto28'] = $fct->Fct->Colaboracion->Centro->codiPostal;
        $array['Texto29'] = 'Espanya';
        $array['Text30'] = $fct->Fct->Colaboracion->telefono;
        $array['Text31'] = $array['Text30'];
        $array['Text32'] = $fct->Fct->Instructor->Nombre;
        $array['Text33'] = $fct->Fct->Instructor->dni;
        $array['Text34'] = $fct->Fct->Instructor->email;
        $array['Text36'] = $fct->Fct->Colaboracion->Ciclo->llocTreball;
        $array['Text39'] = $fct->Alumno->apellido1.' '.$fct->Alumno->apellido2 ;
        $array['Text38'] = $fct->Alumno->nombre;
        $array['Text37'] = $fct->Alumno->dni;
        $array['Grupo5'] = $fct->Alumno->sexo == 'H'? self::OPCIÓN_1 :'Opción2';
        $array['Text40'] = $fct->Alumno->fecha_nac;
        $array['Text41'] = $fct->Alumno->domicilio;
        $array['Text42'] = $fct->Alumno->poblacion;
        $array['Text43'] = $fct->Alumno->Provincia->nombre;
        $array['Text44'] = $fct->Alumno->telef1;
        $array['Text45'] = $fct->Alumno->email;
        $array['Grupo4'] = self::OPCIÓN_1;
        $array['Grupo6'] = 'Opción2';
        $array['Text57'] = $fct->beca;
        $array['Text49'] = $fct->desde;
        $array['Text50'] = $fct->hasta;
        $array['Text51'] = $fct->Fct->Colaboracion->Ciclo->llocTreball;
        $array['Text47'] = AuthUser()->fullName;
        $array['Text48'] = AuthUser()->especialitat;
        $array['Casilla de verificación1'] = 'Sí';
        $array['Casilla de verificación2'] = 'Sí';
        $array['Casilla de verificación3'] = 'Sí';
        $array['Casilla de verificación4'] = 'Sí';
        $array['Text52'] = $array['Texto9'];

        $fc1 = new Carbon($data);
        Carbon::setLocale('ca');
        $array['Text53'] = $fc1->format('d');
        $array['Text54'] = $fc1->format('F');
        $array['Text55'] = $fc1->format('Y');
        $array['Text56'] = $array['Texto12'];

        return $array;
    }

    public function printAnexeVII($fct,$data,$num='')
    {
        $id = $fct->id;
        $file = storage_path("tmp/dual$id/anexo_vii".$num.".pdf");
        if (!file_exists($file)) {
            $pdf = new Pdf('fdf/ANEXO_VII.pdf');
            $pdf->fillform($this->makeArrayPdfAnexoVII($fct,$data))
                ->saveAs($file);
        }
        return $file;
    }


    private function makeArrayPdfAnexoVII($fct,$data)
    {
        $array[1] = $fct->Alumno->nia;
        $array[2] = $fct->Alumno->nombre;
        $array[3] = $fct->Alumno->apellido1.' '.$fct->Alumno->apellido2;
        $array[4] = $fct->Alumno->dni;
        $array[5] = $fct->Alumno->email;
        $array[6] = $fct->Alumno->fecha_nac;
        $array[7] = substr($fct->Fct->Colaboracion->Ciclo->Departament->vliteral,12);
        $array[8] = $fct->Fct->Colaboracion->Ciclo->vliteral;
        $array[9] = config(self::CONTACTO_NOMBRE);
        $array[10] = config(self::CONTACTO_CODI);
        $array[11] = AuthUser()->fullName;
        $array['11a'] = $fct->Fct->Centro;
        $array['11b'] = $fct->Fct->Instructor->Nombre;
        $array['11c'] = $fct->Fct->Instructor->dni;
        $fc1 = new Carbon($data);
        $fc2 = new Carbon($data);
        $fc2->addDays(6);
        Carbon::setLocale('ca');
        $array[12] = $fct->Fct->Colaboracion->Ciclo->llocTreball;
        $array[13] = $fc1->format(self::D_M_Y).' a '.$fc2->format(self::D_M_Y);
        $fc1->addDays(7);
        $fc2->addDays(7);
        $array[17] = $fct->Fct->Colaboracion->Ciclo->llocTreball;
        $array[18] = $fc1->format(self::D_M_Y).' a '.$fc2->format(self::D_M_Y);
        $fc1->addDays(7);
        $fc2->addDays(7);
        $array[22] = $fct->Fct->Colaboracion->Ciclo->llocTreball;
        $array[23] = $fc1->format(self::D_M_Y).' a '.$fc2->format(self::D_M_Y);
        $fc1->addDays(7);
        $fc2->addDays(7);
        $array[27] = $fct->Fct->Colaboracion->Ciclo->llocTreball;
        $array[28] = $fc1->format(self::D_M_Y).' a '.$fc2->format(self::D_M_Y);
        $fc1->addDays(7);

        $array[37] = $fct->Fct->Colaboracion->Centro->localidad;
        $array[38] = $fc1->format('d');
        $array[39] = $fc1->format('F');
        $array[40] = $fc1->format('Y');

        $array[41] = $fct->Fct->Instructor->Nombre;
        $array[42] = AuthUser()->fullName;

        return $array;
    }

    public function printAnexeVI()
    {
        try {
            $pdf = new Pdf('fdf/ANEXO_VI.pdf');
            $pdf->fillform($this->makeArrayPdfAnexoVI())
                ->send('dualVI'.AuthUser()->dni.'.pdf');
        } catch (IntranetException $e){
            Alert::warning($e->getMessage());
            return back();
        }

    }



    /**
     * @param $array
     * @return mixed
     */
    private function makeArrayPdfAnexoVI()
    {
        $empresas = Fct::misFcts(null, true)->esDual()->count();
        $duales = AlumnoFct::misDual()->orderBy('idAlumno')->get();
        if (count($duales) == 0) {
            throw new IntranetException('No trobe cap alumne en dual');
        }
        $primero = $duales->first();
        $grupo = $primero->Alumno->Grupo->first();
        $ciclo = $primero->Fct->Colaboracion->Ciclo;
        $alumnos = $grupo->Alumnos->where('sexo', 'H')->count();
        $alumnas = $grupo->Alumnos->where('sexo', 'M')->count();
        $dualH = 0;
        $fctH = 0;
        $OKH = 0;
        $NOH = 0;
        $exeH = 0;
        $dualM = 0;
        $fctM = 0;
        $OKM = 0;
        $NOM = 0;
        $exeM = 0;
        $totalHoras = 0;
        $totalHorasFct = 0;
        $europa = 0;
        $iguales = 0;
        $diferentes = 0;
        foreach ($duales as $index => $dual) {
            if ($dual->Alumno->sexo == 'H') {
                $dualH++;
            } else {
                $dualM++;
            }
            $array[66 + $index * 6] = $index + 1;
            $array[67 + $index * 6] = $dual->Alumno->FullName;
            $array[68 + $index * 6] = $dual->Fct->Colaboracion->Centro->Empresa->nombre;
            $array[69 + $index * 6] = $dual->horas;
            $totalHoras += $dual->horas;
            $fct = AlumnoFct::misFcts($grupo->tutor)
                ->esAval()
                ->where('idAlumno', $dual->idAlumno)
                ->first();
            if ($fct) {
                $array[70 + $index * 6] = $fct->Fct->Colaboracion->Centro->Empresa->nombre;
                $array[71 + $index * 6] = $fct->horas;
                $totalHorasFct += $fct->horas;
                if ($fct->Fct->Colaboracion->Centro->Empresa->europa) {
                    $europa++;
                }
                if ($array[68 + $index * 6] == $array[70 + $index * 6]) {
                    $iguales++;
                } else {
                    $diferentes++;
                }
                if ($fct->Alumno->sexo == 'H') {
                    if ($fct->FCT->asociacion == 2) {
                        $exeH++;
                    } else {
                        $fctH++;
                        if ($fct->calificacion) {
                            $OKH++;
                        } else {
                            $NOH++;
                        }
                    }
                } else {
                    if ($fct->FCT->asociacion == 2) {
                        $exeM++;
                    } else {
                        $fctM++;
                        if ($fct->calificacion) {
                            $OKM++;
                        } else {
                            $NOM++;
                        }
                    }
                }
            }
        }

        $array[0] = 'Anexo_VI';
        $array[1] = config('contacto.nombre');
        $array[2] = config('contacto.codi');
        $array[3] = 'Sí';
        $array[5] = 'Sí';
        $array[7] = AuthUser()->Departamento->literal;
        $array[8] = $ciclo->literal;
        if ($ciclo->tipo == 1) $array[9] = 'Sí'; else $array[10] = 'Sí';
        $array[12] = 'Sí';
        $array[14] = AuthUser()->fullName;
        $array[15] = AuthUser()->Departamento->literal;
        $array[16] = $alumnos;
        $array[17] = $alumnas;
        $array[18] = $dualH;
        $array[19] = $dualM;
        $array[20] = $empresas;
        $array[23] = $dualH;
        $array[24] = $dualM;
        $array[39] = $dualH;
        $array[40] = $dualM;
        $array[41] = $dualH;
        $array[42] = $dualM;
        $array[43] = $fctH;
        $array[44] = $fctM;
        $array[45] = $dualH - $fctH;
        $array[46] = $dualM - $fctM;
        $array[47] = $dualH + $dualM;
        $array[48] = $dualH + $dualM;
        $array[49] = $fctH + $fctM;
        $array[50] = $dualH + $dualM - $fctH - $fctM;
        $array[51] = $fctH;
        $array[52] = $fctM;
        $array[53] = $OKH;
        $array[54] = $OKM;
        $array[55] = $NOH;
        $array[56] = $NOM;
        $array[57] = $exeH;
        $array[58] = $exeM;
        $array[61] = $fctH + $fctM;
        $array[62] = $OKH + $OKM;
        $array[63] = $NOH + $NOM;
        $array[64] = $exeH + $exeM;
        $array[218] = $totalHoras;
        $array[219] = $totalHorasFct;
        $array[226] = $iguales;
        $array[227] = $diferentes;
        $array[230] = $europa;
        $array[236] = config('contacto.poblacion');
        $fc1 = new Carbon();
        Carbon::setLocale('ca');
        $array[237] = $fc1->format('d');
        $array[238] = $fc1->format('F');
        $array[239] = $fc1->format('Y');
        $array[240] = AuthUser()->fullName;
        return $array;
    }


    public function printAnexeXIV()
    {
        try {
            $pdf = new Pdf('fdf/ANEXO_XIV.pdf');
            $pdf->fillform($this->makeArrayPdfAnexoXIV())
                ->send('dualXIV'.AuthUser()->dni.'.pdf');
        } catch (IntranetException $e) {
            Alert::warning($e->getMessage());
            return back();
        }
    }

    /**
     * @param $array
     * @return mixed
     */
    private function makeArrayPdfAnexoXIV()
    {
        $empresas = Fct::misFcts(null, true)->esDual()->count();
        $duales = AlumnoFct::misDual()->orderBy('idAlumno')->get();
        if (count($duales) == 0) {
            throw new IntranetException('No trobe cap alumne en dual');
        }
        $primero = $duales->first();
        $grupo = $primero->Alumno->Grupo->first();
        $ciclo = $primero->Fct->Colaboracion->Ciclo;
        $array['form1[0].Pagina1[0].Interior[0].seccion\.a[0].A_TEXT1[0]'] = config('contacto.nombre');
        $array['form1[0].Pagina1[0].Interior[0].seccion\.a[0].A_TEXT2[0]'] = config('contacto.poblacion');
        $array['form1[0].Pagina1[0].Interior[0].seccion\.a[0].A_TEXT3[0]'] = config('contacto.codi');
        $array['form1[0].Pagina1[0].Interior[0].seccion\.a[0].A_TEXT4[0]'] = AuthUser()->Departamento->literal;
        $array['form1[0].Pagina1[0].Interior[0].seccion\.a[0].A_TEXT5[0]'] = $ciclo->literal;

        $array['form1[0].Pagina1[1].Interior[0].seccion\.a[0].A_TEXT1[0]'] = config('contacto.nombre');
        $array['form1[0].Pagina1[1].Interior[0].seccion\.a[0].A_TEXT2[0]'] = config('contacto.poblacion');
        $array['form1[0].Pagina1[1].Interior[0].seccion\.a[0].A_TEXT3[0]'] = config('contacto.codi');
        $array['form1[0].Pagina1[1].Interior[0].seccion\.a[0].A_TEXT4[0]'] = AuthUser()->Departamento->literal;
        $array['form1[0].Pagina1[1].Interior[0].seccion\.a[0].A_TEXT5[0]'] = $ciclo->literal;


        $array['form1[0].Pagina1[0].Interior[0].seccion\.a[0].A_TEXT6[0]'] = AuthUser()->fullName;
        $array['form1[0].Pagina1[0].Interior[0].seccion\.a[0].A_TEXT7[0]'] = AuthUser()->Departamento->literal;
        $array['form1[0].Pagina1[1].Interior[0].seccion\.a[0].A_TEXT6[0]'] = AuthUser()->fullName;
        $array['form1[0].Pagina1[1].Interior[0].seccion\.a[0].A_TEXT7[0]'] = AuthUser()->Departamento->literal;


        foreach ($duales as $index => $dual) {
            $indice = $index+1;
            $array["form1[0].Pagina1[0].Interior[0].seccion\.b[0].B\.B_DNI[0].B_DNI$indice"."[0]"] = $dual->Alumno->dni;
            $array["form1[0].Pagina1[0].Interior[0].seccion\.b[0].B\.B_ALUMNE[0].B_ALUMNO$indice"."[0]"] = $dual->Alumno->FullName;
            $array["form1[0].Pagina1[0].Interior[0].seccion\.b[0].B\.B_HORAS[0].B_HORAS$indice"."[0]"] = $dual->horas;
            $array["form1[0].Pagina1[0].Interior[0].seccion\.b[0].B\.B_LLOC[0].B_LLOC$indice"."[0]"] = "Programador Web";

            $array["form1[0].Pagina1[1].Interior[0].seccion\.b[0].B\.B_DNI[0].B_DNI$indice"."[0]"] = $dual->Alumno->dni;
            $array["form1[0].Pagina1[1].Interior[0].seccion\.b[0].B\.B_ALUMNE[0].B_ALUMNO$indice"."[0]"] = $dual->Alumno->FullName;
            $array["form1[0].Pagina1[1].Interior[0].seccion\.b[0].B\.B_HORAS[0].B_HORAS$indice"."[0]"] = $dual->horas;
            $array["form1[0].Pagina1[1].Interior[0].seccion\.b[0].B\.B_LLOC[0].B_LLOC$indice"."[0]"] = "Programador Web";
        }

        $fc1 = new Carbon();
        Carbon::setLocale('ca');
        $array["form1[0].Pagina1[0].Interior[0].seccion\.c[0].C_TEXTFIRMA1[0]"] = AuthUser()->fullName;
        $array["form1[0].Pagina1[0].Interior[0].seccion\.c[0].C_MES[0]"] = $fc1->format('F');
        $array["form1[0].Pagina1[0].Interior[0].seccion\.c[0].C_LLOC[0]"] = config('contacto.poblacion');
        $array["form1[0].Pagina1[0].Interior[0].seccion\.c[0].C_DIA[0]"] = $fc1->format('d');
        $array["form1[0].Pagina1[0].Interior[0].seccion\.c[0].C_ANY[0]"] = $fc1->format('Y');

        $array["form1[0].Pagina1[1].Interior[0].seccion\.c[0].C_TEXTFIRMA1[0]"] = AuthUser()->fullName;
        $array["form1[0].Pagina1[1].Interior[0].seccion\.c[0].C_MES[0]"] = $fc1->format('F');
        $array["form1[0].Pagina1[1].Interior[0].seccion\.c[0].C_LLOC[0]"] = config('contacto.poblacion');
        $array["form1[0].Pagina1[1].Interior[0].seccion\.c[0].C_DIA[0]"] = $fc1->format('d');
        $array["form1[0].Pagina1[1].Interior[0].seccion\.c[0].C_ANY[0]"] = $fc1->format('Y');
        return $array;
    }
}
