<?php

namespace Intranet\Providers;

use Illuminate\Support\ServiceProvider;
use Blade;


class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('continue', function() {
            return "<?php continue; ?>";
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

}
