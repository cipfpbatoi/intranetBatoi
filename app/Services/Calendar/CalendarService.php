<?php

namespace Intranet\Services\Calendar;

use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event;
use DateTime;

class CalendarService
{


    /**
     * @param $id
     * @param string $descripcion
     * @param string $objetivos
     * @return Calendar
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

    public static function render($ini, $fin, $descripcion, $objetivos, $location)
    {
        $vCalendar = new Calendar('intranet.cipfpbatoi.es');
        $vEvent = new Event();
        $vEvent->setDtStart($ini)
            ->setDtEnd($fin)
            ->setLocation($location)
            ->setSummary($descripcion)
            ->setDescription($objetivos);
        $vCalendar->addComponent($vEvent);
        return $vCalendar;
    }
}
