<?php

namespace Intranet\Http\Controllers\Auth\Profesor;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intranet\Http\Controllers\Auth\PerfilController as Perfil;
use Illuminate\Support\Facades\Auth;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\Profesor;
use Intranet\Http\Requests\PerfilFilesRequest;
use Intranet\Http\Requests\ProfesorPerfilUpdateRequest;
use Intranet\Services\Signature\DigitalSignatureService;
use Intranet\Services\UI\FormBuilder;
use Intranet\Services\Media\ImageService;
use Intranet\Services\PhotoCarnet;
use Intranet\Services\UI\AppAlert as Alert;


class PerfilController extends Perfil
{

    protected $model = 'Profesor';
    protected $perfil = 'profesor';
    private ?ProfesorService $profesorService = null;

    private function profesores(): ProfesorService
    {
        if ($this->profesorService === null) {
            $this->profesorService = app(ProfesorService::class);
        }

        return $this->profesorService;
    }

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
        $profesor = $this->profesores()->findOrFail((string) Auth::user('profesor')->dni);

        // Processa cada fitxer o acció en mètodes separats
        $this->updatePhoto($request, $profesor);
        $this->updateSignature($request, $profesor);
        $this->updatePeu($request, $profesor);
        $this->deleteCertificate($request, $profesor);
        $this->updateDigitalCertificate($request, $profesor);

        return redirect()->back();
    }

    /**
     * Processa la pujada de foto de perfil i garanteix persistència en BBDD.
     *
     * Sempre genera un nou fitxer PNG i actualitza el camp `foto` del professor.
     * Si hi havia una foto anterior, elimina la vella i intenta traslladar
     * signatura/peu vinculats al nom antic.
     *
     * @param PerfilFilesRequest $request
     * @param Profesor $profesor
     * @return void
     */
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

        try {
            $oldPhoto = $profesor->foto;
            $newPhoto = ImageService::newPhotoCarnet($foto, storage_path('app/public/fotos'));
            $newPhoto = basename((string) $newPhoto);

            $profesor->foto = $newPhoto;
            $profesor->save();

            $this->cleanupAndRelinkProfileAssets($oldPhoto, $newPhoto);

            Alert::info($oldPhoto ? 'Modificació de la foto feta amb èxit' : 'Foto nova guardada amb èxit');
        } catch (\RuntimeException $e) {
            Alert::info($e->getMessage());
        }
    }

    /**
     * Elimina/relaciona fitxers antics lligats a la foto anterior.
     *
     * @param string|null $oldPhoto
     * @param string $newPhoto
     * @return void
     */
    private function cleanupAndRelinkProfileAssets(?string $oldPhoto, string $newPhoto): void
    {
        $oldPhoto = $oldPhoto ? basename(str_replace('\\', '/', $oldPhoto)) : null;
        $newPhoto = basename(str_replace('\\', '/', $newPhoto));

        if (empty($oldPhoto) || $oldPhoto === $newPhoto) {
            return;
        }

        $oldPhotoPath = storage_path('app/public/fotos/' . $oldPhoto);
        if (is_file($oldPhotoPath)) {
            @unlink($oldPhotoPath);
        }

        $this->moveProfileAsset('signatures', $oldPhoto, $newPhoto);
        $this->moveProfileAsset('peus', $oldPhoto, $newPhoto);
    }

    /**
     * Mou un fitxer d'asset de perfil si existeix amb el nom antic.
     *
     * @param string $folder
     * @param string $oldPhoto
     * @param string $newPhoto
     * @return void
     */
    private function moveProfileAsset(string $folder, string $oldPhoto, string $newPhoto): void
    {
        $oldPath = storage_path('app/public/' . $folder . '/' . $oldPhoto);
        $newPath = storage_path('app/public/' . $folder . '/' . $newPhoto);

        if (!is_file($oldPath) || is_file($newPath)) {
            return;
        }

        if (!@rename($oldPath, $newPath)) {
            if (@copy($oldPath, $newPath)) {
                @unlink($oldPath);
            }
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
        $this->validate($request, (new ProfesorPerfilUpdateRequest())->rules());
        $new = $this->profesores()->find((string) Auth::user('profesor')->dni);
        if (!$new) {
            return redirect()->route('home.profesor');
        }
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
        return redirect()->route('home.profesor');
    }

}
