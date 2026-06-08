<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Registra autoloads mínims per a compatibilitats locals necessàries en tests.
     */
    private function registerTestingAutoloads(): void
    {
        spl_autoload_register(static function (string $class): void {
            $prefixes = [
                'Jenssegers\\Date\\' => __DIR__ . '/../app/Legacy/Jenssegers/Date/',
                'Styde\\Html\\' => __DIR__ . '/../packages/html/src/',
            ];

            foreach ($prefixes as $prefix => $basePath) {
                if (!str_starts_with($class, $prefix)) {
                    continue;
                }

                $relative = substr($class, strlen($prefix));
                $file = $basePath . str_replace('\\', '/', $relative) . '.php';
                if (is_file($file)) {
                    require_once $file;
                }
            }
        });

        require_once __DIR__ . '/../packages/html/src/helpers.php';
    }

    /**
     * Prepara l'aplicació de Laravel per a les proves.
     */
    public function createApplication()
    {
        $this->registerTestingAutoloads();

        // Ensure tests never consume stale cached config from local/dev containers.
        $testingConfigCache = sys_get_temp_dir() . '/intranetBatoi-testing-config.php';
        putenv('APP_ENV=testing');
        putenv("APP_CONFIG_CACHE={$testingConfigCache}");
        putenv('LOG_CHANNEL=stack');
        putenv('DB_CONNECTION=sqlite');
        putenv('DB_DATABASE=:memory:');
        putenv('CACHE_DRIVER=array');
        putenv('SESSION_DRIVER=array');
        putenv('QUEUE_CONNECTION=sync');
        putenv('MAIL_DRIVER=array');
        $_ENV['APP_ENV'] = 'testing';
        $_ENV['APP_CONFIG_CACHE'] = $testingConfigCache;
        $_ENV['LOG_CHANNEL'] = 'stack';

        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
