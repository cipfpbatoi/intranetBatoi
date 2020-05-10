<?php

namespace Intranet\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intranet\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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

    use AuthenticatesUsers;

    
    public function login(Request $request)
    {
        if (isset(AuthUser()->codigo)){
            return redirect('/home');
        }
        if (isset(AuthUser()->nia)){
            return redirect('/alumno/home');
        }
        if (isPrivateAddress(getClientIpAddress())){
            return view('login');
        }
        abort('401',"No estas autoritzat");
    }
    public function externLogin(Request $request,$token)
    {
        session(['token'=>$token]);
        return redirect("/social/google/$token");
    }
}
