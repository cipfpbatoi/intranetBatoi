<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ApiBaseController extends ApiResourceController
{
    protected $rules;

    /**
     * Resol usuari API des del context autenticat (`sanctum`/`api`).
     */
    public function ApiUser(Request $request){
        return $request->user('sanctum') ?? $request->user('api');
    }

    public function show($cadena, $send = true)
    {
        $cadena = (string) $cadena;

        // Compatibilitat: si és un id "normal", usem el show robust del pare.
        if (!$this->isLegacyFilterExpression($cadena)) {
            if ($send) {
                return parent::show($cadena);
            }

            $class = $this->resolveClass();
            return $class::find($cadena);
        }

        $data = $this->queryLegacy($cadena);
        if ($send) {
            return $this->sendResponse($data, 'OK');
        }

        return $data;
    }

    protected function fields($fields)
    {
        $campos = explode(',', $fields);
        $value = [];
        foreach ($campos as $campo) {
            $campo = trim($campo);
            if ($campo !== '') {
                $value[] = $campo;
            }
        }

        $class = $this->resolveClass();
        return empty($value) ? $class::all() : $class::all($value);
    }

    protected function sendFail($error, $code = 400)
    {
        return parent::sendFail($error, $code);
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

    private function queryLegacy(string $cadena): Collection
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
