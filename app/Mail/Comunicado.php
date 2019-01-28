<?php

namespace Intranet\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class Comunicado extends Mailable
{

    use Queueable,
        SerializesModels;

    public $elemento;
    public $modelo;
    protected $vista;
    protected $attach;

    //protected $calendari;

    private function getmodel($elemento)
    {
        $entero = get_class($elemento);
        $nspace = 'Intranet\Entities\\';
        return substr($entero, strlen($nspace), strlen($entero));
    }

    public function __construct($remitente,$elemento, $vista, $attach=null)
    {
        $this->elemento = $elemento;
        $this->remitente = $remitente;
        $this->modelo = $this->getmodel($this->elemento);
        $this->vista = $vista;
        $this->attach = $attach;
    }

    public function build()
    {
        $nombre = $this->remitente['nombre'];
        $vista = $this->view($this->vista,['remitente'=>$this->remitente])
                        ->from($this->remitente['email'],$nombre)
                        ->subject(trans("models.modelos." . $this->modelo));
        if (isset($this->attach))
            foreach ($this->attach as $index => $value){
                $vista = $vista->attach(storage_path($index),['mime' => $value]);
            }
        
        Log::notice("Enviat comunicat $this->modelo de $nombre");    
        return $vista;
    }

}