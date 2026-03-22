<?php

declare(strict_types=1);

namespace Intranet\Http\Controllers;

use Intranet\Application\AlumnoFct\AlumnoFctService;
use Intranet\Application\Documento\DocumentoFormService;
use Intranet\Application\Documento\DocumentoPersistenceService;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Entities\Documento;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Controllers\Core\IntranetController;
use Intranet\Http\Requests\DocumentoStoreRequest;
use Intranet\Presentation\Crud\DocumentoCrudSchema;
use Intranet\Services\Document\CreateOrUpdateDocumentAction;
use Intranet\Services\UI\FormBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Gestiona el formulari documental específic del projecte associat a FCT.
 */
class FctProjecteDocumentoController extends IntranetController
{
    private ?GrupoService $grupoService = null;
    private ?AlumnoFctService $alumnoFctService = null;
    private ?DocumentoFormService $documentoFormService = null;
    private ?DocumentoPersistenceService $documentoPersistenceService = null;

    protected $model = 'Documento';

    public function __construct(
        ?GrupoService $grupoService = null,
        ?AlumnoFctService $alumnoFctService = null,
        ?DocumentoFormService $documentoFormService = null,
        ?DocumentoPersistenceService $documentoPersistenceService = null
    ) {
        parent::__construct();
        $this->grupoService = $grupoService;
        $this->alumnoFctService = $alumnoFctService;
        $this->documentoFormService = $documentoFormService;
        $this->documentoPersistenceService = $documentoPersistenceService;
    }

    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
    }

    private function alumnoFcts(): AlumnoFctService
    {
        if ($this->alumnoFctService === null) {
            $this->alumnoFctService = app(AlumnoFctService::class);
        }

        return $this->alumnoFctService;
    }

    private function forms(): DocumentoFormService
    {
        if ($this->documentoFormService === null) {
            $this->documentoFormService = app(DocumentoFormService::class);
        }

        return $this->documentoFormService;
    }

    private function persistence(): DocumentoPersistenceService
    {
        if ($this->documentoPersistenceService === null) {
            $this->documentoPersistenceService = app(DocumentoPersistenceService::class);
        }

        return $this->documentoPersistenceService;
    }

    /**
     * Mostra el formulari de document de projecte associat a una FCT avaluable.
     *
     * @throws NotFoundDomainException
     */
    public function create($default = [])
    {
        $this->authorize('create', Documento::class);
        $idFct = request()->route('fct');

        $fct = $this->alumnoFcts()->findOrFail((int) $idFct);
        $grupoTutor = $this->grupos()->firstByTutor(AuthUser()->dni);
        $ciclo = $grupoTutor?->Ciclo?->ciclo ?? '';

        $elemento = (new CreateOrUpdateDocumentAction())->build(
            $this->forms()->projectDefaults($fct, $ciclo, AuthUser()->FullName)
        );
        $formulario = new FormBuilder($elemento, DocumentoCrudSchema::projectFormFields());
        $modelo = $this->model;

        Session::put('redirect', 'PanelFctAvalController@index');

        return view($this->chooseView('create'), compact('formulario', 'modelo'));
    }

    /**
     * Persistix el document de projecte i actualitza la nota del projecte si cal.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Documento::class);
        $this->validate($request, (new DocumentoStoreRequest())->rules());
        $fct = $request->route('fct');

        if ($fct !== null && $request->filled('nota')) {
            $this->forms()->updateNota($this->alumnoFcts(), (int) $fct, $request->nota);
            if ((float) $request->nota < 5) {
                return $this->redirect();
            }
        }

        $this->persistence()->storeFromRequest($request);

        return $this->redirect();
    }
}
