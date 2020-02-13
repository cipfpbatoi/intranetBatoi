<?php

namespace Intranet\Providers;

use Illuminate\Support\ServiceProvider;
//use Laravel\Dusk\DuskServiceProvider;
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
        if ($this->app->environment('local', 'testing')) {
           // $this->app->register(DuskServiceProvider::class);
        }
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Way\Generators\GeneratorsServiceProvider::class);
            $this->app->register(\Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider::class);
        }

    }

}
