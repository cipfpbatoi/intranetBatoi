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
use Intranet\Services\FormBuilder;
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
        $profesor = Profesor::findOrFail(Auth::user('profesor')->dni);

        // Processa cada fitxer o acció en mètodes separats
        $this->updatePhoto($request, $profesor);
        $this->updateSignature($request, $profesor);
        $this->updatePeu($request, $profesor);
        $this->deleteCertificate($request, $profesor);
        $this->updateDigitalCertificate($request, $profesor);

        return redirect()->back();
    }

    private function updatePhoto(PerfilFilesRequest $request, Profesor $profesor)
    {
        if (!$request->hasFile('foto')) {
            return;
        }

        $foto = $request->file('foto');
        if (!$foto->isValid()) {
            Alert::info('Format no vàlid');
            return;
        }

        if ($profesor->foto) {
            // Actualitzem la foto si ja existia
            ImageService::updatePhotoCarnet($foto, storage_path('app/public/fotos/' . $profesor->foto));
            Alert::info('Modificació de la foto feta amb èxit');
        } else {
            // Guardem una foto nova si no en tenia
            $fileName = ImageService::newPhotoCarnet($foto, storage_path('app/public/fotos'));
            $profesor->foto = $fileName;
            $profesor->save();

            Alert::info('Foto nova guardada amb èxit');
        }
    }

    private function updateSignature(PerfilFilesRequest $request, Profesor $profesor)
    {
        if (!$request->hasFile('signatura')) {
            return;
        }

        $signatura = $request->file('signatura');
        if (!$signatura->isValid()) {
            Alert::info('Format no vàlid');
            return;
        }

        // Si vols guardar-ho amb el mateix nom que la foto (segons el teu codi original):
        ImageService::toPng($signatura, storage_path('app/public/signatures/' . $profesor->foto));
        Alert::info('Signatura guardada amb èxit');
    }

    private function updatePeu(PerfilFilesRequest $request, Profesor $profesor)
    {
        if (!$request->hasFile('peu')) {
            return;
        }

        $peu = $request->file('peu');
        if (!$peu->isValid()) {
            Alert::info('Format no vàlid');
            return;
        }

        ImageService::toPng($peu, storage_path('app/public/peus/' . $profesor->foto));
        Alert::info('Peu guardat amb èxit');
    }

    private function deleteCertificate(PerfilFilesRequest $request, Profesor $profesor)
    {
        if (!$request->eliminar_certificat) {
            return;
        }

        if (!Hash::check($request->password, $profesor->password)) {
            Alert::info('La contrasenya no és correcta');
            return;
        }

        DigitalSignatureService::deleteCertificate($profesor);
        Alert::info('Certificat eliminat');
    }

    private function updateDigitalCertificate(PerfilFilesRequest $request, Profesor $profesor)
    {
        if (!$request->hasFile('certificat_digital')) {
            return;
        }

        $certificatDigital = $request->file('certificat_digital');
        if (!$certificatDigital->isValid()) {
            Alert::info('Format no vàlid');
            return;
        }

        if (!Hash::check($request->password, $profesor->password)) {
            Alert::info('La contrasenya no és correcta');
            return;
        }

        // Assumint que $profesor->fileName és on es guarda el nom del certificat
        $certPath = $certificatDigital->getRealPath();
        $nameFile = $profesor->fileName;

        DigitalSignatureService::cryptCertificate($certPath, $nameFile, $request->password);
        Alert::info('Certificat guardat amb èxit');
    }


    public function update(Request $request, $id=null)
    {
        $new = Profesor::find(Auth::user('profesor')->dni);
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
