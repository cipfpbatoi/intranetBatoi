<?php

namespace Intranet\Support\Concerns;

use Jenssegers\Date\Date;

trait DatesTranslator
{
    public function getCreatedAttribute($date)
    {
        return new Date($date);
    }

    public function getUpdatedAttribute($date)
    {
        return new Date($date);
    }

    public function getSalidaAttribute($date)
    {
        return new Date($date);
    }

    public function getEntradaAttribute($date)
    {
        return new Date($date);
    }
}
