<?php
namespace Intranet\Http\Controllers;

use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Storage;


/**
 * Class ActualizacionController
 * @package Intranet\Http\Controllers
 */
class ActualizacionController extends Controller
{

    const FITXER_VERSION = 'version.txt';

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function actualizacion()
    {
        Alert::info(system('rm ./../composer.lock'));
        Alert::info(system('git pull origin '.config('constants.branch')));
        Alert::info(system('php ./../artisan config:cache'));
        Alert::info(system('php ./../artisan migrate --force'));
        $versionesInstaladas = config('constants.version');
        $versionNueva = end($versionesInstaladas);
        $versionActual = Storage::exists(self::FITXER_VERSION)?Storage::get(self::FITXER_VERSION):'v0';
        if ($versionNueva > $versionActual) {
            AdministracionController::exe_actualizacion($versionActual);
            Storage::put(self::FITXER_VERSION, $versionNueva);
            Alert::info('Actualització realitzada correctament');
        } else {
            Alert::info('Ja tens la darrera versió');
        }
        return redirect('/');
    }
}
