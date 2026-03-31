<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Seguimiento\SeguimientoService;
use Intranet\Http\Controllers\Core\ModalController;

use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Activity;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Colaboracion;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Requests\ColaboracionRequest;
use Intranet\Http\Traits\Autorizacion;
use Intranet\Presentation\Crud\ColaboracionCrudSchema;
use Intranet\Services\Document\PdfFormService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
    protected $gridFields = ColaboracionCrudSchema::GRID_FIELDS;
    /**
     * @var array
     */
    protected $titulo = [];
    protected $profile = false;
    protected $formFields = ColaboracionCrudSchema::FORM_FIELDS;

    /**
     * Inicialitza el controlador modal amb una vista pròpia per al llistat departamental.
     */
    public function __construct(private readonly SeguimientoService $seguimientoService)
    {
        parent::__construct();
        $this->panel = new \Intranet\UI\Panels\Panel(
            $this->model,
            $this->gridFields,
            'colaboracion.departamento',
            true,
            $this->parametresVista
        );
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
                    'roles' => [config('roles.rol.tutor') ]
                ]
        ));
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'colaboracion.edit',
                [
                    'roles' => [config('roles.rol.tutor')]
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
        $ciclos = Ciclo::query()
            ->where('departamento', AuthUser()->departamento)
            ->pluck('id')
            ->all();

        return Colaboracion::query()
            ->whereIn('idCiclo', $ciclos)
            ->with(['Centro.Empresa', 'Ciclo', 'Propietario'])
            ->get();
    }

    /**
     * Actualitza una col·laboració des del formulari específic de panell.
     *
     * Manté el flux legacy: escriu estat/tutor i registra anotació en Activity.
     *
     * @param ColaboracionRequest $request
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ColaboracionRequest $request, $id)
    {
        $this->persist($request, $id);
        $colaboracion = $this->findModelOrFail(
            Colaboracion::class,
            $id,
            'Col·laboració no trobada',
            ['colaboracion_id' => $id]
        );
        $colaboracion->tutor = authUser()->dni;
        $colaboracion->estado = $request->estado;
        $colaboracion->save();
        if ($request->anotacio != '') {
            $activity = Activity::record('book',  $colaboracion, $request->anotacio, null, 'Contacte previ');
            $this->seguimientoService->record(
                $colaboracion,
                'book',
                'Contacte previ',
                $request->anotacio,
                ['source' => 'activities', 'activity_id' => $activity->id]
            );
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
     * Mostra el detall de la col·laboració.
     *
     * Manté l'accés directe per URL i, quan la petició arriba en mode modal,
     * retorna només el parcial del contingut per incrustar-lo al diàleg.
     *
     * @param Request $request
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        $elemento = $this->findModelOrFail(
            Colaboracion::class,
            $id,
            'Col·laboració no trobada',
            ['colaboracion_id' => $id]
        );

        $elemento->loadMissing([
            'Centro.Empresa',
            'Centro.instructores',
            'Ciclo',
            'Propietario',
        ]);

        $ultimContacte = $this->seguimientoService->latestActivityForColaboracion($elemento->id);
        $returnUrl = $request->filled('return_to')
            ? (string) $request->query('return_to')
            : url()->previous();

        if ($request->boolean('modal') || $request->ajax()) {
            return view('colaboracion.partials.show', compact('elemento', 'ultimContacte'));
        }

        return view('colaboracion.show', compact('elemento', 'ultimContacte', 'returnUrl'));
    }

    public function printAnexeIV($colaboracion)
    {
        $file = storage_path("tmp/dual$colaboracion->id/ANEXO_IV.pdf");
        if (!file_exists($file)) {
            app(PdfFormService::class)->fillAndSave('fdf/ANEXO_IV.pdf', $this->makeArrayPdfAnexoIV($colaboracion), $file);
        }
        return $file;
    }

    public function printConveni($colaboracion)
    {
        $file = storage_path("tmp/dual$colaboracion->id/Conveni.pdf");
        if (!file_exists($file)) {
            app(PdfFormService::class)->fillAndSave('fdf/Conveni.pdf', $this->makeArrayPdfConveni($colaboracion), $file);
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
        $fc1 = new Carbon();
        Carbon::setLocale('ca');
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
