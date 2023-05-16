<?php

namespace Intranet\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;


class DatabaseConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */

    public function register(): void
    {
        $configurations = DB::table('settings')->pluck('value', 'key')->toArray();

        foreach ($configurations as $key => $value) {
            config([$key => $value]);
        }

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
