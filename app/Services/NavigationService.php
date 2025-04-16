<?php

namespace Intranet\Services;


use Illuminate\Support\Facades\Request;

class NavigationService
{
    public static function customBack($default = '/home')
    {
        $history = self::dropFromHistory();
        $url = count($history) > 0 ? end($history) : $default;

        return redirect()->to($url);
    }

    public static function dropFromHistory()
    {
        $history = session('navigation_history', []);

        if (count($history) > 1) {
            array_pop($history);
            session(['navigation_history' => $history]);
        }

        return $history;
    }

    public static function addToHistory()
    {
        if (!Request::isMethod('get')) {
            return;
        }

        $history = session('navigation_history', []);
        $url = Request::fullUrl();

        if (end($history) !== $url) {
            $history[] = $url;
            session(['navigation_history' => $history]);
        }
    }

    public static function getPreviousUrl($default = '/')
    {
        $history = session('navigation_history', []);

        return count($history) > 1
            ? $history[count($history) - 2]
            : $default;
    }
}