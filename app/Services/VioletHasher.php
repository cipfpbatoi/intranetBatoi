<?php

namespace Intranet\Services;

/**
 * Servei VioletHasher.
 */
class VioletHasher
{
    public static function dniHash(string $dni, ?string $pepper = null): string
    {
        $pepper = $pepper ?: config('violetbox.pepper');
        // hash determinista (NO usar Hash::make perquè és aleatori)
        return hash_hmac('sha256', trim(mb_strtoupper($dni)), $pepper);
    }
}

