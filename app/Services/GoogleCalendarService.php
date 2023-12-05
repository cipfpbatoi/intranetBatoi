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

    const CALENDAR_CREDENTIALS = 'calendar/google-calendar-cipfpbatoi.json';
    const CALENDAR_ID = 'esdeveniments@cipfpbatoi.es';
    const CALENDAR_APPLICATION_NAME = 'Google Calendar API PHP';
    const CALENDAR_SUBJECT = 'esdeveniments@cipfpbatoi.es';

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig(storage_path(self::CALENDAR_CREDENTIALS));
        $this->client->setScopes(Google_Service_Calendar::CALENDAR);
        $this->client->setApplicationName(self::CALENDAR_APPLICATION_NAME);
        $this->client->setSubject(self::CALENDAR_SUBJECT);
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
                "email" => self::CALENDAR_ID,
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
                $calendar->events->insert(self::CALENDAR_ID, $event);
            } catch (\Exception $e) {
                Alert::danger("No s'ha pogut guardar l'esdeveniment($key) en el calendari:". $e->getMessage());
            }
        }
    }

    public static function listCalendars()
    {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('calendar/google-calendar-cipfpbatoi.json'));
        $client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);

        $service = new Google_Service_Calendar($client);


        $calendarList = $service->calendarList->listCalendarList();

        foreach ($calendarList->getItems() as $calendarListEntry) {
            echo "ID del Calendari: " . $calendarListEntry->getId() . "<br/>";
            echo "Nom del Calendari: " . $calendarListEntry->getSummary() . "<br/>";
        }
    }

    public static function generarTokenAcces()
    {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('calendar/google-calendar-cipfpbatoi.json'));
        $client->setScopes(['https://www.googleapis.com/auth/calendar']);

        if ($client->isAccessTokenExpired()) {
            $client->refreshTokenWithAssertion();
        }

        $accessToken = $client->getAccessToken();
        $token = $accessToken['access_token'];
        return $token;
    }

    // Altres mètodes per a interactuar amb l'API de Calendar...
}
