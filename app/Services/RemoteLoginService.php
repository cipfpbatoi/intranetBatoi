<?php

namespace Intranet\Services;

use Illuminate\Support\Facades\Http;
use Intranet\Exceptions\IntranetException;

/**
 * Servei RemoteLoginService.
 */
class RemoteLoginService
{
    public static function login($link,$user,$pass)
    {
        $response = Http::post($link.'login_check', [
            'username' => $user,
            'password' => $pass
        ]);

        if (isset($response['token'])) {
            return $response['token'];
        }
        throw new IntranetException('No hi ha connexió amb el servidor de matrícules: '.$response['error']);
    }
}