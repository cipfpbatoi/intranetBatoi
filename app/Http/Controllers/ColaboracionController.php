<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Botones\BotonImg;
use Intranet\Entities\Activity;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Colaboracion;
use Intranet\Http\Requests\ColaboracionRequest;
use Intranet\Http\Traits\Autorizacion;
use Jenssegers\Date\Date;
use mikehaertl\pdftk\Pdf;
use Response;

/**
 * Class ColaboracionController
 * @package Intranet\Http\Controllers
 */
class ColaboracionController extends ModalController
{
    use Autorizacion;
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
    protected $gridFields = [
        'short',
        'Xciclo',
        'puestos',
        'Xestado',
        'localidad',
        'contacto',
        'email',
        'telefono',
        'horari',
        'profesor',
        'ultimo',
        'anotacio'
    ];
    /**
     * @var array
     */
    protected $titulo = [];
    protected $profile = false;
    protected $formFields= [
        'idCentro' => ['type' => 'hidden'],
        'idCiclo' => ['type' => 'hidden'],
        'contacto' => ['type' => 'text'],
        'telefono' => ['type'=>'number'],
        'email' => ['type'=>'email'],
        'puestos' => ['type' => 'text'],
        'estado' => ['type' => 'select'],
        'anotacio' => ['type' => 'textarea'],
    ];


    /**
     * @param Request $request
     * @param null $id
     * @return mixed
     */
    protected function realStore(Request $request, $id = null)
    {
        $elemento = $id ? Colaboracion::findOrFail($id) : new Colaboracion(); //busca si hi ha
        if ($id) {
            $elemento->setRule('idCentro', $elemento->getRule('idCentro').','.$id);
        }
        $this->validateAll($request, $elemento);    // valida les dades
        return $elemento->fillAll($request);        // ompli i guarda
    }

    /**
     *
     */
    public function iniBotones()
    {
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'colaboracion.show',
                [
                    'img'=>'fa-eye-slash',
                    'roles' => [config('roles.rol.practicas'),config('roles.rol.dual')]
                ]
        ));
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'colaboracion.edit',
                [
                    'roles' => [config('roles.rol.practicas')]
                ]
            )
        );


    }

    /**
     * @return mixed
     */
    public function search()
    {
        $this->titulo = ['quien' => AuthUser()->Departamento->literal ];
        $ciclos = Ciclo::select('id')->where('departamento', AuthUser()->departamento)->get()->toArray();
        $colaboraciones = Colaboracion::whereIn('idCiclo', $ciclos)->with('Centro')->get();
        return $colaboraciones->filter(function ($colaboracion) {
            return $colaboracion->Centro->Empresa->concierto;
        });
    }

    public function update(ColaboracionRequest $request, $id)
    {
        $colaboracion = Colaboracion::findOrFail($id);
        $colaboracion->fillAll($request);
        $colaboracion->tutor = authUser()->dni;
        $colaboracion->estado = $request->estado;
        $colaboracion->save();
        if ($request->anotacio != '') {
            Activity::record('book',  $colaboracion, $request->anotacio, null, 'Contacte previ');
        }
        return $this->redirect();
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse

    public function update(Request $request, $id)
    {
        parent::update($request, $id);
        $empresa = Centro::find($request->idCentro)->idEmpresa;
        Session::put('pestana',1);
        return $this->showEmpresa($empresa);
    }
     */




    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */

    public function show($id)
    {
        $elemento = Colaboracion::findOrFail($id);
        return redirect(route('empresa.detalle',$elemento->Centro->idEmpresa));
    }

    public function printAnexeIV($colaboracion)
    {
        $file = storage_path("tmp/dual$colaboracion->id/ANEXO_IV.pdf");
        if (!file_exists($file)) {
            $pdf = new Pdf('fdf/ANEXO_IV.pdf');
            $pdf->fillform($this->makeArrayPdfAnexoIV($colaboracion))->saveAs($file);
        }
        return $file;
    }

    public function printConveni($colaboracion)
    {
        $file = storage_path("tmp/dual$colaboracion->id/Conveni.pdf");
        if (!file_exists($file)) {
            $pdf = new Pdf('fdf/Conveni.pdf');
            $pdf->fillform($this->makeArrayPdfConveni($colaboracion))
                ->saveAs($file);
        }

        return $file;
    }

    protected function makeArrayPdfAnexoIV($colaboracion)
    {
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
        } else {
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



    protected function makeArrayPdfConveni($colaboracion)
    {
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
        $array['este conveni precisa el contingut i abast'] =  $colaboracion->Ciclo->dataSignaturaDual->format('d/m/Y')??'';
        $array['AA'] = $colaboracion->Centro->Empresa->localidad;
        $array['undefined_2'] = $colaboracion->Centro->Empresa->cif;
        $array['Província de'] = 'Alacant';
        $array['CP'] =explode(',',$colaboracion->Centro->direccion)[1]??'03801';

        return $array;
    }

    protected function print($idColaboracion)
    {
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
