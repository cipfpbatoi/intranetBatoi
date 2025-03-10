<?php

namespace Intranet\Services;

use Illuminate\Support\Facades\Log;
use Styde\Html\Facades\Alert;

class AlertLogger
{
    public static function info($message,$channel='sao')
    {
        self::log($message, 'info',$channel);
    }

    public static function success($message,$channel='sao')
    {
        self::log($message, 'success',$channel);
    }

    public static function warning($message,$channel='sao')
    {
        self::log($message, 'warning',$channel);
    }

    public static function error($message,$channel='sao')
    {
        self::log($message, 'error',$channel);
    }

    public static function danger($message,$channel='sao')
    {
        self::log($message, 'danger',$channel);
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