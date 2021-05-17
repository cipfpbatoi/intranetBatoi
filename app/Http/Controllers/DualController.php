<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Documento;
use Intranet\Entities\Dual;
use Intranet\Entities\Grupo;
use Intranet\Entities\Horario;
use Intranet\Entities\Profesor;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\Http\Requests\DualRequest;
use mikehaertl\pdftk\Command;
use mikehaertl\pdftk\Pdf;
use Jenssegers\Date\Date;

/**
 * Class DualAlumnoController
 * @package Intranet\Http\Controllers
 */
class DualController extends ModalController
{
    use traitImprimir;

    const CONTACTO_DIRECTOR = 'contacto.director';
    const CONTACTO_NOMBRE = 'contacto.nombre';
    const CONTACTO_CODI = 'contacto.codi';
    const CONTACTO_POBLACION = 'contacto.poblacion';
    const CONTACTO_PROVINCIA = 'contacto.provincia';
    const OPCIÓN_1 = 'Opción1';
    const D_M_Y = "d/m/y";


    /**
     * @var string
     */
    protected $model = 'Dual';
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

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('dual.delete'));
        $this->panel->setBoton('grid', new BotonImg('dual.edit'));
        $this->panel->setBoton('grid', new BotonImg('dual.informe',['img'=>'fa-file-zip-o']));
        $this->panel->setBoton('index', new BotonBasico("dual.create", ['class' => 'btn-info']));
        $this->panel->setBoton('index', new BotonBasico("dual.anexeVI", ['class' => 'btn-info','id' => 'anexoVI']));
        $this->panel->setBoton('index', new BotonBasico("dual.anexeXIV", ['class' => 'btn-info','id' => 'anexoXIV']));

        Session::put('redirect', 'DualAlumnoController@index');
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
    public function store(DualRequest $request)
    {
        DB::transaction(function() use ($request) {
            $idAlumno = $request['idAlumno'];
            $elemento = new Dual();
            $elemento->fillAll($request);
            $elemento->Alumnos()->attach($idAlumno, ['desde' => FechaInglesa($request->desde), 'hasta' => FechaInglesa($request->hasta), 'horas' => $request->horas, 'beca' => $request->beca]);
        });
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
        $secretario = Profesor::find(config('contacto.secretario'));
        $director = Profesor::find(config(self::CONTACTO_DIRECTOR));
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
        if ($stream){
            return $pdf->stream();
        } else {
            $file = storage_path("tmp/dual$id/$informe".'.pdf');
            if (!file_exists($file)){
                $pdf->save($file);
            }
            return $file;
        }
    }





    protected function getGestor($doc,$ciclo){
        $documento = Documento::where('tags',"$doc,$ciclo")->where('tipoDocumento','Dual')->first();
        if ($documento) {
            return storage_path('app/' . $documento->fichero);
        }
    }



    private function chooseAction($fct,$document,&$zip,$data){
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
                $zip->addFile($this->informe($fct,'anexo_v',false,$data),$carpeta_firma."Anexo V Prevención Riesgos Laborales FP Dual.pdf");break;
            case 'annexevii':
                if ($data != null ) {
                    $zip->addFile($this->printAnexeVII($fct, $data), $carpeta_formacio . "ANEXO_VII.pdf");
                } else {
                    $fc1 = new Date($fct->desde);
                    $fc2 = new Date($fct->hasta);
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
            default : break;
        }
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

    public function printAnexeXII($fct,$data){
        $id = $fct->id;
        $file = storage_path("tmp/dual$id/anexo_xii.pdf");
        if (!file_exists($file)) {
            $pdf = new Pdf('fdf/ANEXO_XII.pdf');
            $pdf->fillform($this->makeArrayPdfAnexoXII($fct,$data))
                ->saveAs($file);
        }
        return $file;
    }

    public function printAnexeIV($fct,$data){
        $id = $fct->id;
        $file = storage_path("tmp/dual$id/anexo_iv.pdf");
        if (!file_exists($file)) {
            $pdf = new Pdf('fdf/ANEXO_IV.pdf');
            $pdf->fillform($this->makeArrayPdfAnexoIV($fct,$data))
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
        $fc1 = new Date($data);
        Date::setlocale('ca');
        $array[31] = config(self::CONTACTO_POBLACION);
        $array[32] = $fc1->format('d');
        $array[33] = $fc1->format('F');
        $array[34] = $fc1->format('Y');
        $array[35] = $fct->Fct->Colaboracion->Centro->Empresa->gerente;

        return $array;
    }

    public function printConveni($fct,$data){
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

        dd($fct->Fct->Colaboracion->Ciclo->dataSignaturaDual->format('d/m/Y')??'');
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
            $horario = Horario::HorarioGrupo($grupo->codigo);
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
        $fc1 = new Date($data);
        Date::setlocale('ca');
        $array[17] = config(self::CONTACTO_POBLACION);
        $array[18] = $fc1->format('d');
        $array[19] = $fc1->format('F');
        $array[20] = $fc1->format('Y');
        $array[21] = $array[1];
        $array[22] = $fct->Fct->Colaboracion->Centro->Empresa->gerente;
        $array[23] = $fct->Fct->Centro;
        $array[24] = explode(',',$fct->Fct->Colaboracion->Centro->direccion)[0];
        $array[25] = explode(',',$fct->Fct->Colaboracion->Centro->direccion)[1];
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
        $array[42] = $fct->Fct->Colaboracion->Centro->Empresa->gerente;

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


    private function makeArrayPdfAnexoXIII($fct,$data)
    {
        $array[1] = Profesor::find(config('contacto.secretario'))->fullName;
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
        $fc1 = new Date();
        Date::setlocale('ca');
        $array[28] = $fc1->format('d');
        $array[29] = $fc1->format('F');
        $array[30] = $fc1->format('Y');
        $array[31] = $array[1];
        $array[32] = Profesor::find(config(self::CONTACTO_DIRECTOR))->fullName;

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
        $fc1 = new Date($data);
        Date::setlocale('ca');
        $array[54] = $fc1->format('d');
        $array[55] = $fc1->format('F');
        $array[56] = $fc1->format('Y');
        $array[57] = $array[1];
        $array[58] = Profesor::find(config(self::CONTACTO_DIRECTOR))->fullName;

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
        $array['Texto12'] = Profesor::find(config(self::CONTACTO_DIRECTOR))->fullName;
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

        $fc1 = new Date($data);
        Date::setlocale('ca');
        $array['Text53'] = $fc1->format('d');
        $array['Text54'] = $fc1->format('F');
        $array['Text55'] = $fc1->format('Y');
        $array['Text56'] = $array['Texto12'];

        return $array;
    }

    public function printAnexeVII($fct,$data,$num=''){
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
        $fc1 = new Date($data);
        $fc2 = new Date($data);
        $fc2->addDays(6);
        Date::setlocale('ca');
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
    
    
} 