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

    protected $files,$SService;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($files,$SService)
    {
        $this->files = $files;
        $this->SService = $SService;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->SService->uploadA56($this->files);
        } catch (\Exception $e) {
            Mail::to('igomis@cipfpbatoi.es', 'Intranet')
                ->send(new Comunicado('igomis@cipfpbatoi.es'
                , $this->files[0], 'email.a56error'));
        }

    }

}
