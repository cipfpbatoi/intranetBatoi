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
                    ->updateInfo()
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
        $array['untitled2'] =  $array['untitled1'];
        $array['untitled5'] = config(fileContactos().'.nombre');
        $array['untitled6'] =  $array['untitled5'];
        $array['untitled7'] = config(fileContactos().'.codi');
        $array['untitled8'] =  $array['untitled7'];
        $array['untitled9'] = $fct->Instructor->fullName;
        $array['untitled10'] =  $array['untitled9'];
        $array['untitled11'] = $fct->Instructor->dni;
        $array['untitled12'] =  $array['untitled11'];
        $array['untitled15'] = $fct->Colaboracion->Ciclo->vliteral;
        $array['untitled16'] =  $fct->Colaboracion->Ciclo->cliteral;
        $array['untitled19'] = curso();
        $array['untitled20'] =  curso();
        $array['untitled21'] = $fct->Colaboracion->Centro->Empresa->nombre;
        $alumnes = $fct->Alumnos->count();
        $array['untitled22'] = $alumnes;
        $hores = $fct->AlFct->sum('horas');
        $array['untitled23'] = $hores;
        $array['untitled24'] = config('contacto.poblacion');
        $array['untitled25'] = day(Hoy());
        $array['untitled26'] = month(Hoy());
        $array['untitled27'] = substr(year(Hoy()), 2, 2);
        $array['untitled28'] = $director->fullName;
        $array['untitled28'] = $array['untitled1'];

        dd($array);
    }
}
