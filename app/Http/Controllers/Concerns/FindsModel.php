<?php

namespace Intranet\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Intranet\Exceptions\NotFoundDomainException;

/**
 * Utilitats per a resoldre models amb errors de domini coherents.
 */
trait FindsModel
{
    /**
     * Recupera un model o llança una excepció de domini coherent.
     *
     * @param class-string<Model> $modelClass
     * @param int|string $id
     * @param string $message
     * @param array<string, mixed> $context
     * @throws NotFoundDomainException
     * @return Model
     */
    protected function findModelOrFail(
        string $modelClass,
        $id,
        string $message = 'Element no trobat',
        array $context = []
    ): Model {
        try {
            return $modelClass::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            if ($context === []) {
                $context = ['id' => $id];
            }
            throw new NotFoundDomainException($message, $context, $e);
        }
    }

    /**
     * Envolta una cerca que podria llançar ModelNotFoundException.
     *
     * @param callable $resolver
     * @param string $message
     * @param array<string, mixed> $context
     * @throws NotFoundDomainException
     * @return mixed
     */
    protected function wrapNotFound(callable $resolver, string $message = 'Element no trobat', array $context = [])
    {
        try {
            return $resolver();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException($message, $context, $e);
        }
    }
}
