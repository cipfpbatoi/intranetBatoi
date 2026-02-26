<?php

namespace Intranet\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Façana de compatibilitat per a l'API `Field::*`.
 */
class Field extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'field';
    }
}
