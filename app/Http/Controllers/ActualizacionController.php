<?php
/* clase : IntranetController
 * És la classe pare de tots els controladors amb el mètodes comuns a ells
 */
namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Controller;
use Intranet\Botones\Panel;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Programacion;
use Intranet\Entities\Modulo_ciclo;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Resultado;
use DB;
use Intranet\Entities\Horario;
use Intranet\Entities\Grupo;
use Styde\Html\Facades\Alert;
use Intranet\Entities\Profesor;
use Illuminate\Support\Facades\Storage;

class ActualizacionController extends Controller{
    
  protected function actualizacion(){
        system('git pull');
        system('php ./../artisan config:cache');
        system('php ./../artisan migrate');
        $version_nueva = end(config('constants.version'));
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