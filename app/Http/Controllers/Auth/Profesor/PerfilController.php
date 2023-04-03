<?php

namespace Intranet\Http\Controllers\Auth\Profesor;

use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Intranet\Http\Controllers\Auth\PerfilController as Perfil;
use Illuminate\Support\Facades\Auth;
use Intranet\Entities\Profesor;
use Intranet\Http\Requests\PerfilFilesRequest;
use Intranet\Services\DigitalSignatureService;
use Styde\Html\Facades\Alert;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

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
            if ($request->file('foto')->isValid()) {
                $new->foto = $request->file('foto')->store('fotos', 'public');
            } else {
                Alert::info('Formato no valido');
            }
        }
        if ($request->hasFile('signatura')) {
            if ($request->file('signatura')->isValid()) {
                $request->file('signatura')
                    ->storeAs(
                        'signatures',
                        $new->fileName.'.png',
                        'public'
                    );
                Alert::info('Signatura guardada amb exit');
            } else {
                Alert::info('Format no vàlid');
            }
        }
        if ($request->hasFile('peu')) {
            if ($request->file('peu')->isValid()) {
                $request->file('peu')
                    ->storeAs(
                        'peus',
                        $new->fileName.'.png',
                        'public'
                    );
                Alert::info('Peu guardat amb exit');
            } else {
                Alert::info('Format no vàlid');
            }
        }
        if ($request->hasFile('certificat_digital')) {
            if ($request->file('certificat_digital')->isValid()) {
                if (Hash::check($request->password, $new->password)) {
                    DigitalSignatureService::cryptCertificate(
                        $request->file('certificat_digital')->getRealPath(),
                        storage_path('app/certificats/'.$new->fileName.'.tmp'),
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
        $new->save();
        return redirect()->back();
    }


    public function update(Request $request, $id = null)
    {
        $new = $this->class::find(Auth::user('profesor')->dni);
        if (isset($request->mostrar)) {
            $new->mostrar = $request->mostrar;
        } else {
            $new->mostrar = 0;
        }
        
        parent::update($request, $new);
        Alert::info(system('php ./../artisan cache:clear'));
        return redirect("/home");
    }

}
