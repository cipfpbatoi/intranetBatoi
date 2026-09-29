<?php

declare(strict_types=1);

namespace Intranet\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Intranet\Application\Convalidacio\ConvalidacioAccessService;
use Symfony\Component\HttpFoundation\Response;

/** Requerix la clau de proves quan Direcció bloqueja les convalidacions. */
class ConvalidacioAlumnoAccessMiddleware
{
    public function __construct(private readonly ConvalidacioAccessService $access)
    {
    }

    /** Redirigix a la pantalla de clau si l'alumne no ha desbloquejat el cicle vigent. */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->access->isBlocked() || $this->access->sessionIsUnlocked()) {
            return $next($request);
        }

        return redirect()->route('convalidacions.access');
    }
}
