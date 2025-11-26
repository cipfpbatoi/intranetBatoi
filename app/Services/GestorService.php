<?php

namespace Intranet\Services;

use Intranet\Entities\Documento;
use Intranet\Services\Document\DocumentContext;
use Intranet\Services\Document\DocumentResponder;
use Intranet\Services\Document\DocumentResolver;

class GestorService
{
    private $elemento;
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
        if (isset($this->document)) {
            if ($parametres) {
                $this->update($parametres);
            }
        } else {
            $this->document = new Documento($parametres);
            $this->document->curso = curso();
            if (empty($this->document->supervisor)) {
                $this->document->supervisor = authUser()->FullName;
            }
            if (empty($this->document->descripcion)) {
                $this->document->descripcion = 'Registre dia ' . hoy('d-m-Y');
            }
            if ($el = $this->elemento) {
                if (empty($this->document->idDocumento)) {
                    $primaryKey = $el->primaryKey??null;
                    $this->document->idDocumento = $el->id ?? $el->$primaryKey ?? $this->document->tipoDocumento;
                }
                if (empty($this->document->propietario)) {
                    $this->document->propietario = $el->Profesor->FullName ?? '';
                }
                if (empty($this->document->fichero)) {
                    $this->document->fichero = $el->fichero;
                }
            } else {
                if (empty($this->document->propietario)) {
                    $this->document->propietario = authUser()->FullName;
                }
                if (empty($this->document->tags)) {
                    $this->document->tags = 'listado llistat autorizacion autorizacio';
                }
                if (empty($this->document->rol)) {
                    $this->document->rol = 2;
                }
            }
        }
        $this->document->save();
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


    private function update($parametres)
    {
        foreach ($parametres as $key => $valor) {
            $this->document->$key = $valor;
        }
    }
}
