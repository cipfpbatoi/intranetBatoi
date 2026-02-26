<?php

namespace Intranet\Services\UI;

use Styde\Html\Facades\Alert as StydeAlert;

/**
 * Façana pròpia d'alertes de la intranet.
 *
 * Manté la mateixa API estàtica que s'usa al codi legacy (`info`, `warning`,
 * `danger`, `success`, `error`, `message`) i encapsula la dependència de
 * `styde/html` en un únic punt.
 */
class AppAlert
{
    /**
     * Mostra un missatge informatiu.
     *
     * @param string $message
     * @return void
     */
    public static function info(string $message): void
    {
        self::send('info', $message);
    }

    /**
     * Mostra un missatge d'avís.
     *
     * @param string $message
     * @return void
     */
    public static function warning(string $message): void
    {
        self::send('warning', $message);
    }

    /**
     * Mostra un missatge d'error.
     *
     * @param string $message
     * @return void
     */
    public static function danger(string $message): void
    {
        self::send('danger', $message);
    }

    /**
     * Mostra un missatge d'èxit.
     *
     * @param string $message
     * @return void
     */
    public static function success(string $message): void
    {
        self::send('success', $message);
    }

    /**
     * Mostra un missatge d'error (alias de danger per compatibilitat).
     *
     * @param string $message
     * @return void
     */
    public static function error(string $message): void
    {
        self::send('danger', $message);
    }

    /**
     * Mostra un missatge amb nivell explícit.
     *
     * @param string $message
     * @param string $level
     * @return void
     */
    public static function message(string $message, string $level = 'info'): void
    {
        self::send($level, $message);
    }

    /**
     * Encapsula l'enviament real de l'alerta.
     *
     * @param string $level
     * @param string $message
     * @return void
     */
    private static function send(string $level, string $message): void
    {
        $allowedLevels = ['info', 'warning', 'danger', 'success'];
        $normalizedLevel = in_array($level, $allowedLevels, true) ? $level : 'info';

        StydeAlert::{$normalizedLevel}($message);
    }
}
