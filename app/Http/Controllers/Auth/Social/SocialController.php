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

    public function getSocialAuth()
    {
        if (!config("services.google"))
            abort('404');
        return Socialite::driver('google')->redirect();
    }

    public function getSocialAuthToken($token)
    {
        if (!config("services.google"))
            abort('404');
        return Socialite::driver('google')->redirect();
    }

    private function checkTokenAndRedirect(Request $request,$user){
        if ($request->session()->has('token') && $user->api_token != session('token')) {
            return redirect()->to('http://www.cipfpbatoi.es/index.php/ca/principal/')->send();
        }
        Auth::login($user);
        session(['lang' => AuthUser()->idioma]);
        return redirect('/home');
    }

    public function getSocialAuthCallback(Request $request)
    {
        if ($user = Socialite::driver('google')->user()) {
            if ($the_user = Profesor::select()->where('emailItaca', $user->email)->first()) {
                return $this->checkTokenAndRedirect($request,$the_user);
            }
            if ($the_user = Profesor::select()->where('email', $user->email)->first()) {
                return $this->checkTokenAndRedirect($request,$the_user);
            }
            if ($the_user = Alumno::select()->where('email', $user->email)->first()) {
                Auth::guard('alumno')->login($the_user);
                session(['lang' => AuthUser()->idioma]);
                return redirect('/alumno/home');
            }
            return redirect('login');

        } else {
            return '¡¡¡Algo fue mal!!!';
        }
    }
    

}
