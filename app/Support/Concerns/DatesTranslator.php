<?php

namespace Intranet\Support\Concerns;

use Illuminate\Support\Carbon;

trait DatesTranslator
{
    public function getCreatedAttribute($date)
    {
        return new Carbon($date);
    }

    public function getUpdatedAttribute($date)
    {
        return new Carbon($date);
    }

    public function getSalidaAttribute($date)
    {
        return new Carbon($date);
    }

    public function getEntradaAttribute($date)
    {
        return new Carbon($date);
    }
}
