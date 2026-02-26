<?php

namespace Intranet\Http\Controllers\Auth\Alumno;

use Illuminate\Http\Request;
use Intranet\Http\Controllers\Auth\PerfilController as Perfil;
use Illuminate\Support\Facades\Auth;
use Intranet\Http\Requests\AlumnoPerfilUpdateRequest;

class PerfilController extends Perfil
{

    protected $model = 'Alumno';
    protected $perfil = 'alumno';

    public function editar()
    {
        $id = auth('alumno')->user()->nia;
        return parent::edit($id);
    }

    public function update(Request $request, $id = null)
    {
        $this->validate($request, (new AlumnoPerfilUpdateRequest())->rules());
        $nia = $id ?? auth('alumno')->user()->nia;
        $class = $this->modelClass();
        $new = $class::find($nia) ?? auth('alumno')->user();
        if (!$new) {
            abort(404);
        }
        $new->setConnection(config('database.default'));
        parent::update($request, $new);
        return redirect()->route('home.alumno');
    }

}
