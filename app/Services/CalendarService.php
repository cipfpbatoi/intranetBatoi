<?php

namespace Intranet\Services;

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
            $ini =   Carbon::parseTime($elemento->desde);
            $fin =  Carbon::parseTime($elemento->hasta);
        } else {
            $ini =  Carbon::parseTime($elemento->fecha);
            $fin =  Carbon::parseTime($elemento->fecha);
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
