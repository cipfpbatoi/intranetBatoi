<?php

namespace Intranet\Finders\MailFinders;

use Intranet\Entities\Instructor;

/**
 * Recupera els instructors amb alguna FCT dels cicles d'informàtica.
 */
class InstructoresInformaticaFinder extends Finder
{
    /**
     * Codis de cicle inclosos en el col·lectiu.
     *
     * @var array<int, string>
     */
    private const CICLOS = ['DAM', 'DAW', 'ASIX'];

    /**
     * Inicialitza el col·lectiu d'instructors de DAM, DAW i ASIX amb FCT existent.
     *
     * @return void
     */
    public function __construct()
    {
        $this->elements = Instructor::query()
            ->whereHas('Fcts', function ($fctQuery): void {
                $fctQuery->esFct()
                    ->whereHas('Colaboracion.Ciclo', function ($query): void {
                        $query->where(function ($cycleQuery): void {
                            foreach (self::CICLOS as $ciclo) {
                                $cycleQuery->orWhereRaw('UPPER(ciclo) LIKE ?', ['%' . $ciclo . '%'])
                                    ->orWhereRaw('UPPER(acronim) = ?', [$ciclo]);
                            }
                        });
                    });
            })
            ->orderBy('surnames')
            ->orderBy('name')
            ->get();
    }
}
