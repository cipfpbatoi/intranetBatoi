<?php
/* clase : IntranetController
 * És la classe pare de tots els controladors amb el mètodes comuns a ells
 */
namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use Intranet\Http\Controllers\AdministracionController;

class ActualizacionController extends Controller{
    
  protected function actualizacion(){
        system('rm ./../app/Http/Controllers/AdministracionController.php');
        Alert::info(system('git pull'));
        Alert::info(system('php ./../artisan config:cache'));
        Alert::info(system('php ./../artisan migrate'));
        $versiones = config('constants.version');
        $version_nueva = end($versiones);
        if (Storage::exists('version.txt')) $version_actual = Storage::get('version.txt');
        else $version_actual = 'v_0';
        if ($version_nueva > $version_actual){
            AdministracionController::exe_actualizacion($version_actual);
            Storage::put('version.txt',$version_nueva);
            Alert::info('Actualització realitzada correctament');
        }
        else Alert::info('Ja tens la darrera versió');
        return redirect('/');
    }
}