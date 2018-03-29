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

    public function getSocialAuthCallback()
    {
        if ($user = Socialite::driver('google')->user()) {
            if ($the_user = Profesor::select()->where('emailItaca', $user->email)->first()) {
                Auth::login($the_user);
                session(['lang' => AuthUser()->idioma]);
            } else {
                if ($the_user = Profesor::select()->where('email', $user->email)->first()) {
                    Auth::login($the_user);
                    session(['lang' => AuthUser()->idioma]);
                } else {
                    if ($the_user = Alumno::select()->where('email', $user->email)->first()) {
                        Auth::guard('alumno')->login($the_user);
                        session(['lang' => AuthUser()->idioma]);
                        return redirect('/alumno/home');
                    }
                    else return redirect('login');
                }
            }
            return redirect('/home');
        } else {
            return '¡¡¡Algo fue mal!!!';
        }
    }
    

}
