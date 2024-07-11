<?php

namespace Intranet\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Intranet\Mail\Comunicado;



class UploadFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $files;
    protected $sService;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file, $sService,$fcts)
    {
        $this->file = $file;
        $this->sService = $sService;
        $this->fcts = $fcts;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->sService->uploadFile($this->file);
            foreach ($this->fcts as $fct) {
                $fct->a56 = 2;
                $fct->save();
            }
        } catch (\Exception $e) {
            Mail::to('igomis@cipfpbatoi.es', 'Intranet')
                ->send(new Comunicado(
                    'igomis@cipfpbatoi.es',
                    $this->files[0],
                    'email.a56error'
                ));
        }

    }

}
