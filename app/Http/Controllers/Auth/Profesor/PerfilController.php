<?php

namespace Intranet\Http\Controllers\Auth\Profesor;

use Illuminate\Http\Request;
use Intranet\Http\Controllers\Auth\PerfilController as Perfil;
use Illuminate\Support\Facades\Auth;

class PerfilController extends Perfil
{

    protected $model = 'Profesor';
    protected $perfil = 'profesor';

    public function editar()
    {
        return parent::edit(AuthUser()->dni);
    }

    public function update(Request $request, $id = null)
    {
        $new = $this->class::find(Auth::user('profesor')->dni);
        if (isset($request->mostrar))
            $new->mostrar = $request->mostrar;
        else 
            $new->mostrar = 0;
        parent::update($request, $new);
        return redirect("/home");
    }

}
