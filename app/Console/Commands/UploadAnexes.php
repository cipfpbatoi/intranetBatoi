<?php

namespace Intranet\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Intranet\Entities\Adjunto;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Profesor;
use Intranet\Entities\Notification;
use Illuminate\Support\Facades\Mail;
use Intranet\Jobs\UploadFiles;
use Intranet\Mail\Comunicado;
use Intranet\Mail\ResumenDiario;
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
    protected $description = 'Pujar anexes V i VI';
    protected $SService;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        try {
            $this->SService = new SecretariaService();
            parent::__construct();
        } catch (\Exception $e) {
            echo 'No hi ha connexió amb el servidor de matrícules';
            exit();
        }
    }

    private function tipoDocument($title){
        $tipos = ['A5'=>'10','A6'=>'11','AVI'=>'11','AV'=>'10','AN.VI'=>'11','AN.V'=>'10',
            'ANEXO5'=>'10','ANEXO6'=>'11','ANNEXVI'=>'11','ANNEXV'=>'10'];

        foreach ($tipos as $key => $tipo){
            if (str_contains(strtoupper($title),$key)){
                return $tipos[$key];
            }
        }
        return null;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (AlumnoFct::where('a56',1)->where('beca',0)->get() as $fct){
            $document = array();
            $tutor = '';
            foreach(Adjunto::where('route','alumnofctaval/'.$fct->id)->where('extension','pdf')->get() as $key => $adjunto){
                $document[$key]['title'] = $this->tipoDocument($adjunto->title);
                $document[$key]['file'] = $adjunto->route;
                $document[$key]['name'] = $adjunto->name;
                $document[$key]['size'] = $adjunto->size;
                $document[$key]['dni'] = $fct->Alumno->dni;
                $document[$key]['fct'] = $fct;
                $tutor = $adjunto->owner;
            }
            if (count($document) == 2) {
                if (isset($document[0]['title'])&&$document[1]['title']){
                    UploadFiles::dispatch($document,$this->SService);
                } else {
                    if ($document[0]['size'] > $document[1]['size']){
                        $document[0]['title'] = '10';
                        $document[1]['title'] = '11';
                    } else {
                        $document[0]['title'] = '11';
                        $document[1]['title'] = '10';
                    }
                    UploadFiles::dispatch($document,$this->SService);
                }
            } else {
                if (count($document)) {
                    $profesor = Profesor::find($tutor);
                    Mail::to($profesor->email, 'Intranet')
                        ->send(new Comunicado([
                            'tutor' => $profesor->shortName, 'nombre' => 'Ignasi Gomis',
                            'email' => 'igomis@cipfpbatoi.es', 'document' => $document
                        ], $fct, 'email.a56'));
                }
            }
        }
    }



}
