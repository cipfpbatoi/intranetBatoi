<?php
namespace Intranet\Services;

use Illuminate\Support\Facades\Http;



class SecretariaService
{
    protected $link,$user,$pass;
    protected $token;

    public function __construct()
    {
        $this->link = env('APLSEC_LINK','https://matricula.cipfpbatoi.es/api/');
        $this->user = env('APLSEC_USER','intranet@cipfpbatoi.es');
        $this->pass =  env('APLSEC_PASS','intr4n3t@B4t01');
        $this->login();
    }


    public function login(){
        $response = Http::post($this->link.'login_check',[
            'username' => $this->user,
            'password' => $this->pass
        ]);

        if (isset($response['token'])){
            $this->token = $response['token'];
        } else {
            throw new \Exception('No hi ha connexió amb el servidor de matrícules: '.$response['error']);
        }
    }

    public function uploadA56($files){
        foreach ($files as $document) {
            $this->uploadFile($document);
        }
        $files[0]['fct']->a56 = 2;
        $files[0]['fct']->save();
    }

    private function uploadFile($document){

        $curso = substr(curso(),0,4);
        $link = $this->link."application/".$curso."/student/".$document['dni']."/document/".$document['title'];
        $route = storage_path('app/public/adjuntos/'.$document['file'].'/'.$document['name']);
        $name = $document['title'] == 10 ? 'A5.pdf':'A6.pdf';

        $response = Http::withToken($this->token)
            ->attach('file',file_get_contents($route),$name)
            ->post($link);

        if ($response['code'] == 200) {
            return 1;
        } else {
            throw new \Exception('No he pogut carregar el fitxer '.$document['name'].'de la fct '.$document['fct'].' al servidor de matrícules: ');
        }
    }

}