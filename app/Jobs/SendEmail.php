<?php

namespace Intranet\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailer;
use Intranet\Mail\Comunicado;
use Intranet\Events\EmailSended;


class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    
    protected $correo;
    protected $elemento;
    protected $attach;
    protected $vista;
    protected $remitente;

    
    public function __construct($correo,$remitente,$vista,$elemento,$attach=null)
    {
        $this->correo = $correo;
        $this->remitente = $remitente;
        $this->vista = $vista;
        $this->elemento = $elemento;
        $this->attach = $attach;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    
    public function handle(Mailer $mailer)
    {
        $mailer->to($this->correo, 'Intranet')
                ->send(new Comunicado($this->remitente,$this->elemento, $this->vista,$this->attach));
        
        if (isset($this->remitente['id'])){
            avisa($this->remitente['id'], 'El correu adreçat a '.$this->correo.' ha sigut enviat','#','SERVIDOR DE CORRREU');
            event(new EmailSended($this->elemento,$this->correo));
        }
    }
}
