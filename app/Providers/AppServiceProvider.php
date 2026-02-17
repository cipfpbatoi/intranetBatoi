<?php

namespace Intranet\Providers;

use Illuminate\Support\ServiceProvider;
use Blade;
use Intranet\Domain\AlumnoFct\AlumnoFctRepositoryInterface;
use Intranet\Domain\Comision\ComisionRepositoryInterface;
use Intranet\Domain\Empresa\EmpresaRepositoryInterface;
use Intranet\Domain\Grupo\GrupoRepositoryInterface;
use Intranet\Domain\Horario\HorarioRepositoryInterface;
use Intranet\Domain\Profesor\ProfesorRepositoryInterface;
use Intranet\Infrastructure\Persistence\Eloquent\AlumnoFct\EloquentAlumnoFctRepository;
use Intranet\Infrastructure\Persistence\Eloquent\Comision\EloquentComisionRepository;
use Intranet\Infrastructure\Persistence\Eloquent\Empresa\EloquentEmpresaRepository;
use Intranet\Infrastructure\Persistence\Eloquent\Grupo\EloquentGrupoRepository;
use Intranet\Infrastructure\Persistence\Eloquent\Horario\EloquentHorarioRepository;
use Intranet\Infrastructure\Persistence\Eloquent\Profesor\EloquentProfesorRepository;


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
        $this->app->bind(AlumnoFctRepositoryInterface::class, EloquentAlumnoFctRepository::class);
        $this->app->bind(ComisionRepositoryInterface::class, EloquentComisionRepository::class);
        $this->app->bind(ProfesorRepositoryInterface::class, EloquentProfesorRepository::class);
        $this->app->bind(HorarioRepositoryInterface::class, EloquentHorarioRepository::class);
        $this->app->bind(GrupoRepositoryInterface::class, EloquentGrupoRepository::class);
        $this->app->bind(EmpresaRepositoryInterface::class, EloquentEmpresaRepository::class);
    }

}
