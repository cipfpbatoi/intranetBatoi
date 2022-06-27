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
    protected $token;
    private $user,$pass,$link;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->user = env('APLSEC_USER','intranet@cipfpbatoi.es');
        $this->pass =  env('APLSEC_PASS','intr4n3t@B4t01');
        $this->link =  env('APLSEC_LINK','https://matricula.cipfpbatoi.es/api/');
        parent::__construct();
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
        if ($this->login()){
            foreach (AlumnoFct::where('a56',1)->where('beca',0)->get() as $fct){
                $document = array();
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
                        UploadFiles::dispatch($document,$this->token);
                    } else {
                        if ($document[0]['size'] > $document[1]['size']){
                            $document[0]['title'] = '10';
                            $document[1]['title'] = '11';
                        } else {
                            $document[0]['title'] = '11';
                            $document[1]['title'] = '10';
                        }
                        UploadFiles::dispatch($document,$this->token);
                    }
                } else {
                    $profesor = Profesor::find($tutor);
                    Mail::to('igomis@cipfpbatoi.es', 'Intranet')
                       ->send(new Comunicado(['tutor'=>$profesor->shortName,'nombre'=>'Ignasi Gomis','email'=>'igomis@cipfpbatoi.es'],$fct,'email.a56'));
                }
            }
        } else {
            echo 'No hi ha connexio';
        }
        return back();
    }

    private function login(){
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
