<?php

namespace Intranet\Providers;

use Intranet\Entities\Empresa;
use Intranet\Entities\Fct;
use Intranet\Entities\Actividad;
use Intranet\Entities\Comision;
use Intranet\Entities\Incidencia;
use Intranet\Entities\ImportRun;
use Intranet\Entities\Profesor;
use Intranet\Entities\Reunion;
use Intranet\Entities\Documento;
use Intranet\Entities\Task;
use Intranet\Entities\Setting;
use Intranet\Entities\Colaboracion;
use Intranet\Policies\ColaboracionPolicy;
use Intranet\Policies\ActividadPolicy;
use Intranet\Policies\ComisionPolicy;
use Intranet\Policies\DocumentoPolicy;
use Intranet\Policies\EmpresaPolicy;
use Intranet\Policies\FctPolicy;
use Intranet\Policies\IncidenciaPolicy;
use Intranet\Policies\ImportRunPolicy;
use Intranet\Policies\ProfesorPolicy;
use Intranet\Policies\ReunionPolicy;
use Intranet\Policies\TaskPolicy;
use Intranet\Policies\SettingPolicy;
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
        Reunion::class => ReunionPolicy::class,
        Documento::class => DocumentoPolicy::class,
        Actividad::class => ActividadPolicy::class,
        Comision::class => ComisionPolicy::class,
        Task::class => TaskPolicy::class,
        Setting::class => SettingPolicy::class,
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
