<?php

namespace Intranet\Providers;

use Blade;
use Collective\Html\FormBuilder as CollectiveFormBuilder;
use Collective\Html\HtmlBuilder;
use Illuminate\Support\ServiceProvider;
use Intranet\Services\UI\FieldBuilder;
use Intranet\Domain\AlumnoFct\AlumnoFctRepositoryInterface;
use Intranet\Domain\Comision\ComisionRepositoryInterface;
use Intranet\Domain\Empresa\EmpresaRepositoryInterface;
use Intranet\Domain\Expediente\ExpedienteRepositoryInterface;
use Intranet\Domain\Fct\FctRepositoryInterface;
use Intranet\Domain\FaltaProfesor\FaltaProfesorRepositoryInterface;
use Intranet\Domain\Grupo\GrupoRepositoryInterface;
use Intranet\Domain\Horario\HorarioRepositoryInterface;
use Intranet\Domain\Profesor\ProfesorRepositoryInterface;
use Intranet\Infrastructure\Persistence\Eloquent\AlumnoFct\EloquentAlumnoFctRepository;
use Intranet\Infrastructure\Persistence\Eloquent\Comision\EloquentComisionRepository;
use Intranet\Infrastructure\Persistence\Eloquent\Empresa\EloquentEmpresaRepository;
use Intranet\Infrastructure\Persistence\Eloquent\Expediente\EloquentExpedienteRepository;
use Intranet\Infrastructure\Persistence\Eloquent\Fct\EloquentFctRepository;
use Intranet\Infrastructure\Persistence\Eloquent\FaltaProfesor\EloquentFaltaProfesorRepository;
use Intranet\Infrastructure\Persistence\Eloquent\Grupo\EloquentGrupoRepository;
use Intranet\Infrastructure\Persistence\Eloquent\Horario\EloquentHorarioRepository;
use Intranet\Infrastructure\Persistence\Eloquent\Profesor\EloquentProfesorRepository;

/**
 * Proveidor principal de serveis de l'aplicació.
 */
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

        HtmlBuilder::macro('classes', function ($classes): string {
            if (!is_array($classes)) {
                $classes = func_get_args();
            }

            $compiled = [];

            foreach ($classes as $name => $enabled) {
                if (is_int($name)) {
                    $name = (string) $enabled;
                    $enabled = true;
                }

                if ($enabled) {
                    $className = trim((string) $name);
                    if ($className !== '') {
                        $compiled[] = $className;
                    }
                }
            }

            if (empty($compiled)) {
                return '';
            }

            return ' class="'.e(implode(' ', $compiled)).'"';
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('field', function ($app): FieldBuilder {
            $fieldBuilder = new FieldBuilder(
                $app->make(CollectiveFormBuilder::class),
                $app['translator'],
                $app['view']
            );

            $theme = (string) config('html.theme', 'bootstrap');
            $themeConfig = (array) config("html.themes.$theme", []);
            $fieldBuilder->setAbbreviations((array) config('html.abbreviations', []));
            $fieldBuilder->setCssClasses((array) ($themeConfig['field_classes'] ?? []));
            $fieldBuilder->setTemplates((array) ($themeConfig['field_templates'] ?? []));

            return $fieldBuilder;
        });
        $this->app->alias('field', FieldBuilder::class);

        $this->app->bind(AlumnoFctRepositoryInterface::class, EloquentAlumnoFctRepository::class);
        $this->app->bind(ComisionRepositoryInterface::class, EloquentComisionRepository::class);
        $this->app->bind(ProfesorRepositoryInterface::class, EloquentProfesorRepository::class);
        $this->app->bind(HorarioRepositoryInterface::class, EloquentHorarioRepository::class);
        $this->app->bind(GrupoRepositoryInterface::class, EloquentGrupoRepository::class);
        $this->app->bind(EmpresaRepositoryInterface::class, EloquentEmpresaRepository::class);
        $this->app->bind(ExpedienteRepositoryInterface::class, EloquentExpedienteRepository::class);
        $this->app->bind(FaltaProfesorRepositoryInterface::class, EloquentFaltaProfesorRepository::class);
        $this->app->bind(FctRepositoryInterface::class, EloquentFctRepository::class);
    }

}
