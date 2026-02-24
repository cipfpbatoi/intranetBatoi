<?php

namespace Intranet\Http\Controllers\Auth;

use Intranet\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Intranet\Services\HR\FitxatgeService;
use Intranet\Services\Auth\PerfilService;

/**
 * Description of HomeIdentifyController
 *
 * @author igomis
 */
abstract class HomeController extends Controller
{

    protected $guard;

    public function __construct()
    {
        $this->middleware($this->guard);
        
    }

    public function index(FitxatgeService $fitxatgeService, PerfilService $perfilService)
    {
        $usuari = AuthUser();

        if ($this->guard === 'profesor') {
            if ($usuari->dni === '12345678A') {
                return redirect('/fichar');
            }

            if (!$fitxatgeService->isInside($usuari->dni, true) && !Session::get('userChange')) {
                $fitxatgeService->fitxar($usuari->dni);
            }

            $dades = $perfilService->carregarDadesProfessor($usuari->dni);
            return view('home.profile', array_merge(['usuario' => $usuari], $dades));
        }

        // Alumne
        $dades = $perfilService->carregarDadesAlumne($usuari->nia);
        return view('home.alumno', array_merge(['usuario' => $usuari], $dades));
    }


    public function legal()
    {
        return view('intranet.legal');
    }

}
