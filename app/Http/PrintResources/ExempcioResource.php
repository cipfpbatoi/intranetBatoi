<?php

namespace Intranet\Http\PrintResources;

use Intranet\Application\Grupo\GrupoService;

/**
 * Prepara les dades automàtiques del nou informe d'exempció d'FE.
 */
class ExempcioResource extends PrintResource
{
    private const CAMP_POBLACIO = 'Tutora del centre educatiu    Tutora del centro educativo';
    private const CAMP_DIA = 'undefined';
    private const CAMP_MES = 'de';

    /**
     * @param mixed $elements
     */
    public function __construct($elements)
    {
        $this->elements = $elements;
        $this->file = 'InformeExencionFCT.pdf';
        $this->flatten = false;
    }

    /**
     * Transforma l'FCT d'alumnat en els camps automàtics del formulari PDF.
     *
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $alumno = $this->elements->Alumno;
        $tutor = authUser();
        $grupo = app(GrupoService::class)->largestByTutor($tutor->dni);
        $telefonoAlumne = $alumno->telef1 !== '' ? $alumno->telef1 : $alumno->telef2;
        $telefonoTutor = $tutor->movil1 !== '' ? $tutor->movil1 : $tutor->movil2;

        return [
            'Text1' => "NIA: $alumno->nia - $alumno->fullName - $telefonoAlumne - $alumno->email",
            'Text2' => config('contacto.nombre') . ' ' . config('contacto.codi'),
            'Text3' => $grupo->Ciclo->vliteral,
            'Text4' => "DNI: $tutor->dni - " . $tutor->fullName . ' - ' . $telefonoTutor . ' - ' . $tutor->email,
            self::CAMP_POBLACIO => config('contacto.poblacion'),
            self::CAMP_DIA => day(hoy()),
            self::CAMP_MES => month(hoy()),
            'Text20' => substr((string) year(hoy()), 2, 2),
        ];
    }
}
