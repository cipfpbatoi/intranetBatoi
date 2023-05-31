<?php

namespace Intranet\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Intranet\Http\Controllers\IntranetController;
use Styde\Html\Facades\Alert;


abstract class PerfilController extends IntranetController
{
    
    protected $vista = ['show' => 'perfil', 'edit' => 'perfil'];

    public function update(Request $request, $new)
    {
        $this->validate($request, $new->getRules());
        if ($request->hasFile('foto')) {
            if ($request->file('foto')->isValid()) {
                $new->foto = $request->file('foto')->store('fotos', 'public');
            } else {
                Alert::info('Formato no valido');
            }
        }
        if ($request->email) {
            $new->email = $request->email;
        }
        if ($request->emailItaca) {
            $new->emailItaca = $request->emailItaca;
        }
        if ($request->idioma) {
            $new->idioma = $request->idioma;
        }
        if ($request->departamento) {
            $new->departamento = $request->departamento;
        }
        if ($request->especialitat) {
            $new->especialitat = $request->especialitat;
        }
        if ($request->rol) {
            $new->rol = Rol($request->rol);
        }
        
        $new->save();
    }

}
