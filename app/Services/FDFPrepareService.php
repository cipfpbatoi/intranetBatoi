<?php

namespace Intranet\Services;

use Intranet\Entities\Profesor;
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
        $array['untitled3'] = $grupo->curso.' '.$grupo->Ciclo->vliteral.' - '.$grupo->Ciclo->ciclo ;
        $array['untitled4'] = $nomTutor;
        $array['untitled6'] = $nomTutor;
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
