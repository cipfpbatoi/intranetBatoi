<?php

namespace Intranet\Http\Controllers\Auth\Alumno;

use Intranet\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Intranet\Entities\Alumno;
use Illuminate\Http\Request;

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

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/alumno/home';



    public function username()
    {
        return 'nia';
    }

    protected function credentials(Request $request)
    {
        // the value in the 'email' field in the request
        $username = $request->get($this->username());

        // check if the value is a validate email address and assign the field name accordingly
        $field = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : $this->username();

        // return the credentials to be used to attempt login
        return [
            $field => $request->get($this->username()),
            'password' => $request->password,
        ];
    }

    protected function guard()
    {
        return Auth::guard('alumno');
    }

    public function showLoginForm()
    {
        return view('/auth/alumno/login');
    }

    public function logout()
    {
        Auth::guard('alumno')->logout();
        return redirect()->route('login');
    }
    public function plogin(Request $request)
    {
        isset(Alumno::where('nia',$request->nia)->get()->first()->idioma)?session(['lang' => Alumno::where('nia',$request->nia)->get()->first()->idioma]):'ca';
        return $this->login($request);
    }

}
