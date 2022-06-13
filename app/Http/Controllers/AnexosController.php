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
    const DIRECTORIO_GESTOR = 'gestor/Empresa/';
    private $user,$pass,$link,$token;


    public function __construct(){
        $this->user = env('APLSEC_USER','intranet@cipfpbatoi.es');
        $this->pass =  env('APLSEC_PASS','intr4n3t@B4t01');
        $this->link =  env('APLSEC_LINK','https://matricula.cipfpbatoi.es/api/');
    }

    public function index(){
        $link = $this->link."application/2021/student/023907686Z/document/10";
        if ($this->login()){
            $response = Http::withToken($this->token)->attach('file',file_get_contents('/var/www/html/intranetBatoi/storage/app/public/adjuntos/alumnofctaval/1294/A5.pdf'))->post($link);
            dd($response);

        } else {
            dd('Sense connexió');
        }


    }

    public function login(){
        $link = $this->link."login_check";

        $response = Http::post($link,['username'=>$this->user,'password'=>$this->pass]);
        if (isset($response['token'])){
            $this->token = $response['token'];
            return 1;
        } else {
            return 0;
        }

    }
    
}
