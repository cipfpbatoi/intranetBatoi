<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\Guardia;

class GuardiaController extends ApiResourceController
{
    protected $model = 'Guardia';

    public function show($id)
    {
        $cadena = (string) $id;

        // Pont legacy per al format actual del frontend:
        // /api/guardia/dia]YYYY-MM-DD&dia[YYYY-MM-DD
        if (preg_match('/^dia\]([^&]+)&dia\[([^&]+)$/', $cadena, $matches) === 1) {
            $data = $this->queryByDiaRange((string) $matches[1], (string) $matches[2]);
            return $this->markLegacyUsage(
                $this->sendResponse($data, 'OK'),
                'guardia.show.range-path',
                '/api/guardia/range?desde=YYYY-MM-DD&hasta=YYYY-MM-DD'
            );
        }

        // Manté compatibilitat amb show legacy d'ApiBaseController
        if ($this->isLegacyFilterExpression($cadena)) {
            return $this->markLegacyUsage(
                $this->sendResponse($this->queryLegacy($cadena), 'OK'),
                'guardia.show.filter-path',
                '/api/guardia/range?desde=...&hasta=...'
            );
        }

        return parent::show($cadena);
    }

    public function range(Request $request)
    {
        $desde = (string) ($request->query('desde', $request->input('desde')) ?? '');
        $hasta = (string) ($request->query('hasta', $request->input('hasta')) ?? '');

        if ($desde === '' || $hasta === '') {
            return $this->sendFail(['success' => false, 'message' => 'Falten paràmetres: desde i hasta'], 422);
        }

        return $this->sendResponse($this->queryByDiaRange($desde, $hasta), 'OK');
    }

    public function getServerTime()
    {
        return response()->json([
            'date' => now()->toDateString(),
            'time' => now()->toTimeString(),
        ]);
    }

    private function queryByDiaRange(string $desde, string $hasta)
    {
        return Guardia::query()
            ->whereBetween('dia', [$desde, $hasta])
            ->get();
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
