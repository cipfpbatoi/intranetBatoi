<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Application\Horario\HorarioService;
use Intranet\Application\Profesor\ProfesorService;
use Illuminate\Support\Facades\Storage;

class HorarioController extends ApiResourceController
{

    protected $model = 'Horario';

    private ?HorarioService $horarioService = null;
    private ?ProfesorService $profesorService = null;

    private function horarios(): HorarioService
    {
        if ($this->horarioService === null) {
            $this->horarioService = app(HorarioService::class);
        }

        return $this->horarioService;
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
        if (!$this->isLegacyFilterExpression($cadena)) {
            return parent::show($cadena);
        }

        return $this->markLegacyUsage(
            $this->sendResponse($this->queryLegacy($cadena), 'OK'),
            'horario.show.filter-path',
            '/api/horario?idProfesor=...'
        );
    }

    public function index()
    {
        $request = request();
        $filters = $this->extractQueryFilters($request);
        if (empty($filters) && !$request->has('fields')) {
            return parent::index();
        }

        return $this->sendResponse(
            $this->queryByRequestFilters($filters, $request->query('fields')),
            'OK'
        );
    }

    public function guardia($idProfesor)
    {
        return $this->sendResponse($this->horarios()->guardiaAllByProfesor((string) $idProfesor), 'OK');
    }
    
    public function HorariosDia($fecha)
    {
        $data = [];
        $profes = $this->profesores()->activos();
        foreach ($profes as $profe) {
            $horario = $this->horarios()->primeraByProfesorAndDateOrdered((string) $profe->dni, (string) $fecha);
            if (isset($horario->first()->desde)) {
                $data[$profe->dni] = $horario->first()->desde . " - " . $horario->last()->hasta;
            } else {
                $data[$profe->dni] = '';
            }
        }
        return $this->sendResponse($data, 'OK');
    }
    
    public function getChange($dni)
    {
        if (Storage::disk('local')->exists('/horarios/'.$dni.'.json')) {
            if ($data = Storage::disk('local')->get('/horarios/'.$dni.'.json')) {
                return $this->sendResponse($data, 'OK');
            } else {
                return $this->sendError('No hi han canvis');
            }
        } else {
            return $this->sendError('No hi ha fitxer');
        }
    }
    
    public function Change(Request $request, $dni)
    {
        if (Storage::disk('local')->put('/horarios/'.$dni.'.json', $request->data)) {
            return $this->sendResponse('Guardado Correctament', 'OK');
        } else {
            return $this->sendError('No se ha podido guardar');
        }
        
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
        $query = $class::query();
        $filters = explode('&', $cadena);
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
            $field = (string) $field;
            $value = (string) $value;

            if ($field === 'idProfesor') {
                $sustituto = $this->profesores()->find($value)?->sustituye_a;
                $query->where(function ($q) use ($field, $value, $sustituto): void {
                    $q->where($field, $value);
                    if ($sustituto !== null && trim((string) $sustituto) !== '') {
                        $q->orWhere($field, (string) $sustituto);
                    }
                });
                continue;
            }

            $query->where($field, '=', $value);
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

            if ($field === 'idProfesor' && $operator === '=') {
                $sustituto = $this->profesores()->find((string) $value)?->sustituye_a;
                $query->where(function ($q) use ($field, $value, $sustituto): void {
                    $q->where($field, $value);
                    if ($sustituto !== null && trim((string) $sustituto) !== '') {
                        $q->orWhere($field, (string) $sustituto);
                    }
                });
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
