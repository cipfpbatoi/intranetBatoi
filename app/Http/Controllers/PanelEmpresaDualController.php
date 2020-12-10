<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Activity;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Fct;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Grupo;
use Intranet\Entities\Ciclo;
use Jenssegers\Date\Date;
use mikehaertl\pdftk\Pdf;
use Response;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Intranet\Botones\BotonImg;
use Illuminate\Support\Facades\Session;

/**
 * Class ColaboracionController
 * @package Intranet\Http\Controllers
 */
class PanelEmpresaDualController extends IntranetController
{
    use traitAutorizar;
    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Colaboracion';
    /**
     * @var array
     */
    protected $gridFields = ['empresa','localidad','contacto','email','telefono'];
    /**
     * @var array
     */
    protected $titulo = [];
    protected $profile = false;
    protected $vista = ['show'=>'colaboracion'];



    /**
     *
     */
    public function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('colaboracion.show',['rol' => config('roles.rol.dual')]));
        $this->panel->setBoton('grid', new BotonImg('colaboracion.print',['img'=>'fa-file-pdf-o','rol' => config('roles.rol.dual')]));
    }

    /**
     * @return mixed
     */
    public function search(){
        $grupo = Grupo::where('tutorDual',AuthUser()->dni)->first();
        $this->titulo = ['quien' => $grupo->Ciclo->ciclo ];
        $colaboraciones = Colaboracion::where('idCiclo',$grupo->idCiclo)->get();
        return $colaboraciones->filter(function ($colaboracion){
            return $colaboracion->Centro->Empresa->dual;
        });
    }

    public function show($id)
    {
        $elemento = Colaboracion::findOrFail($id);
        $contactCol = Activity::mail('Colaboracion')->id($id)->orderBy('created_at')->get();
        $fcts = Fct::where('idColaboracion',$id)->where('asociacion',1)->get();
        $contactFct = Activity::mail('Fct')->ids(hazArray($fcts,'id','id'))->orderBy('created_at')->get();
        $alumnos = [];
        foreach ($fcts as $fct){
            $alumnos = array_merge($alumnos,hazArray($fct->Alumnos,'nia','nia'));
        }
        $contactAl = Activity::mail('Alumno')->ids($alumnos)->orderBy('created_at')->get();
        return view($this->chooseView('show'), compact('elemento','contactCol','contactFct','contactAl','fcts'));
    }

    public function printAnexeIV($colaboracion){
        $file = storage_path("tmp/dual$colaboracion->id/anexo_iv.pdf");
        if (!file_exists($file)) {
            $pdf = new Pdf('fdf/ANEXO_IV.pdf');
            $pdf->fillform($this->makeArrayPdfAnexoIV($colaboracion))->saveAs($file);
        }
        return $file;
    }

    public function printConveni($colaboracion){
        $file = storage_path("tmp/dual$colaboracion->id/conveni.pdf");
        if (!file_exists($file)) {
            $pdf = new Pdf('fdf/Conveni.pdf');
            return $pdf->fillform($this->makeArrayPdfConveni($colaboracion))
                ->saveAs($file);
        }
        return $file;
    }

    protected function makeArrayPdfAnexoIV($colaboracion){
        $array[1] = $colaboracion->Centro->Empresa->nombre;
        $array[2] = $colaboracion->Centro->Empresa->cif;
        $array[3] = $colaboracion->Centro->Empresa->direccion;
        $array[4] = $colaboracion->Centro->Empresa->localidad;
        $array[5] = 'Alacant';
        $array[6] = 'Espanya';
        $array[7] = $colaboracion->Centro->Empresa->codiPostal;
        $array[8] = $colaboracion->Centro->Empresa->telefono;
        $array[9] = $colaboracion->Centro->direccion;
        $array[10] = $colaboracion->Centro->localidad;
        $array[11] = 'Alacant';
        $array[12] = 'Espanya';
        $array[13] = $colaboracion->Centro->codiPostal;
        $array[14] = $colaboracion->Centro->telefono;
        $array[15] = $colaboracion->Centro->Empresa->gerente;
        $array[16] = $colaboracion->Ciclo->vliteral;
        if ($colaboracion->Ciclo->tipo == 1) {
            $array[17] = 'Sí';
        }
        else {
            $array[19] = 'Sí';
        }
        $array[18] = 'Sí';
        $array[21] = substr($colaboracion->Ciclo->Departament->vliteral,12);
        $array[22] = 'Sí';
        $array[24] = config('contacto.nombre');
        $array[25] = config('contacto.codi');
        $array[26] = 'Sí';
        $array[28] = config('contacto.poblacion');
        $array[29] = config('contacto.provincia');
        $array[30] = config('contacto.email');
        $fc1 = new Date();
        Date::setlocale('ca');
        $array[31] = config('contacto.poblacion');
        $array[32] = $fc1->format('d');
        $array[33] = $fc1->format('F');
        $array[34] = $fc1->format('Y');
        $array[35] = $colaboracion->Centro->Empresa->gerente;

        return $array;
    }



    protected function makeArrayPdfConveni($colaboracion){
        $array[1] = $colaboracion->Centro->Empresa->nombre;
        if ($colaboracion->Ciclo->tipo == 1) {
            $array['CORRESPONENT AL CICLE FORMATIU 1'] = 'GRAU MITJA';
            $array['CORRESPONDIENTE AL CICLO FORMATIVO 1'] = 'GRADO MEDIO';
        } else {
            $array['CORRESPONENT AL CICLE FORMATIU 1'] = 'GRAU SUPERIOR';
            $array['CORRESPONDIENTE AL CICLO FORMATIVO 1'] = 'GRADO SUPERIOR';
        }
        $array['CORRESPONENT AL CICLE FORMATIU 2'] = $colaboracion->Ciclo->vliteral;
        $array['CORRESPONDIENTE AL CICLO FORMATIVO 2'] = $colaboracion->Ciclo->cliteral;


        $array['undefined_5'] = $colaboracion->Centro->codiPostal;
        $array[11] =  $colaboracion->Centro->Empresa->gerente;
        $array['undefined_4'] =explode(',',$colaboracion->Centro->direccion)[0];

        $array['acceptar'] = config('contacto.nombre');
        $array['este conveni precisa el contingut i abast'] =  $colaboracion->Ciclo->dataSignaturaDual;
        $array['AA'] = $colaboracion->Centro->Empresa->localidad;
        $array['undefined_2'] = $colaboracion->Centro->Empresa->cif;
        $array['Província de'] = 'Alacant';
        $array['CP'] =explode(',',$colaboracion->Centro->direccion)[1];

        return $array;
    }

    protected function print($idColaboracion){
        $colaboracion = Colaboracion::find($idColaboracion);
        $folder = storage_path("tmp/dual$idColaboracion/");
        $carpeta_autor = $colaboracion->Centro->Empresa->nombre."/010_FaseAutoritzacioConveni/";
        $zip_file = storage_path("tmp/dual_".$colaboracion->Centro->Empresa->nombre.".zip");
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }
        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFile($this->printConveni($colaboracion),$carpeta_autor."CONVENI AMB LEMPRESA COLABORADORA.pdf");
        $zip->addFile($this->printAnexeIV($colaboracion),$carpeta_autor."ANEXO IV DECLARACION RESPONSABLE DE L'EMPRESA COLABORADORA.pdf");
        $zip->addFile($this->printConveni($colaboracion),$carpeta_autor."CONVENI AMB LEMPRESA COLABORADORA.pdf");
        $zip->close();
        $this->deleteDir($folder);

        return response()->download($zip_file);
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


}
