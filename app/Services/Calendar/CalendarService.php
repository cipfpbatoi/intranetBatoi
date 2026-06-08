<?php

namespace Intranet\Services\Calendar;

use DateTime;

/**
 * Servei de generació d'arxius iCalendar.
 */
class CalendarService
{


    /**
     * @param $id
     * @param string $descripcion
     * @param string $objetivos
     * @return IcsCalendar
     * @throws \Exception
     */
    public static function build($elemento, $descripcion='descripcion', $objetivos='objetivos')
    {
        if (isset($elemento->desde)) {
            $ini =  new DateTime($elemento->desde);
            $fin = new DateTime($elemento->hasta);
        } else {
            $ini = new DateTime($elemento->fecha);
            $fin = new DateTime($elemento->fecha);
            $fin->add(new \DateInterval("PT1H"));
        }
        return self::render(
            $ini,
            $fin,
            ucfirst(getClase($elemento))." : ". $elemento->$descripcion,
            $elemento->$objetivos,
            config('contacto.nombre')
        );

    }

    /**
     * Crea un calendari ICS renderitzable.
     *
     * @param \DateTimeInterface $ini
     * @param \DateTimeInterface $fin
     * @param string $descripcion
     * @param string $objetivos
     * @param string|null $location
     * @return IcsCalendar
     */
    public static function render($ini, $fin, $descripcion, $objetivos, $location)
    {
        return new IcsCalendar(
            $ini,
            $fin,
            (string) $descripcion,
            (string) $objetivos,
            (string) $location
        );
    }
}
