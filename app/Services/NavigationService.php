<?php

namespace Intranet\Services;

use Illuminate\Support\Facades\Session;
use Intranet\Componentes\Mensaje;

class NavigationService
{
    public static function customBack($default = '/home')
    {
        $history = self::dropFromHistory();
        // Si hi ha història, treu l'última entrada (la pàgina actual)
        $url = count($history) > 0 ? end($history) : $default;
        return redirect()->to($url);
    }

    public static function dropFromHistory(){
        $history = session('navigation_history', []);
        // Elimina l'última entrada (si existeix) perquè l'usuari està "tornant enrere"
        if (count($history) > 1) {
            array_pop($history); // Elimina l'actual
            session(['navigation_history' => $history]);
        }
        return $history;
    }

    public static function addToHistory($url)
    {
        $history = session('navigation_history', []);
        // Evita duplicats consecutius
        if (end($history) !== $url) {
            $history[] = $url;
            session(['navigation_history' => $history]);
        }

    }



    public static function getPreviousUrl($default = '/')
    {
        $history = session('navigation_history', []);
        // No modifiquem l'array, només obtenim la penúltima URL si és possible
        if (count($history) > 1) {
            $url = $history[count($history) - 2]; // Penúltima entrada com la pàgina anterior
        } else {
            $url = $default;
        }

        return $url;
    }
}
