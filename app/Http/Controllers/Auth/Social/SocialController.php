<?php

namespace Intranet\Http\Controllers\Auth\Social;

use Illuminate\Http\Request;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\Alumno;
use Auth;
use Socialite;
use Intranet\Http\Controllers\Controller;
use Intranet\Services\Auth\ApiSessionTokenService;


class SocialController extends Controller
{
    private ?ProfesorService $profesorService = null;
    private ?ApiSessionTokenService $apiSessionTokenService = null;

    public function __construct()
    {
        $this->middleware('guest');
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

    public function getSocialAuth($token=null)
    {
        if (!config("services.google")) {
            abort('404');
        }
        if (!$token) {
            return Socialite::driver('google')->redirect();
        }
        else{
            $profesor = $this->profesores()->findByApiToken((string) $token);
            if ($profesor) {
                return Socialite::driver('google')->with(["login_hint" => $profesor->email])->redirect();
            }
            abort('404');
        }
    }



    private function checkTokenAndRedirect(Request $request,$user){
        if (!$request->session()->has('token')){
            abort('500',"No hi ha token");
        }
        if ($user->api_token != session('token')) {
            abort('401',"T'has de loguejar amb el teu compte corporatiu des de fora de l'institut");
        }
        return $this->successloginProfesor($user);
    }

    private function successloginProfesor($user){
        Auth::login($user);
        $this->apiSessionTokens()->issueForProfesor($user, 'web-social-login');
        session(['lang' => AuthUser()->idioma]);
        return redirect()->route('home.profesor');
    }

    public function getSocialAuthCallback(Request $request)
    {
        if ($user = Socialite::driver('google')->user()) {
            if ($the_user = $this->profesores()->findByEmail((string) $user->email)) {
                if (isPrivateAddress(getClientIpAddress())){
                    return $this->successloginProfesor($the_user);
                }
                return $this->checkTokenAndRedirect($request,$the_user);
            }
            if ($the_user = Alumno::where('email', $user->email)->first()) {
                if (isPrivateAddress(getClientIpAddress())){
                    Auth::guard('alumno')->login($the_user);
                    session(['lang' => AuthUser()->idioma]);
                    return redirect()->route('home.alumno');
                }
                else {
                    abort('401','Ho sentim però no et pots loguejar des de fora del centre');
                }
            }
            abort('401',"Email no trobat: T'has de loguejar amb el teu compte corporatiu");

        } else {
            abort('500','¡¡¡Algo fue mal con google !!!');
        }
    }
    

}
