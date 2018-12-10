<?php

namespace Intranet\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        require_once base_path() . '/app/Helpers/MyHelpers.php';
        require_once base_path() . '/app/Helpers/DateHelpers.php';
        require_once base_path() . '/app/Helpers/HoraryHelpers.php';
    }

}
