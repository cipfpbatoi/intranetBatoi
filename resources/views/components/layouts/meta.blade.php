{{-- resources/views/components/layouts/user-meta.blade.php --}}
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
@php
    $user = authUser();
    $apiSessionToken = session('api_access_token');

    if (
        $user instanceof \Intranet\Entities\Profesor
        && (!is_string($apiSessionToken) || $apiSessionToken === '')
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
