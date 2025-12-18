<?php

namespace Intranet\Services\Document;

use Illuminate\Http\Request;
use Intranet\Entities\Documento;

/**
 * Servei CreateOrUpdateDocumentAction.
 */
class CreateOrUpdateDocumentAction
{
    public function fromRequest(Request $request, array $overrides = [], ?Documento $document = null, $elemento = null): Documento
    {
        $payload = array_merge($request->all(), $overrides);
        $payload = $this->applyDefaults($payload, $document, $elemento);

        $idDocumento = $payload['idDocumento'] ?? null;
        $enrichedRequest = $request->duplicate($payload, $request->files->all());

        $document = $document ?? new Documento();
        $document->fillAll($enrichedRequest);

        if ($idDocumento !== null) {
            $document->idDocumento = $idDocumento;
            $document->save();
        }

        return $document;
    }

    public function fromArray(array $data, ?Documento $document = null, $elemento = null): Documento
    {
        $payload = $this->applyDefaults($data, $document, $elemento);
        $document = $document ?? new Documento();

        foreach ($payload as $key => $value) {
            $document->$key = $value;
        }

        $document->save();

        return $document;
    }

    public function build(array $data, ?Documento $document = null, $elemento = null): Documento
    {
        $payload = $this->applyDefaults($data, $document, $elemento);
        $document = $document ?? new Documento();

        foreach ($payload as $key => $value) {
            $document->$key = $value;
        }

        return $document;
    }

    private function applyDefaults(array $data, ?Documento $document, $elemento = null): array
    {
        $payload = $data;

        $payload['curso'] = $this->firstAvailable($data, $document, 'curso') ?? curso();
        $payload['supervisor'] = $this->firstAvailable($data, $document, 'supervisor') ?? authUser()->FullName;
        $payload['descripcion'] = $this->firstAvailable($data, $document, 'descripcion') ?? 'Registre dia ' . hoy('d-m-Y');

        if ($elemento) {
            $payload['idDocumento'] = $this->firstAvailable($data, $document, 'idDocumento') ?? $this->resolveElementoId($elemento, $payload, $document);
            $payload['propietario'] = $this->firstAvailable($data, $document, 'propietario') ?? ($elemento->Profesor->FullName ?? '');
            $payload['fichero'] = $this->firstAvailable($data, $document, 'fichero') ?? ($elemento->fichero ?? null);
        } else {
            $payload['propietario'] = $this->firstAvailable($data, $document, 'propietario') ?? authUser()->FullName;
            $payload['tags'] = $this->firstAvailable($data, $document, 'tags') ?? 'listado llistat autorizacion autorizacio';
            $payload['rol'] = $this->firstAvailable($data, $document, 'rol') ?? 2;
        }

        return $payload;
    }

    private function firstAvailable(array $data, ?Documento $document, string $property)
    {
        if (array_key_exists($property, $data) && $data[$property] !== null && $data[$property] !== '') {
            return $data[$property];
        }

        if ($document && isset($document->$property) && $document->$property !== null && $document->$property !== '') {
            return $document->$property;
        }

        return null;
    }

    private function resolveElementoId($elemento, array $payload, ?Documento $document): ?string
    {
        $primaryKey = $elemento->primaryKey ?? null;

        return $elemento->id
            ?? ($primaryKey ? ($elemento->$primaryKey ?? null) : null)
            ?? ($payload['tipoDocumento'] ?? $document->tipoDocumento ?? null);
    }
}
