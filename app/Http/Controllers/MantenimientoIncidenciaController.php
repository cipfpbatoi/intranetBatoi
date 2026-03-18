<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Incidencia;
use Intranet\Entities\OrdenTrabajo;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Traits\Autorizacion;

/**
 * Controller de flux de manteniment per a incidències.
 *
 * Separa les transicions d'estat i la vinculació amb ordres de treball
 * del CRUD general de professorat.
 */
class MantenimientoIncidenciaController extends Controller
{
    use Autorizacion {
        accept as protected traitAccept;
        resign as protected traitResign;
        resolve as protected traitResolve;
        refuse as protected traitRefuse;
    }

    /**
     * @var string
     */
    protected $perfil = 'profesor';

    /**
     * @var string
     */
    protected $model = 'Incidencia';

    /**
     * Genera o associa una ordre de treball a una incidència.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generarOrden($id)
    {
        Gate::authorize('viewAny', Incidencia::class);

        $incidencia = $this->findIncidenciaOrFail($id);

        $orden = OrdenTrabajo::where('tipo', $incidencia->tipo)
            ->where('estado', 0)
            ->where('idProfesor', $this->currentProfesorDni())
            ->first();

        if (!$orden) {
            $orden = $this->generateOrder($incidencia);
        }

        $incidencia->orden = $orden->id;
        $incidencia->save();

        if ((int) $incidencia->estado === 1) {
            return $this->accept($id);
        }

        Session::put('pestana', $incidencia->estado);
        return back();
    }

    /**
     * Desvincula l'ordre de treball d'una incidència.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeOrden($id)
    {
        Gate::authorize('viewAny', Incidencia::class);

        $incidencia = $this->findIncidenciaOrFail($id);
        $incidencia->orden = null;
        $incidencia->save();

        return back();
    }

    /**
     * Accepta la incidència dins del flux de manteniment.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function accept($id, $redirect = true)
    {
        Gate::authorize('viewAny', Incidencia::class);

        return $this->traitAccept($id, $redirect);
    }

    /**
     * Retrocedix la incidència dins del flux de manteniment.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function resign($id, $redirect = true)
    {
        Gate::authorize('viewAny', Incidencia::class);

        return $this->traitResign($id, $redirect);
    }

    /**
     * Resol la incidència dins del flux de manteniment.
     *
     * @param Request $request
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function resolve(Request $request, $id, $redirect = true)
    {
        Gate::authorize('viewAny', Incidencia::class);

        return $this->traitResolve($request, $id, $redirect);
    }

    /**
     * Refusa la incidència dins del flux de manteniment.
     *
     * @param Request $request
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function refuse(Request $request, $id, $redirect = true)
    {
        Gate::authorize('viewAny', Incidencia::class);

        return $this->traitRefuse($request, $id, $redirect);
    }

    /**
     * Busca una incidència o falla amb context.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return Incidencia
     */
    private function findIncidenciaOrFail($id): Incidencia
    {
        return $this->findModelOrFail(
            Incidencia::class,
            $id,
            'Incidència no trobada',
            ['incidencia_id' => $id]
        );
    }

    /**
     * Crea una nova ordre de treball a partir d'una incidència.
     *
     * @param Incidencia $incidencia
     * @return OrdenTrabajo
     */
    private function generateOrder(Incidencia $incidencia): OrdenTrabajo
    {
        $dni = $this->currentProfesorDni();
        $user = AuthUser();

        $orden = new OrdenTrabajo();
        $orden->idProfesor = $dni;
        $orden->estado = 0;
        $orden->tipo = $incidencia->tipo;
        $orden->descripcion =
            'Ordre oberta el dia ' . Hoy() . ' pel profesor '
            . ($user->FullName ?? $user->fullName ?? $dni)
            . ' relativa a ' . $incidencia->Tipos->literal;
        $orden->save();

        return $orden;
    }

    /**
     * Retorna el DNI del professor autenticat.
     *
     * @return string
     */
    private function currentProfesorDni(): string
    {
        $user = AuthUser();
        abort_unless(is_object($user) && isset($user->dni) && (string) $user->dni !== '', 403);

        return (string) $user->dni;
    }
}
