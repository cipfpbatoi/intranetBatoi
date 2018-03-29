<?php

namespace Intranet\Console\Commands;

use Illuminate\Console\Command;
use Intranet\Http\Controllers\FicharController;
use Intranet\Http\Controllers\GuardiaController;
use Intranet\Entities\Profesor;

class notifyDailyFaults extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fault:Daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifica ausencias diarias';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $profesores = FicharController::noHanFichado(Hoy());
        $guardias = GuardiaController::noGuardia(Hoy());
        foreach ($profesores as $profesor) 
            avisa($profesor,'No has fixat hui dia ' . Hoy('d-m-Y'), '#', 'Sistema');
        foreach ($guardias as $guardia)
            avisa($guardia,'No has fixat la guardia  hui dia ' . Hoy('d-m-Y'), '#', 'Sistema');
    }

}
