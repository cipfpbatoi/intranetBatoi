<?php


namespace Intranet\Http\Controllers;


use Illuminate\Support\Facades\Http;
use DB;
use Intranet\Entities\AlumnoFct;
use Styde\Html\Facades\Alert;


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
                ->attach('file',file_get_contents('/var/www/html/intranetBatoi/storage/app/public/adjuntos/alumnofctaval/1294/A5.pdf'),'A5')
                ->post($link);
            if ($response->code == 200)
        } else {
            Alert::danger('No hi ha connexio');
        }


    }

    private function tipoDocument($title){
        $tipos = ['A5'=>'A5','A6'=>'A6','AVI'=>'A6','AV'=>'A5','AN.VI'=>'A6','AN.V'=>'A5',
            'ANEXO5'=>'A5','ANEXO6'=>'A6','ANNEXVI'=>'A6','ANNEXV'=>'A5'];

        foreach ($tipos as $key => $tipo){
            if (str_contains(strtoupper($title)),$key){
                return $tipos[$key];
            }
        }
        return null;
    }

    public function sendDocuments(){
        foreach (AlumnoFct::where('a56',1)->get() as $fct){
            foreach(Adjunto::where('route','alumnofctaval/'.$fct->id)->where('extension','pdf')->get() as $key => $adjunto){
                $document[$key]['title'] = $this->tipoDocument($adjunto->title);
                $document[$key]['file'] = $adjunto->route;
                $document[$key]['name'] = $adjunto->name;
                $document[$key]['size'] = $adjunto->size;
                $tutor = $adjunto->owner;
            }
            if (count($document) == 2) {
                if (isset($document[0]['title'])&&$document[1]['title']){
                    $this->upload($document);
                } else {
                    if ($document[0]['size'] > $document[1]['size']){
                        $document[0]['title'] = 'A5';
                        $document[1]['title'] = 'A6';
                    } else {
                        $document[0]['title'] = 'A6';
                        $document[1]['title'] = 'A5';
                    }
                    $this->upload($document)  ;
                }
            } else {
                Alert::danger($fct->Alumno->fullName.' no te '.count($document).' documents');
            }
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
