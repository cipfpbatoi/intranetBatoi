<?php
namespace Intranet\Http\Controllers\Auth;


use Illuminate\Http\Request;
use Intranet\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Intranet\Entities\Profesor;

class ExternLoginController extends Controller
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
        return 'api_token';
    }

    protected function authenticated(Request $request, $user)
    {
        if (isset($user->idioma)) {
            session(['lang' => $user->idioma]);
        } else {
            session(['lang' => 'ca']);
        }
    }

    public function showExternLoginForm($token)
    {
        $professor = Profesor::where('api_token', $token)->first();
        if ($professor && $professor->changePassword) {
            return view('auth/profesor/externLogin', compact('professor'));
        } else {
            return view('errors.401');
        }
    }
}
