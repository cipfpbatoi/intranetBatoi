<?php

namespace Intranet\Services;

use mikehaertl\pdftk\Pdf;
use Intranet\Entities\Grupo;

class FDFPrepareService
{
    public static function exec($pdf, $elements)
    {
        $id = authUser()->id;
        $fdf = $pdf->fdf;
        $method = $pdf->method;
        $file = storage_path("tmp/$id/$fdf");
        if (!file_exists($file)) {
            $pdf = new Pdf("fdf/$fdf");
            $pdf->fillform(self::$method($elements))
                ->saveAs($file);
        }
        return $file;
    }

    public static function fullVacances($elements)
    {
        $nomTutor = AuthUser()->fullName;
        $grupo = Grupo::where('tutor', '=', AuthUser()->dni)->first();
        $alumnes = '';
        foreach ($elements as $element) {
            $alumnes .= $element->Alumno->fullName().'\n';
        }
        $array['untitled1'] = $nomTutor.' - '.authUser()->dni;
        $array['untitled2'] = config('contacto.nombre').' '.config('contacto.codi') ;
        $array['untitled3'] = $grupo->Ciclo->vliteral - $grupo->Ciclo->ciclo ;
        $array['untitled4'] = $nomTutor;
        $array['untitled6'] = $nomTutor;
        $array['unitiled26'] = $alumnes;
        $array['untitled27'] = config('contacto.poblacion');
        $array['untitled28'] = day(Hoy());
        $array['untitled29'] = month(Hoy());
        $array['untitled30'] = substr(year(Hoy()), 2, 2);
        $array['untitled34'] = $nomTutor;
        return $array;
    }
}
