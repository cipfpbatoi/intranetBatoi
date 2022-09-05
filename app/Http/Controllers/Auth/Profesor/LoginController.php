<?php

namespace Intranet\Http\Controllers\Auth\Profesor;

use Intranet\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Intranet\Entities\Profesor;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use function PHPUnit\Framework\isNull;

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
        return Auth::guard('profesor');
    }

    public function showLoginForm()
    {
        return view('auth/profesor/login');
    }

    public function logout()
    {
        if (isPrivateAddress(getClientIpAddress())){
            Auth::guard('profesor')->logout();
            Session()->flush();
            return redirect('/login');
        }
        Auth::guard('profesor')->logout();
        Session()->flush();
        return redirect()->to('http://www.cipfpbatoi.es/index.php/ca/principal/')->send();

    }

    public function plogin(Request $request)
    {
        $profesor = Profesor::where('codigo',$request->codigo)->get()->first();
        if (isset($profesor) && !isset($profesor->changePassword)){
            if ($profesor->dni ==  $request->password ) {
                return view('auth/profesor/firstLogin', compact('profesor'));
            } else {
                return back()->withInput()->withErrors(['password' => "Has d'introduir el dni amb 0 davant i lletra majuscula"]);
            }
        } else {
            isset($profesor->idioma)?session(['lang' => $profesor->idioma]):'ca';
            return $this->login($request);
        }
    }

    public function firstLogin(Request $request){

        $validator = Validator::make($request->all(),[
            'password' => ['required','confirmed',Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->uncompromised()]
        ]);
        if ($validator->fails())   //check all validations are fine, if not then redirect and show error messages
        {
            return back()->withInput()->withErrors($validator);
        }
        else
        {
            $profesor = Profesor::where('codigo',$request->codigo)->get()->first();
            $profesor->email = $request->email;
            $profesor->password = bcrypt(trim($request->password));
            $profesor->changePassword = date('Y-m-d');
            $profesor->save();
            Auth::login($profesor);
            session(['lang' => AuthUser()->idioma]);
            return redirect('/home');
        }

    }
}
