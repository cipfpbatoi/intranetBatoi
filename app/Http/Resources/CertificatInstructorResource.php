<?php

namespace Intranet\Http\Resources;

use Intranet\Entities\Profesor;

class CertificatInstructorResource extends ArrayResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function toArray($elements)
    {
        $secretario = Profesor::find(config(fileContactos().'.secretario'));
        $director = Profesor::find(config(fileContactos().'.director'));
        return [
            'untitled1' => $secretario->fullName,
            'untitled13' =>  $secretario->fullName,
            'untitled3' => config('contacto.nombre'),
            'untitled15' => config('contacto.nombre'),
            'untitled5' => config('contacto.codi'),
            'untitled17' => config('contacto.codi'),
            'untitled6' => $elements->Instructor->contacto,
            'untitled18' => $elements->Instructor->contacto,
            'untitled8' => $elements->Instructor->dni,
            'untitled20' => $elements->Instructor->dni,
            'untitled10' => $elements->Colaboracion->Ciclo->vliteral,
            'untitled22' =>  $elements->Colaboracion->Ciclo->cliteral,
            'untitled12' => curso(),
            'untitled24' =>  curso(),
            'untitled25' => $elements->Colaboracion->Centro->Empresa->nombre,
            'untitled26' => $elements->Alumnos->count(),
            'untitled27' => max(800, $elements->AlFct->sum('horas')),
            'untitled28' => config('contacto.poblacion'),
            'untitled29' => day(Hoy()),
            'untitled30' => month(Hoy()),
            'untitled31' => substr(year(Hoy()), 2, 2),
            'untitled32' => $director->fullName,
            'untitled34' => $secretario->fullName
        ];
    }
}

