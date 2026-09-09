<?php

namespace Intranet\Http\Controllers\Auth;

use Intranet\Http\Controllers\Core\IntranetController;

use Illuminate\Http\Request;
use Intranet\Http\Requests\AuthPerfilUpdateRequest;
use Intranet\Services\Media\ImageService;
use Intranet\Services\UI\AppAlert as Alert;
use Illuminate\Support\Facades\Log;


/**
 * Persistència compartida dels camps editables dels perfils d'usuari.
 */
abstract class PerfilController extends IntranetController
{
    /**
     * Vistes compartides de consulta i edició del perfil.
     *
     * @var array<string, string>
     */
    protected $vista = ['show' => 'perfil', 'edit' => 'perfil'];

    /**
     * Actualitza el perfil i reserva la gestió de rols a Direcció i Administració.
     *
     * @param Request $request
     * @param mixed $new
     * @return void
     */
    public function update(Request $request, $new)
    {
        $this->validate($request, (new AuthPerfilUpdateRequest())->rules());

        if ($request->email) {
            $new->email = $request->email;
        }

        if ($request->DA) {
            $new->DA = 1;
        } else {
            if (isset($new->DA)){
                $new->DA = 0;
            }
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
        if ($request->rol && $this->canManageRoles()) {
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
                try {
                    if ($new->foto) {
                        $success = ImageService::updatePhotoCarnet($fitxer, storage_path('app/public/fotos/'.$new->foto));
                        if ($success) {
                            Alert::info('Modificació foto feta amb exit');
                        } else {
                            Alert::info('Error en la modificació de la foto');
                        }
                } else {
                    $nameFile = ImageService::newPhotoCarnet($fitxer, storage_path('app/public/fotos'));
                    $new->foto = $nameFile;
                    Alert::info('Foto nova guardada amb exit');
                }
            } catch (\RuntimeException $e) {
                report($e);
                Log::error('Error actualitzant la foto de l\'usuari.', [
                    'dni' => $new->dni ?? null,
                    'error' => $e->getMessage(),
                ]);
                Alert::info($e->getMessage());
            }
        } else {
                Alert::info('Formato no valido');
            }
        }

        $new->save();
    }

    /**
     * Indica si l'usuari autenticat pot gestionar rols.
     */
    protected function canManageRoles(): bool
    {
        return userIsNameAllow('direccion') || userIsNameAllow('administrador');
    }

}
