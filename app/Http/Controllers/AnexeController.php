<?php


namespace Intranet\Http\Controllers;


use Illuminate\Support\Facades\Http;
use DB;
use Illuminate\Support\Facades\Mail;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Profesor;
use Intranet\Mail\Comunicado;
use Styde\Html\Facades\Alert;
use Intranet\Entities\Adjunto;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class AnexeController extends Controller
{
    private $user,$pass,$link;


    public function __construct(){
       $this->user = env('APLSEC_USER','intranet@cipfpbatoi.es');
       $this->pass =  env('APLSEC_PASS','intr4n3t@B4t01');
       $this->link =  env('APLSEC_LINK','https://matricula.cipfpbatoi.es/api/');
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
            Alert::error("Error al subir el archivo $name de ".$document['dni'].". Error: ".$response->body);
            return 0;
        }
    }

    private function upload($documents)
    {
        $success = 0;
        foreach ($documents as $document) {
            $success += $this->uploadFile($document);
        }
        if ($success == 2) {
            foreach ($documents as $document) {
                $document['fct']->a56 = 2;
                $document['fct']->save();
            }
            Alert::success("Archivos de ".$document['fct']->Alumno->shortName." subidos correctamente");
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


    public function sendDocuments(){
        if ($this->login()){
            foreach (AlumnoFct::where('a56',1)->where('beca',0)->get() as $fct){
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
                        $this->upload($document);
                    } else {
                        if ($document[0]['size'] > $document[1]['size']){
                            $document[0]['title'] = '10';
                            $document[1]['title'] = '11';
                        } else {
                            $document[0]['title'] = '11';
                            $document[1]['title'] = '10';
                        }
                        $this->upload($document)  ;
                    }
                } else {
                    $profesor = Profesor::find($tutor);
                    Mail::to('igomis@cipfpbatoi.es', 'Intranet')
                        ->send(new Comunicado(['tutor'=>$profesor->shortName,'nombre'=>'Ignasi Gomis','email'=>'igomis@cipfpbatoi.es'],$fct,'email.a56'));
                    Alert::danger($fct->Alumno->fullName.' no te '.count($document).' documents');
                }
            }
        } else {
            Alert::danger('No hi ha connexio');
        }
        return back();
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
