<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\Espacio;
use Intranet\Entities\Reserva;
use Intranet\Exceptions\NotFoundDomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Controlador API de reserves.
 */
class ReservaController extends ApiResourceController
{

    protected $model = 'Reserva';
    private ?ProfesorService $profesorService = null;

    public function index()
    {
        $request = request();
        $filters = $this->extractQueryFilters($request);
        if (empty($filters) && !$request->has('fields')) {
            return parent::index();
        }

        $data = $this->queryByRequestFilters($filters, $request->query('fields'));
        foreach ($data as $uno) {
            if (isset($uno->Profesor->nombre)) {
                $uno->nomProfe = $uno->Profesor->ShortName;
            }
        }

        return $this->sendResponse($data, 'OK');
    }

    private function profesores(): ProfesorService
    {
        if ($this->profesorService === null) {
            $this->profesorService = app(ProfesorService::class);
        }

        return $this->profesorService;
    }
    
    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $cadena = (string) $id;
        $isLegacy = $this->isLegacyFilterExpression($cadena);
        $data = $isLegacy
            ? $this->queryLegacy($cadena)
            : $this->singleReservaAsCollection($cadena);

        foreach ($data as $uno) {
            if (isset($uno->Profesor->nombre)) {
                $uno->nomProfe = $uno->Profesor->ShortName;
            }
        }
        $response = $this->sendResponse($data, 'OK');
        if ($isLegacy) {
            return $this->markLegacyUsage(
                $response,
                'reserva.show.filter-path',
                '/api/reserva?idEspacio=...&dia=...'
            );
        }

        return $response;
    }

    /**
     * @param string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Support\Collection
     */
    private function singleReservaAsCollection(string $id)
    {
        return collect([
            $this->findModelOrFail(Reserva::class, $id, 'Reserva no trobada', ['reserva_id' => $id]),
        ]);
    }


    public function unsecure(Request $datosProfesor)
    {
        $profesor = $this->profesores()->find((string) $datosProfesor->dni);
        if (!$profesor || $datosProfesor->api_token !== $profesor->api_token) {
            return $this->sendError('Persona no identificada', 401);
        }

        $reserva = Reserva::where('idProfesor', $datosProfesor->dni)
            ->where('dia', Hoy())
            ->where('hora', sesion(hora()))
            ->first();

        if ($reserva && $espacio = Espacio::find($reserva->idEspacio)) {
            if (!$espacio->dispositivo) {
                return $this->sendError('Eixe espai no te obertura', 401);
            }

            try {
                if ($this->runOpenScene()) {
                    return $this->sendResponse('S\'ha enviat la senyal d\'obertura de porta');
                }
                return $this->sendError("No s'ha pogut modificar la porta");
            } catch (\Throwable $e) {
                return $this->sendError("Error consultant el dispositiu: ".$e->getMessage(), 500);
            }
        }

        // Si no hi ha reserva activa, no fem tancar, només retornem error per seguretat.
        $reserva = Reserva::where('idProfesor', $datosProfesor->dni)
            ->where('dia', Hoy())
            ->first();

        if (!$reserva) {
            return $this->sendError('No tens cap reserva per ara', 401);
        }

        if ($espacio = Espacio::find($reserva->idEspacio)) {
            if (!$espacio->dispositivo) {
                return $this->sendError('Eixe espai no te obertura', 401);
            }
            return $this->sendError("La reserva no està activa en aquest moment; utilitza-la a l'hora programada", 401);
        }

        return $this->sendError('No tens cap reserva per ara', 401);
    }

    /**
     * Executa la escena de Fibaro que envia la senyal per obrir la porta.
     *
     * @return bool
     */
    private function runOpenScene(): bool
    {
        $user = (string) config('variables.domotica.user');
        $pass = (string) config('variables.domotica.pass');
        $sceneId = (int) config('variables.domotica.openSceneId', 111);
        $host = rtrim((string) config('variables.domotica.host', 'http://172.16.10.74'), '/');
        $link = $host.'/api/scenes/'.$sceneId.'/execute';

        $response = Http::withBasicAuth($user, $pass)
            ->accept('application/json')
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($link, []);

        return $response->successful();
    }

    private function isLegacyFilterExpression(string $cadena): bool
    {
        foreach (['=', '>', '<', ']', '[', '!'] as $operator) {
            if (strpos($cadena, $operator) !== false) {
                return true;
            }
        }

        return false;
    }

    private function queryLegacy(string $cadena)
    {
        $class = $this->resolveClass();
        $filters = explode('&', $cadena);
        $query = $class::query();
        $fields = null;

        foreach ($filters as $filter) {
            [$field, $value] = array_pad(explode('=', $filter, 2), 2, null);
            if ($field === 'fields') {
                $fields = $value;
                continue;
            }
            $this->applyLegacyCondition($query, $filter);
        }

        if ($fields !== null) {
            $selectedFields = array_values(array_filter(array_map('trim', explode(',', $fields))));
            if (!empty($selectedFields)) {
                $query->select($selectedFields);
            }
        }

        return $query->get();
    }

    private function extractQueryFilters(Request $request): array
    {
        return array_filter(
            $request->query(),
            static fn ($value, $field): bool => $field !== 'fields' && $value !== null && $value !== '',
            ARRAY_FILTER_USE_BOTH
        );
    }

    private function queryByRequestFilters(array $filters, $fields = null)
    {
        $class = $this->resolveClass();
        $query = $class::query();

        foreach ($filters as $field => $value) {
            $query->where((string) $field, '=', (string) $value);
        }

        if (is_string($fields) && $fields !== '') {
            $selectedFields = array_values(array_filter(array_map('trim', explode(',', $fields))));
            if (!empty($selectedFields)) {
                $query->select($selectedFields);
            }
        }

        return $query->get();
    }

    private function applyLegacyCondition($query, string $filter): void
    {
        foreach (['=', '<', '>', ']', '[', '!'] as $operator) {
            $parts = explode($operator, $filter, 2);
            if (count($parts) !== 2) {
                continue;
            }

            $field = trim($parts[0]);
            $value = trim($parts[1]);
            if ($field === '' || $field === 'fields') {
                return;
            }

            if ($value === 'null') {
                if ($operator === '=') {
                    $query->whereNull($field);
                    return;
                }
                if ($operator === '!') {
                    $query->whereNotNull($field);
                    return;
                }
            }

            $sqlOperator = match ($operator) {
                ']' => '>=',
                '[' => '<=',
                '!' => '!=',
                default => $operator,
            };

            $query->where($field, $sqlOperator, $value);
            return;
        }
    }
}
