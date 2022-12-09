<?php

namespace Intranet\Http\PrintResources;

use Intranet\Entities\Profesor;

class CertificatInstructorResource extends PrintResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray()
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
            'untitled6' => $this->getElements()->Instructor->contacto,
            'untitled18' => $this->getElements()->Instructor->contacto,
            'untitled8' => $this->getElements()->Instructor->dni,
            'untitled20' => $this->getElements()->Instructor->dni,
            'untitled10' => $this->getElements()->Colaboracion->Ciclo->vliteral,
            'untitled22' =>  $this->getElements()->Colaboracion->Ciclo->cliteral,
            'untitled12' => curso(),
            'untitled24' =>  curso(),
            'untitled25' => $this->getElements()->Colaboracion->Centro->Empresa->nombre,
            'untitled26' => $this->getElements()->Alumnos->count(),
            'untitled27' => min(800, $this->getElements()->AlFct->sum('horas')),
            'untitled28' => config('contacto.poblacion'),
            'untitled29' => day(Hoy()),
            'untitled30' => month(Hoy()),
            'untitled31' => substr(year(Hoy()), 2, 2),
            'untitled32' => $director->fullName,
            'untitled34' => $secretario->fullName
        ];
    }
}

