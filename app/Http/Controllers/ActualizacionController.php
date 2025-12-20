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
        $env = str_starts_with($command, 'git ')
            ? $this->gitEnv()
            : null;

        $process = Process::fromShellCommandline($command, base_path(), $env);
        $process->run();

        if (! $process->isSuccessful()) {
            $error = $process->getErrorOutput();

            // Torna a provar si git es queixa per "dubious ownership"
            if (str_contains($error, 'detected dubious ownership')) {
                $this->markRepoAsSafe();
                $process = Process::fromShellCommandline($command, base_path(), $env);
                $process->run();
            }

            if (str_contains($error, '.git/FETCH_HEAD') && str_contains($error, 'Permission denied')) {
                Alert::warning(
                    "No puc executar $label: l'usuari del servidor no té permisos d'escriptura sobre ".
                    base_path('.git').". Dona-li permisos (p.ex. `sudo chown -R www-data:www-data ".
                    base_path('.git')."; sudo chmod -R g+rwX ".base_path('.git')."`)."
                );
                return;
            }

            if (! $process->isSuccessful()) {
                Alert::warning("$label ha fallat: ".$process->getErrorOutput());
                return;
            }
        }

        $output = trim($process->getOutput());
        Alert::info($output !== '' ? $output : "$label completat");
    }

    private function gitEnv(): array
    {
        $home = storage_path('git-home');
        File::ensureDirectoryExists($home);

        return ['HOME' => $home];
    }

    private function markRepoAsSafe(): void
    {
        $safe = Process::fromShellCommandline(
            'git config --global --add safe.directory '.escapeshellarg(base_path()),
            base_path(),
            $this->gitEnv()
        );
        $safe->run();
    }

}
