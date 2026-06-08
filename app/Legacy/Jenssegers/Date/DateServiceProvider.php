<?php

declare(strict_types=1);

namespace Jenssegers\Date;

use Illuminate\Support\ServiceProvider;

/**
 * Service provider de compatibilitat per a entorns on no està instal·lat
 * el paquet original `jenssegers/date`.
 */
class DateServiceProvider extends ServiceProvider
{
    /**
     * Registre buit: la compatibilitat es resol per autoload i Carbon.
     */
    public function register(): void
    {
    }
}
