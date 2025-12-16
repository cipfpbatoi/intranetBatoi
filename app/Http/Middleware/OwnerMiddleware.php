<?php

namespace Intranet\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next,$model)
    {
        $Model = 'Intranet\\Entities\\'.$model;
        $id = $request->segments()[1];
        $registre = $Model::findOrFail($id);

        if (!$this->owner($registre)) {

            // Evitem abort dur: redirigim enrere amb missatge i codi 403
            return redirect()->back()
                ->with('error', 'Has de ser el propietari per fer esta operació.')
                ->setStatusCode(403);
        }

        return $next($request);
    }

    private function owner($model){
        if (isset($model->dni)) {
            return ($model->dni === Auth::user()->dni) ;
        }
        if (isset($model->idProfesor)) {
            return ($model->idProfesor === Auth::user()->dni);
        }
        if ($model->Creador() != null) {
            return ($model->Creador() === Auth::user()->dni);
        }
        return false;
    }
}
