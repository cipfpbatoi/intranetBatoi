<?php

/**
 * Shim de compatibilitat per a codi legacy que usa Jenssegers\Date\Date.
 *
 * Manté l'API antiga (`Date::setlocale(...)`) però sobre Carbon 3.
 */
namespace Jenssegers\Date;

use Illuminate\Support\Carbon;

if (!class_exists(Date::class)) {
    class Date extends Carbon
    {
        /**
         * Compatibilitat amb l'API antiga de jenssegers/date.
         * En PHP, la crida `setlocale` i `setLocale` resol al mateix mètode.
         */
        public static function setLocale(string $locale): void
        {
            parent::setLocale($locale);
        }
    }
}

