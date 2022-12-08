<?php

namespace Intranet\Services;

use Intranet\Entities\Profesor;
use mikehaertl\pdftk\Pdf;
use Intranet\Entities\Grupo;
use Exception;

class FDFPrepareService
{
    const RESOURCE = 'Intranet\\Http\\Resources\\';

    public static function exec($pdf, $elements, $stamp=null)
    {
        $resource= self::RESOURCE.$pdf['resource'];
        $id = str_shuffle('abcdeft12');
        $fdf = $pdf['fdf'];
        $nameFile = storage_path("tmp/{$id}_{$fdf}");
        $flatten = $pdf['flatten']??true;
        $pdf = new Pdf("fdf/".$fdf);
        $pdf->fillForm($resource::toArray($elements));

        if ($flatten) {
            $pdf->flatten();
        }

        if (file_exists($nameFile)) {
            unlink($nameFile);
        }
        try {
            if ($stamp) {
                self::stampPDF($pdf, $nameFile, $stamp);
            } else {
                $pdf->saveAs($nameFile);
            }
            return $nameFile;
        }  catch (Exception $e) {
                dd($e->getMessage(), $pdf, $nameFile);
        }
    }

    private static function stampPDF($pdf, $nameFile, $stamp)
    {
        $tmpFileName = storage_path("tmp/".str_shuffle('abcdef12').'.pdf');
        $pdf->saveAs($tmpFileName);
        $tmp = new Pdf($tmpFileName);
        $tmp->stamp("fdf/$stamp")
            ->saveAs($nameFile);
        unlink($tmpFileName);
    }


    public static function certInstructor($fct): array
    {
        $secretario = Profesor::find(config(fileContactos().'.secretario'));
        $director = Profesor::find(config(fileContactos().'.director'));
        $array['untitled1'] = $secretario->fullName;
        $array['untitled13'] =  $array['untitled1'];
        $array['untitled3'] = config('contacto.nombre');
        $array['untitled15'] =  $array['untitled3'];
        $array['untitled5'] = config('contacto.codi');
        $array['untitled17'] =  $array['untitled5'];
        $array['untitled6'] = $fct->Instructor->contacto;
        $array['untitled18'] =  $array['untitled6'];
        $array['untitled8'] = $fct->Instructor->dni;
        $array['untitled20'] =  $array['untitled8'];
        $array['untitled10'] = $fct->Colaboracion->Ciclo->vliteral;
        $array['untitled22'] =  $fct->Colaboracion->Ciclo->cliteral;
        $array['untitled12'] = curso();
        $array['untitled24'] =  curso();
        $array['untitled25'] = $fct->Colaboracion->Centro->Empresa->nombre;
        $alumnes = $fct->Alumnos->count();
        $array['untitled26'] = $alumnes;
        $hores = $fct->AlFct->sum('horas');
        $array['untitled27'] = $hores;
        $array['untitled28'] = config('contacto.poblacion');
        $array['untitled29'] = day(Hoy());
        $array['untitled30'] = month(Hoy());
        $array['untitled31'] = substr(year(Hoy()), 2, 2);
        $array['untitled32'] = $director->fullName;
        $array['untitled34'] = $array['untitled1'];
        return $array;
    }
}
