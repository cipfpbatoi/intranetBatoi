<?php

namespace Intranet\Http\Controllers\Auth\Alumno;

use Illuminate\Http\Request;
use Intranet\Http\Controllers\Auth\PerfilController as Perfil;
use Illuminate\Support\Facades\Auth;
use Intranet\Services\Media\ImageService;
use Styde\Html\Facades\Alert;

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
        $nia = $id ?? auth('alumno')->user()->nia;
        $class = $this->modelClass();
        $new = $class::find($nia) ?? auth('alumno')->user();
        if (!$new) {
            abort(404);
        }
        $new->setConnection(config('database.default'));
        parent::update($request, $new);
        return redirect("/alumno/home");
    }

}
