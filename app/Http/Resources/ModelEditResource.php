<?php

declare(strict_types=1);

namespace Intranet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Base comuna per a recursos de payload d'edició.
 *
 * Permet declarar només la llista de camps exposats pel formulari.
 */
abstract class ModelEditResource extends JsonResource
{
    /**
     * Llista de camps que formen el contracte d'edició.
     *
     * @return array<int, string>
     */
    abstract protected function fields(): array;

    /**
     * Transforma el model a un array simple restringit als camps editables.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $data = [];

        foreach ($this->fields() as $field) {
            $data[$field] = $this->{$field};
        }

        return $data;
    }
}
