<?php

namespace Intranet\Exceptions;


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
        $counter = Counter::firstOrCreate(['name' => 'selenium_exception_count'], ['count' => 0]);
        $counter->increment('count');
        if ($counter->count > 2) {
            $counter->count = 0;
            $counter->save();
            SeleniumService::restartSelenium();
        }
    }
}
