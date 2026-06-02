<?php

declare(strict_types=1);

namespace Intranet\Http\Controllers;

use Intranet\Application\Documento\DocumentoFormService;
use Intranet\Application\Documento\DocumentoPersistenceService;
use Intranet\Application\Documento\FctQualitatUploadService;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Controllers\Core\IntranetController;
use Intranet\Entities\Adjunto;
use Intranet\Entities\Documento;
use Intranet\Presentation\Crud\DocumentoCrudSchema;
use Intranet\Services\Document\CreateOrUpdateDocumentAction;
use Intranet\Services\UI\FormBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Gestiona el flux específic de qualitat documental d'FCT.
 */
class FctQualitatDocumentoController extends IntranetController
{
    private ?GrupoService $grupoService = null;
    private ?ProfesorService $profesorService = null;
    private ?DocumentoFormService $documentoFormService = null;
    private ?DocumentoPersistenceService $documentoPersistenceService = null;
    private ?FctQualitatUploadService $fctQualitatUploadService = null;

    protected $model = 'Documento';

    public function __construct(
        ?GrupoService $grupoService = null,
        ?ProfesorService $profesorService = null,
        ?DocumentoFormService $documentoFormService = null,
        ?DocumentoPersistenceService $documentoPersistenceService = null,
        ?FctQualitatUploadService $fctQualitatUploadService = null
    ) {
        parent::__construct();
        $this->grupoService = $grupoService;
        $this->profesorService = $profesorService;
        $this->documentoFormService = $documentoFormService;
        $this->documentoPersistenceService = $documentoPersistenceService;
        $this->fctQualitatUploadService = $fctQualitatUploadService;
    }

    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
    }

    private function profesores(): ProfesorService
    {
        if ($this->profesorService === null) {
            $this->profesorService = app(ProfesorService::class);
        }

        return $this->profesorService;
    }

    private function forms(): DocumentoFormService
    {
        if ($this->documentoFormService === null) {
            $this->documentoFormService = app(DocumentoFormService::class);
        }

        return $this->documentoFormService;
    }

    private function qualitatUploads(): FctQualitatUploadService
    {
        if ($this->fctQualitatUploadService === null) {
            $this->fctQualitatUploadService = app(FctQualitatUploadService::class);
        }

        return $this->fctQualitatUploadService;
    }

    private function persistence(): DocumentoPersistenceService
    {
        if ($this->documentoPersistenceService === null) {
            $this->documentoPersistenceService = app(DocumentoPersistenceService::class);
        }

        return $this->documentoPersistenceService;
    }

    /**
     * Mostra el formulari de creació de documentació de qualitat FCT.
     *
     * @param array<string, mixed> $default
     * @throws NotFoundDomainException
     */
    public function create($default = [])
    {
        $this->authorize('create', Documento::class);

        $grupo = $this->grupos()->firstByTutor(AuthUser()->dni);
        if (!$grupo) {
            throw new NotFoundDomainException(
                'No hi ha grup de tutoria assignat',
                ['profesor_id' => AuthUser()->dni]
            );
        }

        $elemento = (new CreateOrUpdateDocumentAction())->build(
            $this->forms()->qualitatDefaults($grupo, AuthUser()->FullName)
        );
        $formulario = new FormBuilder($elemento, DocumentoCrudSchema::qualitatFormFields());
        $modelo = $this->model;

        Session::put('redirect', 'FctController@index');

        return view($this->chooseView('create'), compact('formulario', 'modelo'));
    }

    /**
     * Consolida en ZIP la documentació de qualitat FCT d'un tutor.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     */
    public function upload($id)
    {
        $this->authorize('create', Documento::class);

        $profesor = $this->profesores()->findOrFail((string) $id);
        $documents = Adjunto::where('route', "profesor/$id")->get();
        $grupo = $this->grupos()->firstByTutor((string) $id);

        if (!$grupo) {
            throw new NotFoundDomainException('No hi ha grup de tutoria assignat', ['profesor_id' => $id]);
        }

        $documento = $this->qualitatUploads()->createZipDocument($profesor, $grupo, $documents);

        if ($documento !== null) {
            return redirect()->route('alumnofct.index');
        }

        return back();
    }

    /**
     * Persistix el registre documental de qualitat FCT.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Documento::class);
        $this->persistence()->storeFromRequest($request);

        return $this->redirect();
    }
}
