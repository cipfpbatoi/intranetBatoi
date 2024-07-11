<?php

namespace Intranet\Console\Commands;

use Illuminate\Console\Command;
use Intranet\Entities\Adjunto;
use Intranet\Entities\AlumnoFct;
use Intranet\Exceptions\IntranetException;
use Intranet\Jobs\UploadFiles;
use Intranet\Services\FDFPrepareService;
use Intranet\Services\SecretariaService;
use Styde\Html\Facades\Alert;

class UploadAnexes extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:Anexe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pujar anexes V';



    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $sService = new SecretariaService();
        } catch (\Exception $e) {
            echo 'No hi ha connexió amb el servidor de matrícules';
            exit();
        }
        $correctos = 0;
        $incorrectos = 0;
        foreach (AlumnoFct::where('a56', 1)->where('beca', 0)->get() as $fct) {
            $document = array();
            $fcts = $this->buscaDocuments($fct, $document);
            sleep(1);
            if (isset($document['route'])) {
                try {
                    $sService->uploadFile($document);
                    foreach ($fcts as $fct1) {
                        $fct1->a56 = 2;
                        $fct1->save();
                    }
                    $correctos++;
                } catch (IntranetException $e) {
                    $incorrectos++;
                    echo $fct->Alumno->shortName.' '.$e->getMessage().PHP_EOL;
                }
            }
        }
        echo 'Correctos: '.$correctos.' Incorrectos: '.$incorrectos.PHP_EOL;
    }


    /**
     * @param $fct
     * @param  array  &$document
     * @return string
     */
    private function buscaDocuments($fct, array &$document)
    {
        $document['title'] = 10;
        $document['dni'] = $fct->Alumno->dni;
        $document['alumne'] = trim($fct->Alumno->shortName);


        $fcts = AlumnoFct::where('idAlumno', $fct->idAlumno)->where('a56', '>', 0)->get(); //mira tots els de l'alumne

        foreach ($fcts as $fct1) {  // cerque els adjunts
            $adjuntos[] = Adjunto::where('route', 'alumnofctaval/'.$fct1->id)
                ->where('extension', 'pdf')
                ->orderBy('created_at', 'desc')
                ->get()
                ->first();
        }

        if (count($adjuntos) == 1) { // si soles hi ha un
            $document['route'] =
                'app/public/adjuntos/'.
                $adjuntos[0]->route.'/'.
                $adjuntos[0]->title.'.'.$adjuntos[0]->extension;
            $document['name'] = $adjuntos[0]->title.'.'.$adjuntos[0]->extension;
            $document['size'] = $adjuntos[0]->size;

        } else {
            $size = 0;
            $files = array();
            foreach ($adjuntos as $adjunto) {
                if (isset($adjunto)) {
                    $files[] =
                        storage_path(
                            'app/public/adjuntos/'.
                            $adjunto->route.'/'.
                            $adjunto->title.'.'.$adjunto->extension
                        );
                    $size += $adjunto->size;
                }
            }
            $document['route'] = FDFPrepareService::joinPDFs($files, $document['dni']);
            $document['name'] = $document['dni'].'.pdf';
            $document['size'] = $size;
        }
        return $fcts;
    }


}
