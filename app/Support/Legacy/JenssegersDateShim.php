<?php

/**
 * Shim de compatibilitat per a codi legacy que usa Jenssegers\Date\Date.
 *
 * Manté la signatura de `setlocale()` i delega en Carbon 3 (`setLocale()`),
 * evitant dependència externa incompatible amb Laravel 12.
 */
namespace Jenssegers\Date;

use Illuminate\Support\Carbon;

if (!class_exists(Date::class)) {
    class Date extends Carbon
    {
        /**
         * Compatibilitat amb API antiga (minúscules).
         *
         * @param string $locale
         * @return bool
         */
        public static function setlocale($locale): bool
        {
            parent::setLocale((string) $locale);

            return true;
        }
    }
}

