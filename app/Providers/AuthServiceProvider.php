<?php

namespace Intranet\Providers;

use Intranet\Entities\Empresa;
use Intranet\Entities\Fct;
use Intranet\Entities\Incidencia;
use Intranet\Entities\ImportRun;
use Intranet\Entities\Profesor;
use Intranet\Entities\Colaboracion;
use Intranet\Policies\ColaboracionPolicy;
use Intranet\Policies\EmpresaPolicy;
use Intranet\Policies\FctPolicy;
use Intranet\Policies\IncidenciaPolicy;
use Intranet\Policies\ImportRunPolicy;
use Intranet\Policies\ProfesorPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'Intranet\Model' => 'Intranet\Policies\ModelPolicy',
        Empresa::class => EmpresaPolicy::class,
        Fct::class => FctPolicy::class,
        Colaboracion::class => ColaboracionPolicy::class,
        ImportRun::class => ImportRunPolicy::class,
        Incidencia::class => IncidenciaPolicy::class,
        Profesor::class => ProfesorPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();


        Gate::define('manage-bustia-violeta', function () {
            return userIsNameAllow('comissio_IiC');
        });
         
    }
}
