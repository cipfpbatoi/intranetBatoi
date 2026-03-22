<?php

namespace Intranet\Providers;

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
            // En arrencada primerenca (tests/CI), no podem assumir logger ni DB disponibles.
            return;
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

    }
}
