<?php

namespace Intranet\Services;

use Intranet\Entities\Documento;
use Intranet\Services\Document\CreateOrUpdateDocumentAction;
use Intranet\Services\Document\DocumentContext;
use Intranet\Services\Document\DocumentResponder;
use Intranet\Services\Document\DocumentResolver;

/**
 * Servei GestorService.
 */
class GestorService
{
    /** @var mixed */
    private $elemento;
    /** @var mixed */
    private $document;
    private DocumentContext $context;
    private DocumentResponder $responder;

    public function __construct($elemento = null, $documento = null, ?DocumentResolver $resolver = null, ?DocumentResponder $responder = null)
    {
        $this->elemento = $elemento;
        $resolver = $resolver ?? new DocumentResolver();
        $this->context = $resolver->resolve($elemento, $documento);
        $this->document = $this->context->document();
        $this->responder = $responder ?? new DocumentResponder();
    }

    public function save($parametres = null)
    {
        $action = new CreateOrUpdateDocumentAction();
        $paramsArray = $parametres ?? [];

        if ($parametres || !$this->document) {
            $this->document = $action->fromArray($paramsArray, $this->document, $this->elemento);
        } else {
            $this->document->save();
        }

        return $this->document->id;
    }

    public function render()
    {
        return $this->responder->respond($this->context);
    }

    public static function saveDocument($filePath, $tags, $descripcion = null, $supervisor = null)
    {
        return Documento::create([
            'fichero' => $filePath,
            'tags' => $tags,
            'curso' => curso(),
            'descripcion' => $descripcion ?? 'Registre dia ' . hoy('d-m-Y'),
            'propietario' => $supervisor ?? authUser()->FullName,
            'supervisor' => $supervisor ?? authUser()->FullName,
            'rol' => 2,
        ]);
    }


}
