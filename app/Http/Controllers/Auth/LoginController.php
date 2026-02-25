<?php

namespace Intranet\Http\Controllers\Auth;

use Intranet\Http\Controllers\Controller;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    protected $redirectTo = '/home';


    public function login()
    {
        if (isset(AuthUser()->codigo)) {
            return redirect()->route('home.profesor');
        }
        if (isset(AuthUser()->nia)) {
            return redirect()->route('home.alumno');
        }
        if (isPrivateAddress(getClientIpAddress())) {
            return view('login');
        }
        abort('401', "No estas autoritzat");
    }

}
