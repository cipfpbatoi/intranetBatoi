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

    const FITXER_VERSION = 'version.txt';
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
        $version_actual = Storage::exists(self::FITXER_VERSION)?Storage::get(self::FITXER_VERSION):'v0';
        if ($version_nueva > $version_actual){
            AdministracionController::exe_actualizacion($version_actual);
            Storage::put(self::FITXER_VERSION,$version_nueva);
            Alert::info('Actualització realitzada correctament');
        }
        else {
            Alert::info('Ja tens la darrera versió');
        }
        return redirect('/');
    }

}