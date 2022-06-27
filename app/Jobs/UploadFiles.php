<?php

namespace Intranet\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;


class UploadFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $files,$token,$link;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($files,$token)
    {
        $this->files = $files;
        $this->token = $token;
        $this->link =  env('APLSEC_LINK','https://matricula.cipfpbatoi.es/api/');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $success = 0;
        foreach ($this->files as $document) {
            $success += $this->uploadFile($document);
        }
        if ($success == 2) {
            foreach ($this->files as $document) {
                $document['fct']->a56 = 2;
                $document['fct']->save();
            }
            echo "Archivos de ".$document['fct']->Alumno->shortName." subidos correctamente";
        } else {
            echo "Error al subir los archivos de ".$document['fct']->Alumno->shortName;
        }
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
            echo "Error al subir el archivo $name de ".$document['dni'].":".print_r($response['error']);
            return 0;
        }
    }
}
