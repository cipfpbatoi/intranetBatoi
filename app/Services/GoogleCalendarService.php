<?php

namespace Intranet\Services;

use DateTime;
use Google_Client;
use Google_Service_Calendar;
use Styde\Html\Facades\Alert;

class GoogleCalendarService
{
    protected $client;
    protected $events;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig(storage_path(config('services.calendar.calendarCredentialsPath')));
        $this->client->setScopes(Google_Service_Calendar::CALENDAR);
        $this->client->setApplicationName(config('services.calendar.calendarApplicationName'));
        $this->client->setSubject(config('services.calendar.calendarSubject'));
        if ($this->client->isAccessTokenExpired()) {
            $this->client->refreshTokenWithAssertion();
        }
        $this->client->getAccessToken();
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getCalendar()
    {
        return new Google_Service_Calendar($this->client);
    }

    public function addEvent($title, $description, $start, $end,$attendees = [])
    {
        $this->events[$title] = new \Google_Service_Calendar_Event([
            'summary' => $title,
            'description' => $description,
            'start' => [
                'dateTime' => self::dateToGoogle($start),
                'timeZone' => 'Europe/Madrid',
            ],
            'end' => [
                'dateTime' => self::dateToGoogle($end),
                'timeZone' => 'Europe/Madrid',
            ],
            'attendees' => $attendees,
            "creator"=> array(
                "email" => config('services.calendar.calendarSubject'),
                "displayName" => "CIPFP BATOI",
                "self"=> true
            ),
            "guestsCanInviteOthers" => false,
            "guestsCanModify" => false,
            "guestsCanSeeOtherGuests" => false,
        ]);
    }

    private static function dateToGoogle($dataHoraOriginal)
    {
        $dataHoraObjecte = DateTime::createFromFormat("d-m-Y H:i", $dataHoraOriginal);
        return  $dataHoraObjecte->format(DateTime::ATOM);
    }

    public function saveEvents()
    {
        $calendar = $this->getCalendar();
        foreach ($this->events as $key => $event) {
            try {
                $calendar->events->insert(config('services.calendar.calendarId'), $event);
                Alert::info("S'ha guardat l'esdeveniment($key) en el calendari");
            } catch (\Exception $e) {
                Alert::danger("No s'ha pogut guardar l'esdeveniment($key) en el calendari:". $e->getMessage());
            }
        }
    }

}
