<?php
// app/Console/Commands/DeleteOldCotxeAccessos.php

namespace Intranet\Console\Commands;

use Illuminate\Console\Command;
use Intranet\Entities\CotxeAcces;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class DeleteOldCotxeAccessos extends Command
{
    protected $signature = 'cotxes:esborra-vells';
    protected $description = 'Esborra els accessos de cotxes amb més d’una setmana';

    public function handle(): int
    {
        try {
            $antiga = Carbon::now()->subDays(7);

            $esborrats = CotxeAcces::where('created_at', '<', $antiga)->delete();

            $this->info("S'han esborrat $esborrats accessos antics.");
            return self::SUCCESS;
        } catch (Throwable $e) {
            report($e);
            Log::error('Error esborrant accessos de cotxes antics.', [
                'exception' => $e->getMessage(),
            ]);

            return self::FAILURE;
        }
    }
}
