<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\Falta_profesor;

class FaltaProfesorController extends ApiBaseController
{

    protected $model = 'Falta_profesor';
    
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
