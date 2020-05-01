<?php

namespace Intranet\Http\Controllers\Auth\Profesor;

use Intranet\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Intranet\Entities\Profesor;

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

    protected $redirectTo = '/home';

    public function username()
    {
        return 'codigo';
    }

    protected function guard()
    {
        return Auth::guard('profesor');
    }

    public function showLoginForm()
    {
        return view('auth/profesor/login');
    }

    public function logout()
    {
        Auth::guard('profesor')->logout();
        Session()->flush();
        dd(isPrivateAddress(getClientIpAddress()));
        if (isPrivateAddress(getClientIpAddress()))
            return redirect('/login');
        return redirect()->to('https://www.google.es')->send();

    }

    public function plogin(Request $request)
    {
        isset(Profesor::where('codigo',$request->codigo)->get()->first()->idioma)?session(['lang' => Profesor::where('codigo',$request->codigo)->get()->first()->idioma]):'ca';
        return $this->login($request);
        
    }
}
