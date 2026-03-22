<?php

declare(strict_types=1);

namespace Intranet\Presentation\AlumnoFct;

use Intranet\Entities\AlumnoFct;
use Illuminate\Support\Carbon;

/**
 * Presenter de UI per a AlumnoFct.
 *
 * Encapsula decisions visuals (p. ex. classe CSS de color) fora del model.
 */
final class AlumnoFctPresenter
{
    private const BG_PURPLE = 'bg-purple';
    private const BG_ORANGE = 'bg-orange';
    private const BG_BLUE_SKY = 'bg-blue-sky';
    private const BG_GREEN = 'bg-green';

    public function __construct(private readonly AlumnoFct $alumnoFct)
    {
    }

    /**
     * Retorna la classe CSS de fons segons estat/temporalitat del registre.
     */
    public function cssClass(): string
    {
        return match ($this->alumnoFct->asociacion) {
            2 => self::BG_PURPLE,
            3 => self::BG_ORANGE,
            default => $this->backgroundByDates(),
        };
    }

    public function centerName(int $length = 30): string
    {
        return substr((string) ($this->alumnoFct->Fct?->Centro ?? ''), 0, $length);
    }

    public function studentShortName(): string
    {
        return (string) ($this->alumnoFct->Alumno?->ShortName ?? '');
    }

    public function studentNameWithMinorIcon(): string
    {
        $name = $this->studentShortName();
        if ($name === '' || !$this->alumnoFct->Alumno?->esMenorEdat($this->alumnoFct->desde)) {
            return $name;
        }

        return $name . "<em class='fa fa-child'></em>";
    }

    public function remainingPracticeTimeLabel(): string
    {
        if (!$this->alumnoFct->horas_diarias) {
            return '??';
        }

        $dies = ($this->alumnoFct->horas - $this->alumnoFct->realizadas) / $this->alumnoFct->horas_diarias;
        return floor($dies / 5) . ' Setmanes - ' . ($dies % 5) . ' Dia';
    }

    public function contactName(): string
    {
        return (string) ($this->alumnoFct->Alumno?->NameFull ?? '');
    }

    public function fullName(): string
    {
        return (string) ($this->alumnoFct->Alumno?->fullName ?? '');
    }

    public function completedHoursLabel(): string
    {
        return $this->alumnoFct->realizadas . '/' . $this->alumnoFct->horas . ' ' . $this->alumnoFct->actualizacion;
    }

    public function instructorName(int $length = 30): string
    {
        return substr((string) ($this->alumnoFct->Fct?->XInstructor ?? ''), 0, $length);
    }

    public function printableId(): string
    {
        return (string) $this->alumnoFct->idFct . '-' . $this->studentShortName();
    }

    private function backgroundByDates(): string
    {
        $today = Carbon::now();
        $fechaHasta = new Carbon((string) $this->alumnoFct->hasta);
        $fechaDesde = new Carbon((string) $this->alumnoFct->desde);

        if ($fechaHasta->format('Y-m-d') <= $today->format('Y-m-d')) {
            return self::BG_BLUE_SKY;
        }

        if ($this->alumnoFct->adjuntos && $fechaDesde->format('Y-m-d') > $today->format('Y-m-d')) {
            return self::BG_GREEN;
        }

        return '';
    }
}
