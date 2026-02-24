<?php

namespace Intranet\Providers;

use Intranet\Entities\Empresa;
use Intranet\Entities\Fct;
use Intranet\Policies\EmpresaPolicy;
use Intranet\Policies\FctPolicy;
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
