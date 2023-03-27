<?php

namespace Intranet\Http\Controllers\Auth\Profesor;

use Illuminate\Http\Request;
use Intranet\Http\Controllers\Auth\PerfilController as Perfil;
use Illuminate\Support\Facades\Auth;
use Intranet\Entities\Profesor;
use Intranet\Http\Requests\PerfilFilesRequest;
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
