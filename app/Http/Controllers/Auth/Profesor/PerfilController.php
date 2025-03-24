<?php

namespace Intranet\Http\Controllers\Auth\Profesor;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intranet\Http\Controllers\Auth\PerfilController as Perfil;
use Illuminate\Support\Facades\Auth;
use Intranet\Entities\Profesor;
use Intranet\Http\Requests\PerfilFilesRequest;
use Intranet\Services\DigitalSignatureService;
use Intranet\Services\ImageService;
use Intranet\Services\PhotoCarnet;
use Styde\Html\Facades\Alert;


class PerfilController extends Perfil
{

    protected $model = 'Profesor';
    protected $perfil = 'profesor';

    public function editar()
    {
         return parent::edit(AuthUser()->dni);
    }

    public function files()
    {
        $profesor = AuthUser();
        return view('perfil.files', compact('profesor'));
    }

    public function updateFiles(PerfilFilesRequest $request)
    {

        $new = Profesor::find(Auth::user('profesor')->dni);
        if ($request->hasFile('foto')) {
            $fitxer = $request->file('foto');
            if ($fitxer->isValid()) {
                if ($new->foto) {
                    ImageService::updatePhotoCarnet($fitxer, storage_path('app/public/fotos/'.$new->foto));
                    Alert::info('Modificació foto feta amb exit');
                } else {
                    $nameFile = ImageService::newPhotoCarnet($fitxer, storage_path('app/public/fotos'));
                    $new->foto = $nameFile;
                    $new->save();
                    Alert::info('Foto nova guardada amb exit');
                }
            } else {
                Alert::info('Formato no valido');
            }
        }
        if ($request->hasFile('signatura')) {
            $signatura = $request->file('signatura');
            if ($signatura->isValid()) {
                ImageService::toPng($signatura, storage_path('/app/public/signatures/'.$new->foto));
                Alert::info('Signatura guardada amb exit');
            } else {
                Alert::info('Format no vàlid');
            }
        }
        if ($request->hasFile('peu')) {
            $signatura = $request->file('peu');
            if ($signatura->isValid()) {
                ImageService::toPng($signatura, storage_path('/app/public/peus/'.$new->foto));
                Alert::info('Peu guardat amb exit');
            } else {
                Alert::info('Format no vàlid');
            }
        }
        if ($request->eliminar_certificat) {
            if (Hash::check($request->password, $new->password)) {
                DigitalSignatureService::deleteCertificate($new);
                Alert::info('Certificat eliminat');
            } else {
                Alert::info('La contrasenya no és correcta');
            }
        }
        if ($request->hasFile('certificat_digital')) {
            if ($request->file('certificat_digital')->isValid()) {
                if (Hash::check($request->password, $new->password)) {
                    $cert = $request->file('certificat_digital')->getRealPath();
                    $nameFile = $new->fileName;
                    DigitalSignatureService::cryptCertificate(
                        $cert,
                        $nameFile,
                        $request->password
                    );
                    Alert::info('Certificat guardat amb exit');
                } else {
                    Alert::info('La contrasenya no és correcta');
                }
            } else {
                Alert::info('Format no vàlid');
            }
        }

        return redirect()->back();
    }


    public function update(Request $request, $id=null)
    {
        $new = $this->class::find(Auth::user('profesor')->dni);
        if (isset($request->mostrar)) {
            $new->mostrar = $request->mostrar;
        } else {
            $new->mostrar = 0;
        }
        if (isset($request->movil1)){
            $new->movil1 = $request->movil1;
        }
        if (isset($request->movil2)){
            $new->movil2 = $request->movil2;
        }
        
        parent::update($request, $new);
        Alert::info(system('php ./../artisan cache:clear'));
        return redirect("/home");
    }

}
