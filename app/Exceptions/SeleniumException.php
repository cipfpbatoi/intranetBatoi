<?php

namespace Intranet\Exceptions;


use Illuminate\Support\Facades\Log;
use Intranet\Services\SeleniumService;
use Intranet\Entities\Counter;

class SeleniumException extends \Exception
{
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        // Incrementa el comptador aquí
        $this->incrementCounter();

        // Crida al constructor pare
        parent::__construct($message, $code, $previous);
    }


    private function incrementCounter()
    {
        Log::channel('sao')->info("Selenium Exception incrementing counter");
        $counter = Counter::firstOrCreate(['name' => 'selenium_exception_count'], ['count' => 0]);
        $counter->increment('count');
        if ($counter->count > 1) {
            $counter->count = 0;
            $counter->save();
            SeleniumService::restartSelenium();
        }
    }
}
