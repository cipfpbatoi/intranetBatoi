<?php


namespace Intranet\Http\Controllers;


use Illuminate\Support\Facades\Http;
use DB;



/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class AnexosController extends Controller
{
    private $user,$pass,$link,$token;


    public function __construct(){
        $this->user = env('APLSEC_USER','intranet@cipfpbatoi.es');
        $this->pass =  env('APLSEC_PASS','intr4n3t@B4t01');
        $this->link =  env('APLSEC_LINK','https://matricula.cipfpbatoi.es/api/');
    }

    public function index(){
        $link = $this->link."application/2021/student/023907686Z/document/10";
        if ($this->login()){
            $response = Http::withToken($this->token)
                ->attach('file',file_get_contents('/var/www/html/intranetBatoi/storage/app/public/adjuntos/alumnofctaval/1294/A5.pdf'))
                ->post($link);

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
