<?php

namespace Intranet\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Intranet\Entities\Adjunto;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Profesor;
use Intranet\Entities\Notification;
use Illuminate\Support\Facades\Mail;
use Intranet\Exceptions\IntranetException;
use Intranet\Jobs\UploadFiles;
use Intranet\Mail\Comunicado;
use Intranet\Mail\ResumenDiario;
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
    protected $sService;



    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->sService = new SecretariaService();
        } catch (\Exception $e) {
            echo 'No hi ha connexió amb el servidor de matrícules';
            exit();
        }
        foreach (AlumnoFct::where('a56', 1)->where('beca', 0)->get() as $fct) {
            $document = array();
            $this->buscaDocuments($fct, $document);
            if (count($document) == 1){
                UploadFiles::dispatch($document, $this->sService);
            } else {
                $profesor = Profesor::find('021652470V');
                Mail::to($profesor->email, 'Intranet')
                    ->send(new Comunicado([
                        'tutor' => $profesor->shortName, 'nombre' => 'Ignasi Gomis',
                        'email' => 'igomis@cipfpbatoi.es', 'document' => $document
                    ], $fct, 'email.a56'));
            }
        }
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

        foreach ($fcts as $key => $fct1) {  // cerque els adjunts
            $adjuntos[$key] = Adjunto::where('route', 'alumnofctaval/'.$fct1->id)
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
            foreach ($adjuntos as $key => $adjunto) {
                $files[$key] =
                    storage_path(
                        'app/public/adjuntos/'.
                        $adjuntos[$key]->route.'/'.
                        $adjuntos[$key]->title.'.'.$adjuntos[$key]->extension
                    );
                $size += $adjuntos[$key]->size;
            }
            $document['route'] = FDFPrepareService::joinPDFs($files, $document['dni']);
            $document['name'] = $document['dni'].'.pdf';
            $document['size'] = $size;
        }
    }


}
