<?php
namespace Intranet\Http\Controllers;

use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;


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
    public function actualizacion()
    {
        if (File::exists(base_path('composer.lock'))) {
            File::delete(base_path('composer.lock'));
            Alert::info('composer.lock eliminat');
        }

        $this->runShell('git pull origin '.config('constants.branch'), 'git pull');

        Artisan::call('config:cache');
        Alert::info('config:cache executat');

        Artisan::call('migrate', ['--force' => true]);
        Alert::info('migrate --force executat');

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

    private function runShell(string $command, string $label): void
    {
        $process = Process::fromShellCommandline($command, base_path());
        $process->run();

        if (! $process->isSuccessful()) {
            Alert::warning("$label ha fallat: ".$process->getErrorOutput());
            return;
        }

        $output = trim($process->getOutput());
        Alert::info($output !== '' ? $output : "$label completat");
    }

}
