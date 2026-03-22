<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\AlumnoFct\AlumnoFctService;
use Intranet\Application\Documento\DocumentoLifecycleService;
use Intranet\Application\Documento\DocumentoPersistenceService;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Controllers\Core\IntranetController;
use Illuminate\Http\Request;

use Intranet\Entities\Adjunto;
use Intranet\Entities\Documento;
use Intranet\Presentation\Crud\DocumentoCrudSchema;
use Intranet\Services\General\GestorService;
use Intranet\Services\UI\FormBuilder;
use Illuminate\Support\Facades\Session;

/**
 * Controlador de gestió documental comuna.
 */
class DocumentoController extends IntranetController
{
    private ?DocumentoLifecycleService $documentoLifecycleService = null;
    private ?DocumentoPersistenceService $documentoPersistenceService = null;

    protected $model = 'Documento';
    protected $formFields = DocumentoCrudSchema::FORM_FIELDS;

    public function __construct(
        ?DocumentoLifecycleService $documentoLifecycleService = null,
        ?DocumentoPersistenceService $documentoPersistenceService = null
    )
    {
        parent::__construct();
        $this->documentoLifecycleService = $documentoLifecycleService;
        $this->documentoPersistenceService = $documentoPersistenceService;
    }

    private function documentos(): DocumentoLifecycleService
    {
        if ($this->documentoLifecycleService === null) {
            $this->documentoLifecycleService = app(DocumentoLifecycleService::class);
        }

        return $this->documentoLifecycleService;
    }

    private function persistence(): DocumentoPersistenceService
    {
        if ($this->documentoPersistenceService === null) {
            $this->documentoPersistenceService = app(DocumentoPersistenceService::class);
        }

        return $this->documentoPersistenceService;
    }


    protected function redirect()
    {
        if (Session::get('redirect')) {
            return redirect()->action(Session::get('redirect'));
        }

        return redirect()->route('documento.index');
    }


 

    public function store(Request $request, $fct = null)
    {
        $this->authorize('create', Documento::class);
        $this->persistence()->storeFromRequest($request);

        return $this->redirect();
    }

    public function edit($id = null)
    {
        $elemento = Documento::findOrFail($id);
        $this->authorize('update', $elemento);
        $formulario = new FormBuilder($elemento, DocumentoCrudSchema::editFormFields((bool) $elemento->enlace));
        $modelo = $this->model;
        return view($this->chooseView('edit'), compact('formulario', 'modelo'));
    }
    
    public function show($id)
    {
        $documento = Documento::findOrFail($id);
        $this->authorize('view', $documento);
        $gestor = new GestorService(null, $documento);
        return $gestor->render();
    }

    public function destroy($id)
    {
        $borrar = Documento::findOrFail($id);
        $this->authorize('delete', $borrar);
        $this->documentos()->delete($borrar);
        return $this->redirect();
    }

    public function readFile($name)
    {
        $adjunto = Adjunto::where('name', $name)->first();
        if (!$adjunto) {
            return back();
        }

        if (!is_file($adjunto->path)) {
            return back()->withErrors("No s'ha trobat el fitxer adjunt.");
        }

        return response()->file($adjunto->path);
    }

    /**
     * Mostra un adjunt resolt per identificador intern.
     *
     * @param int|string $id
     */
    public function showAttached($id)
    {
        $adjunto = Adjunto::find($id);
        if (!$adjunto) {
            return back();
        }

        if (!is_file($adjunto->path)) {
            return back()->withErrors("No s'ha trobat el fitxer adjunt.");
        }

        return response()->file($adjunto->path);
    }

    

}
