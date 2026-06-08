<?php

namespace Intranet\Services\Calendar;

use DateTimeInterface;
use DateTimeZone;

/**
 * Representa un calendari iCalendar senzill amb un únic esdeveniment.
 */
class IcsCalendar
{
    /**
     * @param DateTimeInterface $start Data d'inici de l'esdeveniment.
     * @param DateTimeInterface $end Data de finalització de l'esdeveniment.
     * @param string $summary Resum visible de l'esdeveniment.
     * @param string $description Descripció de l'esdeveniment.
     * @param string $location Ubicació de l'esdeveniment.
     */
    public function __construct(
        private readonly DateTimeInterface $start,
        private readonly DateTimeInterface $end,
        private readonly string $summary,
        private readonly string $description,
        private readonly string $location
    ) {
    }

    /**
     * Renderitza el contingut ICS.
     */
    public function render(): string
    {
        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//intranet.cipfpbatoi.es//Intranet Batoi//CA',
            'CALSCALE:GREGORIAN',
            'BEGIN:VEVENT',
            'UID:' . md5($this->summary . $this->formatDate($this->start) . $this->formatDate($this->end)) . '@intranet.cipfpbatoi.es',
            'DTSTAMP:' . gmdate('Ymd\THis\Z'),
            'DTSTART:' . $this->formatDate($this->start),
            'DTEND:' . $this->formatDate($this->end),
            'SUMMARY:' . $this->escape($this->summary),
            'DESCRIPTION:' . $this->escape($this->description),
            'LOCATION:' . $this->escape($this->location),
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        return implode("\r\n", $lines) . "\r\n";
    }

    /**
     * Formata una data en UTC per a iCalendar.
     */
    private function formatDate(DateTimeInterface $date): string
    {
        $utc = (new \DateTimeImmutable($date->format('Y-m-d H:i:s'), $date->getTimezone()))
            ->setTimezone(new DateTimeZone('UTC'));

        return $utc->format('Ymd\THis\Z');
    }

    /**
     * Escapa text segons les regles bàsiques d'iCalendar.
     */
    private function escape(string $value): string
    {
        return str_replace(
            ["\\", ';', ',', "\r\n", "\n", "\r"],
            ["\\\\", '\;', '\,', '\n', '\n', '\n'],
            $value
        );
    }
}
