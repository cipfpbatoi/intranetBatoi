<?php

namespace Intranet\Http\Traits;

use Illuminate\Support\Facades\Schema;

/**
 * Trait traitPanel
 * @package Intranet\Http\Controllers
 */
trait Searchable
{
    protected function search()
    {
        $query = $this->class::query();

        // Comprovar si la taula té el camp idProfesor sense carregar dades
        if (Schema::hasColumn((new $this->class)->getTable(), 'idProfesor')) {
            $query->where('idProfesor', '=', AuthUser()->dni);
        }

        return $query->get();
    }
}