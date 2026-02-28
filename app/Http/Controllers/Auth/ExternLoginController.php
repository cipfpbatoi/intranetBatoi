<?php
namespace Intranet\Http\Controllers\Auth;


use Illuminate\Http\Request;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Intranet\Entities\Profesor;
use Intranet\Services\Auth\ApiSessionTokenService;

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
    private ?ProfesorService $profesorService = null;
    private ?ApiSessionTokenService $apiSessionTokenService = null;

    protected $redirectTo = '/home';

    public function username()
    {
        return 'api_token';
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user instanceof Profesor) {
            if ($this->apiSessionTokenService === null) {
                $this->apiSessionTokenService = app(ApiSessionTokenService::class);
            }
            $this->apiSessionTokenService->issueForProfesor($user, 'web-extern-login');
        }

        if (isset($user->idioma)) {
            session(['lang' => $user->idioma]);
        } else {
            session(['lang' => 'ca']);
        }
    }

    public function showExternLoginForm($token)
    {
        if ($this->profesorService === null) {
            $this->profesorService = app(ProfesorService::class);
        }
        $professor = $this->profesorService->findByApiToken((string) $token);
        if ($professor && $professor->changePassword) {
            return view('auth/profesor/externLogin', compact('professor'));
        } else {
            return view('errors.401');
        }
    }
}
