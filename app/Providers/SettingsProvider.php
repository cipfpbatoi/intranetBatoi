<?php

namespace Intranet\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class SettingsProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        try {
            if (Schema::hasTable('settings')) {
                $registres = \DB::table('settings')->get();

                foreach ($registres as $registre) {
                    config([$registre->collection.'.'.$registre->key => $registre->value]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('No s\'ha pogut carregar la taula settings en arrencar', [
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

    }
}
