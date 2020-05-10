<?php

namespace Intranet\Http\Controllers\Auth\Social;

use Illuminate\Http\Request;
use Intranet\Entities\Profesor;
use Intranet\Entities\Alumno;
use Auth;
use Socialite;
use Illuminate\Support\Facades\Storage;
use Intranet\Http\Controllers\Controller;


class SocialController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function getSocialAuth($token=null)
    {
        if (!config("services.google"))
            abort('404');
        if (!$token) return Socialite::driver('google')->redirect();
        else{
            $profesor = Profesor::where('api_token',$token)->first();
            if ($profesor) return Socialite::driver('google')->with(["login_hint" => $profesor->email])->redirect();
            abort('404');
        }
    }



    private function checkTokenAndRedirect(Request $request,$user){
        if (!$request->session()->has('token')){
            abort('500',"No hi ha token");
        }
        if ($user->api_token != session('token')) {
            abort('401',"T'has de loguejar amb el teu compte corporatiu");
        }
        return $this->successloginProfesor($user);
    }

    private function successloginProfesor($user){
        Auth::login($user);
        session(['lang' => AuthUser()->idioma]);
        return redirect('/home');
    }

    public function getSocialAuthCallback(Request $request)
    {
        if ($user = Socialite::driver('google')->user()) {
            if ($the_user = Profesor::select()->where('email', $user->email)->first()) {
                if (isPrivateAddress(getClientIpAddress())){
                    return $this->successloginProfesor($the_user);
                }
                return $this->checkTokenAndRedirect($request,$the_user);
            }
            if ($the_user = Alumno::select()->where('email', $user->email)->first()) {
                if (isPrivateAddress(getClientIpAddress())){
                    Auth::guard('alumno')->login($the_user);
                    session(['lang' => AuthUser()->idioma]);
                    return redirect('/alumno/home');
                }
                else {
                    abort('401','Ho sentim però no et pots loguejar des de fora del centre');
                }
            }
            abort('401',"T'has de loguejar amb el teu compte corporatiu");

        } else {
            abort('500','¡¡¡Algo fue mal con google !!!');
        }
    }
    

}
