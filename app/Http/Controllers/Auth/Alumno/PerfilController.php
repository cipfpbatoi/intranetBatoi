<?php

namespace Intranet\Http\Controllers\Auth\Alumno;

use Illuminate\Http\Request;
use Intranet\Http\Controllers\Auth\PerfilController as Perfil;
use Illuminate\Support\Facades\Auth;
use Intranet\Services\ImageService;
use Styde\Html\Facades\Alert;

class PerfilController extends Perfil
{

    protected $model = 'Alumno';
    protected $perfil = 'alumno';

    public function editar()
    {
        $id = Auth::user('alumno')->nia;
        return parent::edit($id);
    }

    public function update(Request $request, $id = null)
    {
        $request->validate(['foto' => 'nullable|image|mimes:jpg,jpeg,png|max:10240']);
        $new = $this->class::find(Auth::user('alumno')->nia);
        parent::update($request, $new);
        return redirect("/alumno/home");
    }

}
