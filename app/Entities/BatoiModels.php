<?php

namespace Intranet\Entities;

/**
 * Compatibilitat temporal per a models que encara resolen
 * `Intranet\Entities\BatoiModels`.
 *
 * @deprecated Usa `Intranet\Entities\Concerns\BatoiModels`.
 */
trait BatoiModels
{
    use \Intranet\Entities\Concerns\BatoiModels;
}

