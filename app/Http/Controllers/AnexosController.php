<?php


namespace Intranet\Http\Controllers;


use Illuminate\Support\Facades\Http;
use Intranet\Entities\Articulo;
use Intranet\Entities\ArticuloLote;
use Intranet\Entities\Espacio;
use Intranet\Entities\Material;
use Intranet\Entities\Lote;
use Intranet\Entities\Documento;
use Intranet\Entities\Empresa;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Grupo;
use Intranet\Entities\Menu;
use Intranet\Entities\Poll\Poll;
use Intranet\Entities\Poll\VoteAnt;
use Intranet\Entities\Programacion;
use DB;
use Intranet\Entities\Departamento;
use Styde\Html\Facades\Alert;
use Intranet\Entities\Profesor;
use Illuminate\Support\Facades\Storage;
use Intranet\Jobs\SendEmail;
use Intranet\Entities\AlumnoGrupo;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Poll\Vote;
use Intranet\Entities\Fct;
use Illuminate\Http\Request;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class AnexosController extends Controller
{
    private $token;
    const DIRECTORIO_GESTOR = 'gestor/Empresa/';

    public function login(){
        $user = env('APLSEC_USER','intranet@cipfpbatoi.es');
        $pass =  env('APLSEC_PASS','intr4n3t@B4t01');
        $link = env('APLSEC_LINK','https://matricula.cipfpbatoi.es/api/login_check');
        $response = Http::accept('application/json')->post($link,['username'=>$user,'password'=>$pass]);
        dd($response);
    }
    
}
