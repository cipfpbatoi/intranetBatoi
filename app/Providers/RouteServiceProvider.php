<?php

namespace Intranet\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'Intranet\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->profesorRoutes();
        $this->todosRoutes();
        $this->direccionRoutes();
        $this->adminRoutes();
        $this->alumnoRoutes();
        $this->consergeRoutes();
        $this->mantenimientoRoutes();
        $this->jefeRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
                ], function ($router) {
            require base_path('routes/public.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace . "\\API",
            'prefix' => 'api',
            'as' => 'api.',
                ], function ($router) {
            require base_path('routes/api.php');
        });
    }

    /**
     * Define the "auth" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function profesorRoutes()
    {
        Route::group([
            'middleware' => ['web','role:profesor'],
            'namespace' => $this->namespace,
                ], function ($router) {
            require base_path('routes/profesor.php');
        });
    }
    protected function adminRoutes()
    {
        Route::group([
            'middleware' => ['web','role:administrador'],
            'namespace' => $this->namespace,
                ], function ($router) {
            require base_path('routes/administrador.php');
        });
    }
    protected function todosRoutes()
    {
        Route::group([
            'middleware' => ['web','role:todos'],
            'namespace' => $this->namespace,
                ], function ($router) {
            require base_path('routes/todos.php');
        });
    }
    protected function consergeRoutes()
    {
        Route::group([
            'middleware' => ['web','role:conserge'],
            'namespace' => $this->namespace,
                ], function ($router) {
            require base_path('routes/conserge.php');
        });
    }
    protected function direccionRoutes()
    {
        Route::group([
            'middleware' => ['web','role:direccion'],
            'namespace' => $this->namespace,
            'prefix' => 'direccion'
                ], function ($router) {
            require base_path('routes/direccion.php');
        });
    }

    protected function alumnoRoutes()
    {
        Route::group([
            'middleware' => ['web','role:alumno'],
            'namespace' => $this->namespace,
            'prefix' => 'alumno'
                ], function ($router) {
            require base_path('routes/alumno.php');
        });
    }
    protected function mantenimientoRoutes()
    {
        Route::group([
            'middleware' => ['web','role:mantenimiento'],
            'namespace' => $this->namespace,
            'prefix' => 'mantenimiento'
                ], function ($router) {
            require base_path('routes/mantenimiento.php');
        });
    }
    protected function jefeRoutes()
    {
        Route::group([
            'middleware' => ['web','role:jefe_dpto'],
            'namespace' => $this->namespace,
            'prefix' => 'depto'
                ], function ($router) {
            require base_path('routes/jefeDpto.php');
        });
    }

}
