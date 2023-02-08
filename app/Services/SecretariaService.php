<?php
namespace Intranet\Services;

use Illuminate\Support\Facades\Http;
use Intranet\Exceptions\IntranetException;


class SecretariaService
{
    protected $link;
    protected $user;
    protected $pass;
    protected $token;

    public function __construct()
    {
        $this->link = env('APLSEC_LINK', 'https://matricula.cipfpbatoi.es/api/');
        $this->user = env('APLSEC_USER', 'intranet@cipfpbatoi.es');
        $this->pass =  env('APLSEC_PASS', 'intr4n3t@B4t01');
        $this->login();
    }


    public function login()
    {
        $response = Http::post($this->link.'login_check', [
            'username' => $this->user,
            'password' => $this->pass
        ]);

        if (isset($response['token'])) {
            $this->token = $response['token'];
        } else {
            throw new IntranetException('No hi ha connexió amb el servidor de matrícules: '.$response['error']);
        }
    }

    private function error($response)
    {
        $ret = '';
        if (is_array($response)) {
            foreach ($response as $key => $error) {
                $ret .= $key.': '.$error.',';
            }
        } else {
            $ret = $response;
        }
        return $ret;
    }

    public function uploadFile($document)
    {
        $curso = substr(curso(), 0, 4);
        $link = $this->link."application/".$curso."/student/".$document['dni']."/document/".$document['title'];
        $route = storage_path($document['route']);

        $response = Http::withToken($this->token)
            ->attach('file', file_get_contents($route), $document['name'])
            ->post($link);

        if ($response['code'] == 200) {
            return 1;
        } else {
            throw new IntranetException(
                "No he pogut carregar el fitxer ".$document['name']." de la fct de l'alumne".
                $document['alumne'].' situat al fitxer: '.$route.' al servidor de matrícules: '.
                $this->error($response['error'])
            );
        }
    }
}
