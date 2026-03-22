<?php

namespace Intranet\Http\Controllers\Auth\Profesor;

use Intranet\Application\Profesor\ProfesorService;
use Intranet\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Intranet\Services\Auth\ApiSessionTokenService;
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
    private ?ProfesorService $profesorService = null;
    private ?ApiSessionTokenService $apiSessionTokenService = null;

    protected $redirectTo = '/home';

    public function username()
    {
        return 'codigo';
    }

    private function profesores(): ProfesorService
    {
        if ($this->profesorService === null) {
            $this->profesorService = app(ProfesorService::class);
        }

        return $this->profesorService;
    }

    private function apiSessionTokens(): ApiSessionTokenService
    {
        if ($this->apiSessionTokenService === null) {
            $this->apiSessionTokenService = app(ApiSessionTokenService::class);
        }

        return $this->apiSessionTokenService;
    }

    /**
     * Hook del trait AuthenticatesUsers després de login satisfactori.
     */
    protected function authenticated(Request $request, $user): void
    {
        if ($user instanceof Profesor) {
            $this->apiSessionTokens()->issueForProfesor($user, 'web-login');
        }
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
        $this->apiSessionTokens()->revokeCurrentFromSession();

        if (isPrivateAddress(getClientIpAddress())) {
            Auth::guard('profesor')->logout();
            Session()->flush();
            return redirect()->route('login');
        }
        Auth::guard('profesor')->logout();
        Session()->flush();
        return redirect()->to('http://www.cipfpbatoi.es/index.php/ca/principal/')->send();

    }

    public function plogin(Request $request)
    {
        $profesor = $this->profesores()->findByCodigo((string) $request->codigo);
        if (isset($profesor) && !isset($profesor->changePassword)) {
            if ($profesor->dni ==  $request->password) {
                return view('auth/profesor/firstLogin', compact('profesor'));
            } else {
                return back()
                    ->withInput()
                    ->withErrors(['password' => "Has d'introduir el dni amb 0 davant i lletra majuscula"]);
            }
        } else {
            if (!isset($profesor)) {
                $profesor = $this->profesores()->findByEmail((string) $request->codigo);
            }
            if (isset($profesor->idioma)) {
                session(['lang' => $profesor->idioma]);
            }else {
                session(['lang' => 'ca']);
            }

            return $this->login($request);
        }
    }

    public function firstLogin(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'password' => [
                    'required',
                    'confirmed',
                    Password::min(8)->mixedCase()->letters()->numbers()->uncompromised()
                ]
            ]
        );
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        } else {
            $profesor = $this->profesores()->findByCodigo((string) $request->codigo);
            if (!$profesor) {
                return back()->withInput()->withErrors(['codigo' => "Usuari no trobat"]);
            }
            $profesor->email = $request->email;
            $profesor->password = bcrypt(trim($request->password));
            $profesor->changePassword = date('Y-m-d');
            $profesor->save();
            Auth::login($profesor);
            $this->apiSessionTokens()->issueForProfesor($profesor, 'web-first-login');
            session(['lang' => $profesor->idioma]);
            return redirect()->route('home.profesor');
        }

    }
}
