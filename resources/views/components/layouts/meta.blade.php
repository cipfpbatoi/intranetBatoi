{{-- resources/views/components/layouts/user-meta.blade.php --}}
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="app-locale" content="{{ app()->getLocale() }}">
@php
    $user = authUser();
    $apiSessionToken = session('api_access_token');
    $needsNewApiSessionToken = !is_string($apiSessionToken) || $apiSessionToken === '';

    if (!$needsNewApiSessionToken) {
        try {
            $storedToken = \Laravel\Sanctum\PersonalAccessToken::findToken($apiSessionToken);
            $needsNewApiSessionToken = $storedToken === null
                || ($storedToken->expires_at !== null && $storedToken->expires_at->isPast());
        } catch (\Throwable $exception) {
            $needsNewApiSessionToken = true;
        }
    }

    if (
        $user instanceof \Intranet\Entities\Profesor
        && $needsNewApiSessionToken
    ) {
        try {
            $apiSessionToken = app(\Intranet\Services\Auth\ApiSessionTokenService::class)
                ->issueForProfesor($user, 'web-session-bootstrap');
        } catch (\Throwable $exception) {
            $apiSessionToken = null;
        }
    }
@endphp

<meta name="user-dni" content="{{ $user->dni }}">
<meta name="user-rol" content="{{ $user->rol }}">
@if (is_string($apiSessionToken) && $apiSessionToken !== '')
    <meta name="user-bearer-token" content="{{ $apiSessionToken }}">
@endif
