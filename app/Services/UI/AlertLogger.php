<?php

namespace Intranet\Services\UI;

use Illuminate\Support\Facades\Log;
use Styde\Html\Facades\Alert;

class AlertLogger
{
    public static function info($message,$channel='sao')
    {
        self::log($message, 'info',$channel);
    }


    public static function warning($message,$channel='sao')
    {
        self::log($message, 'warning',$channel);
    }

    public static function error($message,$channel='sao')
    {
        self::log($message, 'error',$channel);
    }



    private static function log($message, $level,$channel )
    {
        if (app()->runningInConsole()) {
            Log::channel($channel)->$level($message);
        } else {
            Alert::$level($message);
        }
    }
}