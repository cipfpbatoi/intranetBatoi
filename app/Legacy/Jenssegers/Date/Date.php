<?php

declare(strict_types=1);

namespace Jenssegers\Date;

use Carbon\Carbon;

/**
 * Capa de compatibilitat mínima per al codi legacy que encara referencia
 * `Jenssegers\Date\Date`.
 *
 * La llibreria original ja no està present en l'entorn actual, però la major
 * part del projecte només necessita el comportament de Carbon amb suport de
 * locale i els noms històrics `setlocale`/`setLocale`.
 */
class Date extends Carbon
{
    /**
     * Manté la signatura històrica en minúscules usada pel projecte.
     */
    public static function setlocale($locale = 'en'): void
    {
        static::setLocale((string) $locale);
    }
}
