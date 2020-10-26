<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Documento;
use Intranet\Entities\Grupo;
use Intranet\Entities\Horario;
use Intranet\Entities\Profesor;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use mikehaertl\pdftk\Command;
use mikehaertl\pdftk\Pdf;
use Jenssegers\Date\Date;

/**
 * Class DualAlumnoController
 * @package Intranet\Http\Controllers
 */
class DualAlumnoController extends FctAlumnoController
{
    use traitImprimir;

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'AlumnoFct';
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
        $this->panel->setBoton('grid', new BotonImg('dual.informe',['img'=>'fa-file-word-o']));
        $this->panel->setBoton('grid', new BotonImg('dual.pdf.anexe_vii'));
        $this->panel->setBoton('grid', new BotonImg('dual.pdf.anexe_va'));
        $this->panel->setBoton('grid', new BotonImg('dual.pdf.anexe_vb'));
        $this->panel->setBoton('grid', new BotonImg('dual.anexeXIII',['img'=>'fa-file-pdf-o']));
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
     * @param $id
     * @param string $informe
     * @return mixed
     */
    public function informe($fct, $informe='anexe_vii',$stream=true)
    {
        $id = is_object($fct)?$fct->id:$fct;
        $fct = is_object($fct)?$fct:AlumnoFct::findOrFail($id);
        $informe = 'dual.'.$informe;
        $secretario = Profesor::find(config('contacto.secretario'));
        $director = Profesor::find(config('contacto.director'));
        $dades = ['date' => FechaPosterior($fct->hasta),
            'consideracion' => $secretario->sexo === 'H' ? 'En' : 'Na',
            'secretario' => $secretario->FullName,
            'centro' => config('contacto.nombre'),
            'codigo' => config('contacto.codi'),
            'poblacion' => config('contacto.poblacion'),
            'provincia' => config('contacto.provincia'),
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
        if ($documento) return storage_path('app/'.$documento->fichero);
    }

    private function chooseAction($fct,$document,&$zip){
        $ciclo = $fct->Fct->Colaboracion->Ciclo->id;
        $zip_local = $fct->Fct->Centro."/020_FaseFirmaConveni_".$fct->Alumno->dualName."/";
        switch ($document) {
            case 'covid':
                $zip->addFile($this->informe($fct,'covid',false),$zip_local."ConformitatAlumne_Covid19_v20201005.pdf"); break;
            case 'beca':
                $zip->addFile($this->informe($fct,'beca',false),$zip_local."Beca.pdf"); break;
            case 'justAl':
                $zip->addFile($this->informe($fct,'justAl',false),$zip_local."JustificanteEntregaCalendario_a_Alumno.pdf");break;
            case 'justEm':
                $zip->addFile($this->informe($fct,'justEm',false),$zip_local."JustificanteEntregaCalendario_a_Empresa.pdf");break;
            case 'DOC1':
                $zip->addFile($this->printDOC1($fct),$zip_local."DOCUMENTO 1 DATOS BÁSICOS PARA EL PROGRAMA DE FORMACIÓN.pdf");break;
            case 'DOC2':
                $zip->addFile($this->getGestor('DOC2',$ciclo),$zip_local."DOCUMENTO 2 CUADRO HORARIO DEL CICLO EN FP DUAL.odt");break;
            case 'DOC3a' :
                $zip->addFile($this->getGestor('DOC3',curso()),$zip_local."DOCUMENTO 3 CALENDARIO ANUAL CENTRO EMPRESA ".curso().".odt");break;
            case 'DOC3b' :
                $zip->addFile($this->getGestor('DOC3',cursoAnterior()),$zip_local."DOCUMENTO 3 CALENDARIO ANUAL CENTRO EMPRESA ".cursoAnterior().".odt");break;
            case 'DOC4' :
                $zip->addFile($this->printDOC4($fct),$zip_local."DOCUMENTO 4 HORARIO DEL CICLO FORMATIVO EN EL CENTRO.pdf");break;
            case 'DOC5' :
                $zip->addFile($this->getGestor('DOC5',$ciclo),$zip_local."DOCUMENTO 5 PROGRAMA DE FORMACIÓN DE MÓDULOS EN DUAL.odt");break;
            case 'annexii' :
                $zip->addFile($this->printAnexeXII($fct),$zip_local."ANEXO XII CONFORMIDAD DEL ALUMNADO.pdf");break;
        }
    }

    protected function getInforme($id){
        return view('dual.informe',compact('id'));
    }

    protected function putInforme($id,Request $request){
        $input = $request->all();
        $fct = AlumnoFct::findOrFail($id);
        $zip_file = storage_path("tmp/dual_".$fct->Alumno->dualName.".zip");
        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        foreach ($input as $index => $value) {
            if ($index !== '_token') {
                $this->chooseAction($fct, $index, $zip);
            }
        }
        $zip->close();

        return response()->download($zip_file);
    }

    public function printAnexeXII($fct){
        $id = $fct->id;
        $file = storage_path("tmp/dual$id/anexo_xii.pdf");
        if (!file_exists($file)) {
            $pdf = new Pdf('fdf/ANEXO_XII.pdf');
            $pdf->fillform($this->makeArrayPdfAnexoXII($fct))
                ->saveAs($file);
        }
        return $file;
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
    private function makeArrayPdfAnexoXII($fct)
    {
        $array[1] = $fct->Alumno->fullName;
        $array[2] = $fct->Alumno->dni;
        $array[3] = $fct->Fct->Colaboracion->Ciclo->vliteral;
        $array[4] =$fct->Fct->Colaboracion->Ciclo->tipo == 1?'Mitjà':'Superior';
        $array[5] = substr($fct->Fct->Colaboracion->Ciclo->Departament->vliteral,12);
        $array[6] = config('contacto.nombre');
        $array[7] = config('contacto.codi');
        $array[8] = curso();

        $array[9] = $array[1];
        $array[10] = $array[2];
        $array[11] = $fct->Fct->Colaboracion->Ciclo->cliteral;
        $array[12] =$fct->Fct->Colaboracion->Ciclo->tipo == 1?'Medio':'Superior';
        $array[13] = $array[5];
        $array[14] = $array[6];
        $array[15] = $array[7];
        $array[16] = $array[8];
        $fc1 = new Date();
        Date::setlocale('ca');
        $array[17] = config('contacto.poblacion');
        $array[18] = $fc1->format('d');
        $array[19] = $fc1->format('F');
        $array[20] = $fc1->format('Y');
        $array[21] = $array[1];
        $array[23] = $fct->Fct->Centro;
        $array[24] = $fct->Fct->Colaboracion->Centro->direccion;
        $array[26] = '';
        $array[27] = $fct->Fct->Colaboracion->Centro->localidad;
        $array[28] = 'Alacant';
        $array[29] = 'Espanya';
        $array[30] = $array[23];
        $array[31] = $array[24];
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


    public function printAnexeXIII($fct){
        $id = $fct->id;
        $pdf = new Pdf('fdf/ANEXO_XIII.pdf');
        $pdf->fillform($this->makeArrayPdfAnexoXIII($fct))
            ->send("dualXIII_$id".'.pdf');
        return $this->redirect();
    }


    private function makeArrayPdfAnexoXIII($id)
    {
        $array[1] = Profesor::find(config('contacto.secretario'))->fullName;
        $array[2] = config('contacto.nombre');
        $array[3] = config('contacto.codi');
        $array[4] = $fct->Alumno->fullName;
        $array[5] = $fct->Alumno->dni;
        $array[6] = $fct->horas;
        $array[7] = $fct->Fct->Colaboracion->Ciclo->vliteral;
        $array[8] = $array[1];
        $array[9] = config('contacto.nombre');
        $array[10] = config('contacto.codi');
        $array[11] = $fct->Alumno->fullName;
        $array[12] = $fct->Alumno->dni;
        $array[13] = $fct->horas;
        $array[14] = $fct->Fct->Colaboracion->Ciclo->cliteral;
        $array[15] = $fct->Fct->Centro;
        $array[16] = $fct->Fct->Colaboracion->Centro->direccion;
        $array[17] = $fct->horas;
        $array[18] = $fct->desde."/".$fct->hasta;
        $array[19] = 1;
        $array[20] = 'Dissenyador web';
        $array[27] = config('contacto.poblacion');
        $fc1 = new Date();
        Date::setlocale('ca');
        $array[28] = $fc1->format('d');
        $array[29] = $fc1->format('F');
        $array[30] = $fc1->format('Y');
        $array[31] = $array[1];
        $array[32] = Profesor::find(config('contacto.director'))->fullName;

        $array[33] = $array[1];
        $array[34] = config('contacto.nombre');
        $array[35] = config('contacto.codi');
        $array[36] = $fct->Alumno->fullName;
        $array[37] = $fct->Alumno->dni;
        $array[38] = $fct->horas;
        $array[39] = $fct->Fct->Colaboracion->Ciclo->vliteral;
        $array[40] = $array[1];
        $array[41] = config('contacto.nombre');
        $array[42] = config('contacto.codi');
        $array[43] = $fct->Alumno->fullName;
        $array[44] = $fct->Alumno->dni;
        $array[45] = $fct->horas;
        $array[46] = $fct->Fct->Colaboracion->Ciclo->cliteral;
        $array[47] = $fct->Fct->Centro;
        $array[48] = $fct->Fct->Colaboracion->Centro->direccion;
        $array[49] = $fct->horas;
        $array[50] = $fct->desde."/".$fct->hasta;
        $array[51] = 1;
        //$array[52] = 'Dissenyador web';
        $array[53] = config('contacto.poblacion');
        $fc1 = new Date();
        Date::setlocale('ca');
        $array[54] = $fc1->format('d');
        $array[55] = $fc1->format('F');
        $array[56] = $fc1->format('Y');
        $array[57] = $array[1];
        $array[58] = Profesor::find(config('contacto.director'))->fullName;

        return $array;
    }

    public function printDOC1($fct){
        $id = $fct->id;
        $file = storage_path("tmp/dual$id/doc1".'.pdf');
        if (!file_exists($file)) {
            $pdf = new Pdf('fdf/DOC_1.pdf');
            $pdf->fillform($this->makeArrayPdfDOC1($fct))
                ->saveAs($file);
        }
        return $file;
    }

    /**
     * @param $array
     * @return mixed
     */
    private function makeArrayPdfDOC1($fct)
    {
        $array['Texto3'] = config('contacto.nombre');
        $array['Texto5'] = config('contacto.codi');
        $array['Texto6'] = config('contacto.telefono');
        $array['Texto7'] = config('contacto.telefono');
        $array['Texto8'] = config('contacto.direccion');
        $array['Texto9'] = config('contacto.poblacion');
        $array['Texto10'] = config('contacto.provincia');
        $array['Texto11'] = config('contacto.postal');
        $array['Texto4'] = config('contacto.email');
        $array['Texto12'] = Profesor::find(config('contacto.director'))->fullName;
        $array['Grupo1'] = 'Opción1';
        $array['Texto13'] = $fct->Fct->Colaboracion->Ciclo->vliteral;
        $array['Texto14'] = substr($fct->Fct->Colaboracion->Ciclo->Departament->vliteral,12);
        $array['Grupo2'] = $fct->Fct->Colaboracion->Ciclo->tipo == 1?'Opción1':'Opción 2';
        $array['Grupo3'] = 'Opción1';
        $array['Texto15'] = $fct->Fct->Colaboracion->Centro->Empresa->nombre;
        $array['Texto16'] = $fct->Fct->Colaboracion->Centro->Empresa->cif;
        $array['Texto17'] = $fct->Fct->Colaboracion->Centro->Empresa->telefono;
        $array['Texto18'] = $array['Texto17'];
        $array['Texto19'] = $fct->Fct->Colaboracion->Centro->Empresa->email;

        $array['Texto20'] = $fct->Fct->Colaboracion->Centro->Empresa->direccion;
        $array['Texto21'] = $fct->Fct->Colaboracion->Centro->Empresa->localidad;
        $array['Texto22'] = 'Alacant';
        $array['Texto23'] = '';
        $array['Texto24'] = 'Espanya';
        $array['Texto25'] = $fct->Fct->Colaboracion->Centro->direccion;
        $array['Texto26'] = $fct->Fct->Colaboracion->Centro->localidad;
        $array['Texto27'] = 'Alacant';
        $array['Texto28'] = '';
        $array['Texto29'] = 'Espanya';
        $array['Text30'] = $fct->Fct->Colaboracion->telefono;
        $array['Text31'] = $array['Text30'];
        $array['Text32'] = $fct->Fct->Instructor->Nombre;
        $array['Text33'] = $fct->Fct->Instructor->dni;
        $array['Text34'] = $fct->Fct->Instructor->email;
        $array['Text36'] = $array['Texto13'];
        $array['Text39'] = $fct->Alumno->apellido1.' '.$fct->Alumno->apellido2 ;
        $array['Text38'] = $fct->Alumno->nombre;
        $array['Text37'] = $fct->Alumno->dni;
        $array['Grupo5'] = $fct->Alumno->sexo == 'H'?'Opción1':'Opción2';
        $array['Text40'] = $fct->Alumno->fecha_nac;
        $array['Text41'] = $fct->Alumno->domicilio;
        $array['Text42'] = $fct->Alumno->poblacion;
        $array['Text43'] = $fct->Alumno->Provincia->nombre;
        $array['Text44'] = $fct->Alumno->telef1;
        $array['Text45'] = $fct->Alumno->email;
        $array['Text49'] = $fct->desde;
        $array['Text50'] = $fct->hasta;
        //$array['Text51'] = "Programador Web";
        $array['Text47'] = AuthUser()->fullName;
        //$array['Text48'] = "Professor d'educació secundària";
        $array['Casilla de verificación1'] = 'Sí';
        $array['Casilla de verificación2'] = 'Sí';
        $array['Casilla de verificación3'] = 'Sí';
        $array['Casilla de verificación4'] = 'Sí';
        $array['Text52'] = $array['Texto9'];

        $fc1 = new Date();
        Date::setlocale('ca');
        $array['Text53'] = $fc1->format('d');
        $array['Text54'] = $fc1->format('F');
        $array['Text55'] = $fc1->format('Y');
        $array['Text56'] = $array['Texto12'];

        return $array;
    }
    
    
} 