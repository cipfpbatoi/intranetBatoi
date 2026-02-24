<?php

namespace Intranet\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Intranet\Events\EmailAnnexeIndividual;
use Intranet\Listeners\ActualitzarBaseDeDadesDespresEmail;

/**
 * Registre d'esdeveniments de l'aplicacio.
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Illuminate\Auth\Events\Login' => [
            'Intranet\Listeners\LogLastLogin',
        ],
        'Illuminate\Auth\Events\Logout' => [
            'Intranet\Listeners\UpdateLastLoggedAt',
        ],
        'Intranet\Events\ActividadCreated' => [
            'Intranet\Listeners\ResponsableCreate',
        ],
        'Intranet\Events\FctCreated' => [
            'Intranet\Listeners\ColaboracionColabora',
        ],
        'Intranet\Events\FctAlDeleted' => [
            'Intranet\Listeners\FctDelete',
        ],
        'Intranet\Events\GrupoCreated' => [
            'Intranet\Listeners\CoordinadorCreate',
        ],
        'Intranet\Events\ReunionCreated' => [
            'Intranet\Listeners\AsistentesCreate',
        ],
        'Intranet\Events\ActivityReport' => [
            'Intranet\Listeners\RegisterActivity',
        ],
        'Intranet\Events\EmailSended' => [
            'Intranet\Listeners\MarkSended',
        ],
        EmailAnnexeIndividual::class => [
            ActualitzarBaseDeDadesDespresEmail::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
