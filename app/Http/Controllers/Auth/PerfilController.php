<?php

namespace Intranet\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Intranet\Http\Controllers\IntranetController;
use Intranet\Services\ImageService;
use Styde\Html\Facades\Alert;


abstract class PerfilController extends IntranetController
{
    
    protected $vista = ['show' => 'perfil', 'edit' => 'perfil'];

    public function update(Request $request, $new)
    {

        $this->validate($request, $new->getRules());
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
        if ($request->telef1) {
            $new->telef1 = $request->telef1;
        }
        if ($request->telef2) {
            $new->telef2 = $request->telef2;
        }
        if ($request->hasFile('foto')) {
            $fitxer = $request->file('foto');
            if ($fitxer->isValid()) {
                if ($new->foto) {
                    ImageService::updatePhotoCarnet($fitxer, storage_path('app/public/fotos/'.$new->foto));
                    Alert::info('Modificació foto feta amb exit');
                } else {
                    $nameFile = ImageService::newPhotoCarnet($fitxer, storage_path('app/public/fotos'));
                    $new->foto = $nameFile;
                    Alert::info('Foto nova guardada amb exit');
                }
            } else {
                Alert::info('Formato no valido');
            }
        }
        
        $new->save();
    }

}
