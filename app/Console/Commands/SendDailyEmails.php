<?php

namespace Intranet\Console\Commands;

use Illuminate\Console\Command;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\Notification;
use Illuminate\Support\Facades\Mail;
use Intranet\Mail\ResumenDiario;

class SendDailyEmails extends Command
{
    public function __construct(private readonly ProfesorService $profesorService)
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:Daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resumen mensajes del dia';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $todos = $this->profesorService->all();
        foreach ($todos as $profesor) {
            $notificaciones = Notification::where('notifiable_id', $profesor->dni)
                    ->whereDate('created_at', hoy())
                    ->whereNull('read_at')
                    ->get();
            // hay que poner email
            if ($notificaciones->count()) {
                Mail::to($profesor->email, 'Intranet Batoi')->send(new ResumenDiario($notificaciones));
            }
        }
    }

}
