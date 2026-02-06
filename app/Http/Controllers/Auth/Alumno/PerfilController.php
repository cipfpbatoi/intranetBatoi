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
        $fotoRule = 'nullable|image|mimes:jpg,jpeg,png|max:10240';
        $foto = $request->file('foto');
        if ($foto) {
            $ext = strtolower($foto->getClientOriginalExtension());
            if (in_array($ext, ['heic', 'heif'], true)) {
                $fotoRule = 'nullable|file|mimes:heic,heif|max:10240';
            }
        }

        $request->validate(
            ['foto' => $fotoRule,
                'telef1' =>'max:14',
                'telef2' =>'max:14',
                'email' => 'email|max:45'
            ]
        );
        $nia = $id ?? Auth::user('alumno')->nia;
        $new = $this->class::find($nia);
        parent::update($request, $new);
        return redirect("/alumno/home");
    }

}
