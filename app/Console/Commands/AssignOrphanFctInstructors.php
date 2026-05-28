<?php

declare(strict_types=1);

namespace Intranet\Console\Commands;

use Illuminate\Console\Command;
use Intranet\Application\Fct\FctService;

/**
 * Associa instructors orfes als centres de treball de les seues FCT.
 */
class AssignOrphanFctInstructors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fct:assign-orphan-instructors {--dry-run : Mostra el resultat sense escriure en la base de dades}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assigna als centres de treball els instructors sense centre que tenen FCT';

    /**
     * Execute the console command.
     *
     * @param FctService $fctService
     * @return int
     */
    public function handle(FctService $fctService): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $result = $fctService->assignOrphanInstructorsToFctCentros($dryRun);

        $prefix = $dryRun ? '[dry-run] ' : '';
        $this->info($prefix . 'Instructors revisats: ' . $result['instructors']);
        $this->info($prefix . 'Assignacions de centre: ' . $result['assignments']);

        return Command::SUCCESS;
    }
}
