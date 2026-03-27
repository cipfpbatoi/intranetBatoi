<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida l'edició d'un resultat.
 */
class ResultadoUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $evaluacionRule = 'required|composite_unique:resultados,idModuloGrupo,evaluacion';
        if (($resultadoId = $this->currentResultadoId()) !== null) {
            $evaluacionRule .= ',' . $resultadoId;
        }

        return [
            'idModuloGrupo' => 'required',
            'evaluacion' => $evaluacionRule,
            'matriculados' => 'required|numeric|max:60',
            'evaluados' => 'required|numeric|lte:matriculados',
            'aprobados' => 'required|numeric|lte:evaluados',
            'udProg' => 'required|numeric|max:30',
            'udImp' => 'required|numeric|lte:udProg',
        ];
    }

    /**
     * Recupera l'identificador del resultat editat per excloure'l de la unicitat composta.
     *
     * @return string|null
     */
    private function currentResultadoId(): ?string
    {
        $resultado = $this->route('resultado');

        if (is_object($resultado) && method_exists($resultado, 'getKey')) {
            $resultado = $resultado->getKey();
        }

        if ($resultado === null || $resultado === '') {
            return null;
        }

        return (string) $resultado;
    }
}
