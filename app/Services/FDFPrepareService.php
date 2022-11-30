<?php

namespace Intranet\Services;

use mikehaertl\pdftk\Pdf;
use Intranet\Entities\Grupo;
use Exception;

class FDFPrepareService
{
    public static function exec($pdf, $elements)
    {
        $id = authUser()->id;
        $fdf = $pdf['fdf'];
        $method = $pdf['method'];
        $file = storage_path("tmp/{$id}_{$fdf}");
        $array = self::$method($elements);
        if (!file_exists($file)) {
            try {
                $pdf = new Pdf("fdf/$fdf");
                $pdf->fillform($array)
                    ->saveAs($file);
            } catch (Exception $e) {
                dd($e->getMessage(), $pdf, $file, $array);
            }

        }

        return $file;
    }

    public static function fullVacances($elements): array
    {
        $nomTutor = AuthUser()->fullName;
        $dni = AuthUser()->dni;
        $grupo = Grupo::where('tutor', '=', AuthUser()->dni)->first();
        $alumnes = '';
        foreach ($elements as $element) {
            $alumnes .= $element->Alumno->fullName.'
';
        }
        $array['untitled1'] = $nomTutor.' - '.$dni;
        $array['untitled2'] = config('contacto.nombre').' '.config('contacto.codi') ;
        $array['untitled3'] = $grupo->Ciclo->vliteral.' - '.$grupo->Ciclo->ciclo ;
        $array['untitled4'] = $nomTutor;
        $array['untitled6'] = $nomTutor;
        $array['untitled28'] = $alumnes.$nomTutor;
        $array['untitled29'] = config('contacto.poblacion');
        $array['untitled30'] = day(Hoy());
        $array['untitled31'] = month(Hoy());
        $array['untitled32'] = substr(year(Hoy()), 2, 2);
        $array['untitled34'] = $nomTutor;
        return $array;
    }
}
