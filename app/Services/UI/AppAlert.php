<?php

namespace Intranet\Services\UI;

use Illuminate\Support\HtmlString;

/**
 * Façana pròpia d'alertes de la intranet.
 *
 * Manté la mateixa API estàtica que s'usa al codi legacy (`info`, `warning`,
 * `danger`, `success`, `error`, `message`) i gestiona la persistència en
 * sessió perquè les vistes puguen renderitzar-les sense dependre de Styde.
 */
class AppAlert
{
    private const SESSION_KEY = 'app_alerts';

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
     * Renderitza i buida les alertes pendents de la sessió.
     *
     * @return HtmlString
     */
    public static function render(): HtmlString
    {
        if (!app()->bound('session') || !app('session')->isStarted()) {
            return new HtmlString('');
        }

        $messages = app('session')->pull(self::SESSION_KEY, []);
        if (!is_array($messages) || $messages === []) {
            return new HtmlString('');
        }

        $html = view('components.ui.app-alerts', ['messages' => $messages])->render();
        return new HtmlString($html);
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

        if (!app()->bound('session')) {
            return;
        }

        $session = app('session');
        $messages = $session->get(self::SESSION_KEY, []);
        if (!is_array($messages)) {
            $messages = [];
        }

        $messages[] = [
            'type' => $normalizedLevel,
            'message' => $message,
        ];

        $session->put(self::SESSION_KEY, $messages);
    }
}
