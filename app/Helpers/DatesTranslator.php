<?php

namespace Intranet\Helpers;

use Carbon\Carbon;

trait DatesTranslator
{

    public function getCreatedAttribute($date)
    {
        return  Carbon::parse($date);
    }

    public function getUpdatedAttribute($date)
    {
        return  Carbon::parse($date);
    }

    public function getSalidaAttribute($date)
    {
        return  Carbon::parse($date);
    }

    public function getEntradaAttribute($date)
    {
        return  Carbon::parse($date);
    }

}

// use app\Helpers\DatesTranslator
// use DatesTranslator
