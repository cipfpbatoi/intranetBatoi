<?php

namespace Intranet\Providers;

use Intranet\Entities\Empresa;
use Intranet\Entities\Fct;
use Intranet\Entities\Falta;
use Intranet\Entities\Actividad;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Comision;
use Intranet\Entities\Departamento;
use Intranet\Entities\Curso;
use Intranet\Entities\Articulo;
use Intranet\Entities\IpGuardia;
use Intranet\Entities\Lote;
use Intranet\Entities\Menu;
use Intranet\Entities\Modulo_ciclo;
use Intranet\Entities\Resultado;
use Intranet\Entities\Solicitud;
use Intranet\Entities\Cotxe;
use Intranet\Entities\Expediente;
use Intranet\Entities\Signatura;
use Intranet\Entities\TutoriaGrupo;
use Intranet\Entities\Incidencia;
use Intranet\Entities\ImportRun;
use Intranet\Entities\MaterialBaja;
use Intranet\Entities\Profesor;
use Intranet\Entities\Reunion;
use Intranet\Entities\Espacio;
use Intranet\Entities\TipoActividad;
use Intranet\Entities\TipoIncidencia;
use Intranet\Entities\Poll\Option;
use Intranet\Entities\Poll\PPoll;
use Intranet\Entities\Documento;
use Intranet\Entities\Task;
use Intranet\Entities\Setting;
use Intranet\Entities\Colaboracion;
use Intranet\Policies\ColaboracionPolicy;
use Intranet\Policies\ActividadPolicy;
use Intranet\Policies\CicloPolicy;
use Intranet\Policies\ComisionPolicy;
use Intranet\Policies\CursoPolicy;
use Intranet\Policies\ArticuloPolicy;
use Intranet\Policies\DepartamentoPolicy;
use Intranet\Policies\DocumentoPolicy;
use Intranet\Policies\EmpresaPolicy;
use Intranet\Policies\FctPolicy;
use Intranet\Policies\FaltaPolicy;
use Intranet\Policies\IncidenciaPolicy;
use Intranet\Policies\ImportRunPolicy;
use Intranet\Policies\MaterialBajaPolicy;
use Intranet\Policies\IpGuardiaPolicy;
use Intranet\Policies\LotePolicy;
use Intranet\Policies\MenuPolicy;
use Intranet\Policies\ModuloCicloPolicy;
use Intranet\Policies\ResultadoPolicy;
use Intranet\Policies\SolicitudPolicy;
use Intranet\Policies\CotxePolicy;
use Intranet\Policies\ExpedientePolicy;
use Intranet\Policies\SignaturaPolicy;
use Intranet\Policies\TutoriaGrupoPolicy;
use Intranet\Policies\ProfesorPolicy;
use Intranet\Policies\ReunionPolicy;
use Intranet\Policies\EspacioPolicy;
use Intranet\Policies\TaskPolicy;
use Intranet\Policies\SettingPolicy;
use Intranet\Policies\TipoActividadPolicy;
use Intranet\Policies\TipoIncidenciaPolicy;
use Intranet\Policies\OptionPolicy;
use Intranet\Policies\PPollPolicy;
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
        Falta::class => FaltaPolicy::class,
        Ciclo::class => CicloPolicy::class,
        Departamento::class => DepartamentoPolicy::class,
        Curso::class => CursoPolicy::class,
        Articulo::class => ArticuloPolicy::class,
        IpGuardia::class => IpGuardiaPolicy::class,
        Lote::class => LotePolicy::class,
        Resultado::class => ResultadoPolicy::class,
        Solicitud::class => SolicitudPolicy::class,
        Cotxe::class => CotxePolicy::class,
        Expediente::class => ExpedientePolicy::class,
        Signatura::class => SignaturaPolicy::class,
        TutoriaGrupo::class => TutoriaGrupoPolicy::class,
        Menu::class => MenuPolicy::class,
        Modulo_ciclo::class => ModuloCicloPolicy::class,
        PPoll::class => PPollPolicy::class,
        Option::class => OptionPolicy::class,
        Colaboracion::class => ColaboracionPolicy::class,
        ImportRun::class => ImportRunPolicy::class,
        Incidencia::class => IncidenciaPolicy::class,
        MaterialBaja::class => MaterialBajaPolicy::class,
        Profesor::class => ProfesorPolicy::class,
        Reunion::class => ReunionPolicy::class,
        Espacio::class => EspacioPolicy::class,
        Documento::class => DocumentoPolicy::class,
        TipoActividad::class => TipoActividadPolicy::class,
        TipoIncidencia::class => TipoIncidenciaPolicy::class,
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
