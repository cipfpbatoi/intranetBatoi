<?php

namespace Intranet\Http\Middleware;

use Closure;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;

class SessionTimeout {

    protected $session;
    protected $timeout = 3600;

    public function __construct(Store $session){
        $this->session = $session;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $isLoggedIn = ($request->path() != 'logout' && $request->path() != 'alumno/logout');
        if(! session('lastActivityTime')){
            $this->session->put('lastActivityTime', time());
           
        }
        elseif(time() - $this->session->get('lastActivityTime') > $this->timeout){
            $this->session->forget('lastActivityTime');
            $cookie = cookie('intend', $isLoggedIn ? url()->current() : 'dashboard');
            $usuario = authUser();
            if ($usuario == null) return redirect()->route('login');
            if (isset(authUser()->codigo)){
                Auth::guard('profesor')->logout();
            }
            else {
                Auth::guard('alumno')->logout();
            }
            return redirect()->route('login');
        }
        $isLoggedIn ? $this->session->put('lastActivityTime', time()) : $this->session->forget('lastActivityTime');
        //dd(session('lastActivityTime'));
        return $next($request);
    }

}
