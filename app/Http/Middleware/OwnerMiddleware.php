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

            abort(403, 'Has de ser el propietari per fer esta operació.');
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
