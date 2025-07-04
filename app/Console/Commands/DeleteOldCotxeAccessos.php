<?php
// app/Console/Commands/DeleteOldCotxeAccessos.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Intranet\Entities\CotxeAcces;
use Carbon\Carbon;

class DeleteOldCotxeAccessos extends Command
{
    protected $signature = 'cotxes:esborra-vells';
    protected $description = 'Esborra els accessos de cotxes amb més d’una setmana';

    public function handle()
    {
        $antiga = Carbon::now()->subDays(7);

        $esborrats = CotxeAcces::where('created_at', '<', $antiga)->delete();

        $this->info("S'han esborrat $esborrats accessos antics.");
    }
}
