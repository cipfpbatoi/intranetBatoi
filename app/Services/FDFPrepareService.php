<?php

namespace Intranet\Services;

use Intranet\Entities\Profesor;
use mikehaertl\pdftk\Pdf;
use Intranet\Entities\Grupo;
use Exception;

class FDFPrepareService
{
    public static function exec($pdf, $elements, $stamp=null)
    {
        $id = authUser()->id;
        $fdf = $pdf['fdf'];
        $method = $pdf['method'];
        $flatten = $pdf['flatten']??true;
        $pdf = new Pdf("fdf/".$fdf);
        $array = self::$method($elements);
        $pdf->fillForm($array);
        if ($flatten) {
            $pdf->flatten();
        }
        $nameFile = storage_path("tmp/{$id}_{$fdf}");
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
                dd($e->getMessage(), $pdf, $nameFile, $array);
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

    public static function fullVacances($elements): array
    {
        $nomTutor = AuthUser()->fullName;
        $dni = AuthUser()->dni;
        $grupo = Grupo::where('tutor', '=', AuthUser()->dni)->first();
        $alumnes = '';
        $mes = mes(Hoy());

        foreach ($elements as $element) {
            $alumnes .= $element->Alumno->fullName.'
';
        }
        $array['untitled1'] = $nomTutor.' - '.$dni;
        $array['untitled2'] = config('contacto.nombre').' '.config('contacto.codi') ;
        $array['untitled3'] = $grupo->curso.' '.$grupo->Ciclo->vliteral.' - '.$grupo->Ciclo->ciclo ;
        $array['untitled4'] = $nomTutor;
        $array['untitled6'] = $nomTutor;
        $array['untitled8'] = 'Yes';
        $array['untitled17'] = 'Yes';
        if ($mes <=4) {
            $array['untitled11'] = 'Yes';
            $array['untitled20'] = 'Yes';
        } elseif ($mes<=8) {
            $array['untitled12'] = 'Yes';
            $array['untitled21'] = 'Yes';
        } else {
            $array['untitled10'] = 'Yes';
            $array['untitled19'] = 'Yes';
        }
        $array['untitled28'] = $alumnes;
        $array['untitled29'] = config('contacto.poblacion');
        $array['untitled30'] = day(Hoy());
        $array['untitled31'] = month(Hoy());
        $array['untitled32'] = substr(year(Hoy()), 2, 2);
        $array['untitled34'] = $nomTutor;
        return $array;
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
