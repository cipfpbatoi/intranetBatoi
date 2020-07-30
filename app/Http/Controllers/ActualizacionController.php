<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use Intranet\Http\Controllers\AdministracionController;

/**
 * Class ActualizacionController
 * @package Intranet\Http\Controllers
 */
class ActualizacionController extends Controller{

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function actualizacion(){
        Alert::info(system('rm ./../composer.lock'));
        Alert::info(system('git pull'));
        Alert::info(system('php ./../artisan config:cache'));
        Alert::info(system('php ./../artisan migrate --force'));
        $versionesInstaladas = config('constants.version');
        $version_nueva = end($versionesInstaladas );
        $version_actual = Storage::exists('version.txt')?Storage::get('version.txt'):'v0';
        if ($version_nueva > $version_actual){
            AdministracionController::exe_actualizacion($version_actual);
            Storage::put('version.txt',$version_nueva);
            Alert::info('Actualització realitzada correctament');
        }
        else Alert::info('Ja tens la darrera versió');
        return redirect('/');
    }

}