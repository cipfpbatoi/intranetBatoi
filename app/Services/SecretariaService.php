<?php
namespace Intranet\Services;

use Illuminate\Support\Facades\Http;
use Intranet\Exceptions\IntranetException;


class SecretariaService
{
    protected $link;
    protected $token;

    public function __construct()
    {
        $this->link = env('APLSEC_LINK', 'https://matricula.cipfpbatoi.es/api/');
        $user = env('APLSEC_USER', 'intranet@cipfpbatoi.es');
        $pass =  env('APLSEC_PASS', 'intr4n3t@B4t01');
        $this->token = RemoteLoginService::login($this->link,$user,$pass);
    }


    public function uploadFile($document)
    {
        try {
            $curso = substr(curso(), 0, 4);
            $link = $this->link."application/".$curso."/student/".$document['dni']."/document/".$document['title'];
            $route = storage_path($document['route']);

            $response = Http::withToken($this->token)
                ->attach('file', file_get_contents($route), $document['name'])
                ->post($link);

            if ($response['code'] == 200) {
                return 1;
            }

        } catch (\Exception $e) {
            throw new IntranetException(
                "No he pogut carregar el fitxer ".$document['name']." de la fct de l'alumne".
                $document['alumne'].' situat al fitxer: '.$route.' al servidor de matrícules: '.
                'Petició: '.$link.' Error: '.$e->getMessage()
            );
        }
        throw new IntranetException(
            "No he pogut carregar el fitxer ".$document['name']." de la fct de l'alumne".
            $document['alumne'].' situat al fitxer: '.$route.' al servidor de matrícules: '.
            'Petició: '.$link.' Resposta: '.$response['code'].' Error: '.
            $this->error($response['error'])
        );
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
}
