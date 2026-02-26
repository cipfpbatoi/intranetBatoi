<?php

namespace Intranet\Http\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;

/**
 * Persisteix missatges d'alerta en sessió al final de cada petició web.
 *
 * Replica el comportament del middleware de Styde però desacoblat del seu
 * namespace perquè el Kernel no depenga directament del paquet.
 */
class AlertPersistMiddleware
{
    /**
     * @var Application
     */
    private Application $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Gestiona la petició HTTP i persistix alertes en finalitzar.
     *
     * @param mixed $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($this->app->bound('alert')) {
            $alert = $this->app->make('alert');
            if (is_object($alert) && method_exists($alert, 'push')) {
                $alert->push();
            }
        }

        return $response;
    }
}
