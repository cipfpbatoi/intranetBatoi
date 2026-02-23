<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\Falta_profesor;

class FaltaProfesorController extends ApiResourceController
{

    protected $model = 'Falta_profesor';

    public function index()
    {
        $request = request();
        $filters = $this->extractQueryFilters($request);
        if (empty($filters)) {
            return parent::index();
        }

        return $this->sendResponse(
            $this->queryByRequestFilters($filters),
            'OK'
        );
    }

    public function show($id)
    {
        $cadena = (string) $id;

        if ($this->isLegacyFilterExpression($cadena)) {
            return $this->markLegacyUsage(
                $this->sendResponse($this->queryByLegacyConditions($cadena), 'OK'),
                'faltaProfesor.show.filter-path',
                '/api/faltaProfesor?dia=YYYY-MM-DD'
            );
        }

        return parent::show($cadena);
    }
    
    public function horas($cadena)
    {
        $result = $this->queryByLegacyConditions((string) $cadena);
        $dias = [];
        foreach ($result as $registro) {
            if ($registro->salida !== null) {
                if (isset($dias[$registro->idProfesor][$registro->dia])) {
                    $dias[$registro->idProfesor][$registro->dia]['horas'] = sumarHoras($dias[$registro->idProfesor][$registro->dia]['horas'], restarHoras($registro->entrada, $registro->salida));
                } else {
                    $dias[$registro->idProfesor][$registro->dia] = ['idProfesor' => $registro->idProfesor, 'fecha' => $registro->dia, 'horas' =>
                        restarHoras($registro->entrada, $registro->salida)];
                }
            } else {
                if (isset($dias[$registro->idProfesor][$registro->dia])) {
                    $dias[$registro->idProfesor][$registro->dia]['horas'] = sumarHoras($dias[$registro->idProfesor][$registro->dia]['horas'], "01:00:00");
                } else {
                    $dias[$registro->idProfesor][$registro->dia] = ['idProfesor' => $registro->idProfesor, 'fecha' => $registro->dia, 'horas' => '01:00:00'];
                }
            }
        }

        return $this->sendResponse($dias, 'OK');
    }

    private function queryByLegacyConditions(string $cadena)
    {
        $query = Falta_profesor::query();
        $filters = explode('&', $cadena);

        foreach ($filters as $filter) {
            $this->applyLegacyCondition($query, $filter);
        }

        return $query->get();
    }

    private function queryByRequestFilters(array $filters)
    {
        $query = Falta_profesor::query();
        foreach ($filters as $field => $value) {
            $query->where((string) $field, '=', (string) $value);
        }

        return $query->get();
    }

    private function extractQueryFilters(Request $request): array
    {
        return array_filter(
            $request->query(),
            static fn ($value): bool => $value !== null && $value !== ''
        );
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

    private function applyLegacyCondition($query, string $filter): void
    {
        foreach (['=', '<', '>', ']', '[', '!'] as $operator) {
            $parts = explode($operator, $filter, 2);
            if (count($parts) !== 2) {
                continue;
            }

            $field = trim($parts[0]);
            $value = trim($parts[1]);
            if ($field === '') {
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
