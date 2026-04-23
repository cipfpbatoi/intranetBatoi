<?php
namespace Intranet\Http\Controllers;

use Intranet\Services\UI\AppAlert as Alert;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
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
        Log::info('Iniciant /actualizacion.', [
            'base_path' => base_path(),
            'configured_branch' => env('BRANCH'),
            'resolved_branch' => $this->resolveBranch(),
        ]);

        if (File::exists(base_path('composer.lock'))) {
            File::delete(base_path('composer.lock'));
            Alert::info('composer.lock eliminat');
            Log::info('composer.lock eliminat durant /actualizacion.');
        }

        $branch = $this->resolveBranch();
        if (! $this->runShell('git pull origin ' . $branch, 'git pull')) {
            Log::warning('/actualizacion interrompuda després de fallar git pull.', [
                'branch' => $branch,
            ]);
            return redirect()->route('home');
        }

        Artisan::call('config:cache');
        Alert::info('config:cache executat');
        Log::info('config:cache executat durant /actualizacion.');

        Artisan::call('migrate', ['--force' => true]);
        Alert::info('migrate --force executat');
        Log::info('migrate --force executat durant /actualizacion.');

        $versionesInstaladas = config('constants.version');
        $versionNueva = end($versionesInstaladas);
        $versionActual = Storage::exists(self::FITXER_VERSION)?Storage::get(self::FITXER_VERSION):'v0';
        if ($versionNueva > $versionActual) {
            AdministracionController::exe_actualizacion($versionActual);
            Storage::put(self::FITXER_VERSION, $versionNueva);
            Alert::info('Actualització realitzada correctament');
            Log::info('Actualització funcional executada.', [
                'version_actual' => $versionActual,
                'version_nova' => $versionNueva,
            ]);
        } else {
            Alert::info('Ja tens la darrera versió');
            Log::info('No cal aplicar actualitzacions funcionals.', [
                'version_actual' => $versionActual,
                'version_nova' => $versionNueva,
            ]);
        }

        return redirect()->route('home');
    }

    /**
     * Executa una orde de shell i mostra avisos amb el resultat.
     *
     * @param string $command
     * @param string $label
     * @return bool
     */
    private function runShell(string $command, string $label): bool
    {
        $env = str_starts_with($command, 'git ')
            ? $this->gitEnv()
            : null;

        Log::info('Executant orde de shell d\'actualització.', [
            'label' => $label,
            'command' => $command,
            'working_directory' => base_path(),
        ]);

        $error = null;
        $process = Process::fromShellCommandline($command, base_path(), $env);
        $process->run();
        $error = $process->getErrorOutput();

        if (! $process->isSuccessful()) {
            if (str_contains($error, 'Host key verification failed')) {
                $this->addGithubKnownHost($env['HOME']);
                $process = Process::fromShellCommandline($command, base_path(), $env);
                $process->run();
                $error = $process->getErrorOutput();
            }

            // Torna a provar si git es queixa per "dubious ownership"
            if (str_contains($error, 'detected dubious ownership')) {
                $this->markRepoAsSafe();
                $process = Process::fromShellCommandline($command, base_path(), $env);
                $process->run();
                $error = $process->getErrorOutput();
            }

            if (str_contains($error, '.git/FETCH_HEAD') && str_contains($error, 'Permission denied')) {
                Alert::warning(
                    "No puc executar $label: l'usuari del servidor no té permisos d'escriptura sobre ".
                    base_path('.git').". Dona-li permisos (p.ex. `sudo chown -R www-data:www-data ".
                    base_path('.git')."; sudo chmod -R g+rwX ".base_path('.git')."`)."
                );
                Log::warning('Error de permisos durant /actualizacion.', [
                    'label' => $label,
                    'error' => $error,
                ]);
                return false;
            }

            if (! $process->isSuccessful()) {
                $message = $error ?: $process->getErrorOutput();
                Alert::warning("$label ha fallat: ".$message);
                Log::warning('Orde de shell fallida durant /actualizacion.', [
                    'label' => $label,
                    'error' => $message,
                ]);
                return false;
            }
        }

        $output = trim($process->getOutput());
        Alert::info($output !== '' ? $output : "$label completat");
        Log::info('Orde de shell completada durant /actualizacion.', [
            'label' => $label,
            'output' => $output,
        ]);
        return true;
    }

    /**
     * Resol la branca a actualitzar.
     *
     * @return string
     */
    private function resolveBranch(): string
    {
        $configuredBranch = trim((string) env('BRANCH', ''));
        if ($configuredBranch !== '') {
            return $configuredBranch;
        }

        $branch = trim((string) $this->currentGitBranch());

        return $branch !== '' && $branch !== 'HEAD'
            ? $branch
            : (string) config('constants.branch');
    }

    /**
     * Obtín la branca actual del repositori.
     *
     * @return string|null
     */
    private function currentGitBranch(): ?string
    {
        $process = Process::fromShellCommandline(
            'git rev-parse --abbrev-ref HEAD',
            base_path(),
            $this->gitEnv()
        );
        $process->run();

        if (! $process->isSuccessful()) {
            return null;
        }

        return trim($process->getOutput());
    }

    private function gitEnv(): array
    {
        $home = storage_path('git-home');
        File::ensureDirectoryExists($home);
        File::ensureDirectoryExists("$home/.ssh");
        $knownHosts = rtrim($home, '/').'/.ssh/known_hosts';
        $sshCommand = sprintf(
            'ssh -o UserKnownHostsFile=%s -o StrictHostKeyChecking=no -o BatchMode=yes',
            escapeshellarg($knownHosts)
        );

        return [
            'HOME' => $home,
            'GIT_SSH_COMMAND' => $sshCommand,
        ];
    }

    /**
     * Afegeix la clau pública de github.com a known_hosts de l'HOME del procés git.
     */
    private function addGithubKnownHost(string $home): void
    {
        $knownHosts = rtrim($home, '/').'/.ssh/known_hosts';
        $scan = Process::fromShellCommandline(
            'ssh-keyscan -H github.com >> '.escapeshellarg($knownHosts),
            base_path(),
            ['HOME' => $home]
        );
        $scan->run();
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
