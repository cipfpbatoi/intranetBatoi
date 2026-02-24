<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\Espacio;
use Intranet\Entities\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class   ReservaController extends ApiResourceController
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
    
    public function show($id)
    {
        $cadena = (string) $id;
        $isLegacy = $this->isLegacyFilterExpression($cadena);
        $data = $isLegacy
            ? $this->queryLegacy($cadena)
            : collect([Reserva::findOrFail($cadena)]);

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
                // LLEGIR estat actual i decidir acció
                $data = $this->getJson($espacio->dispositivo);
                $secured = $this->checkSecuredStatus($data); // true = tancada/asegurada
                $action = $secured ? 'unsecure' : 'secure';

                if ($this->action($action, $espacio)) {
                    return $this->sendResponse('Modificat estat Porta');
                }
                return $this->sendError("No s'ha pogut modificar la porta");
            } catch (\Throwable $e) {
                return $this->sendError("Error consultant el dispositiu: ".$e->getMessage(), 500);
            }
        }

        // Si no hi ha reserva en l'hora actual, intenta almenys tancar la porta de la reserva d'avui
        $reserva = Reserva::where('idProfesor', $datosProfesor->dni)
            ->where('dia', Hoy())
            ->first();

        if ($reserva && $espacio = Espacio::find($reserva->idEspacio)) {
            if (!$espacio->dispositivo) {
                return $this->sendError('Eixe espai no te obertura', 401);
            }
            if ($this->action('secure', $espacio)) {
                return $this->sendResponse('Porta Tancada');
            }
            return $this->sendError("No s'ha pogut tancar la porta");
        }

        return $this->sendError('No tens cap reserva per ara', 401);
    }


    private function getJson($dispositivo)
    {
        $user = config('variables.domotica.user');
        $pass = config('variables.domotica.pass');
        $link = 'http://172.16.10.74/api/devices/'.$dispositivo;

        // Llança excepció si no és 2xx, així no tractem HTML/JSON d’error com si fora vàlid
        $response = Http::withBasicAuth($user, $pass)
            ->acceptJson()
            ->get($link)
            ->throw();

        return $response->json(); // array|mixed
    }

    private function action($action, $espacio): bool
    {
        $user = config('variables.domotica.user');
        $pass =  config('variables.domotica.pass');
        $link = str_replace(
            '{dispositivo}',
            $espacio->dispositivo,
            config('variables.ipDomotica')
            )."/".$action;
        $response = Http::withBasicAuth($user, $pass)
            ->accept('application/json')
            ->post($link, ['args'=>[]]);
        return $response->successful()?true:false;
    }

    private function checkSecuredStatus($data): bool
    {
        // Seguretat per si no venen les claus
        $secured = $data['properties']['secured'] ?? null;
        return (is_numeric($secured) ? ((int)$secured) > 0 : (bool)$secured);
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
