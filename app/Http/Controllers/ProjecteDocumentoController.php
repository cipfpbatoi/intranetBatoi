<?php

declare(strict_types=1);

namespace Intranet\Http\Controllers;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Application\Projecte\ProjecteDocumentService;
use Intranet\Http\Controllers\Core\IntranetController;
use Intranet\Services\Document\PdfService;
use Intranet\Entities\Projecte;

/**
 * Gestiona fluxos documentals específics del domini de projectes.
 */
class ProjecteDocumentoController extends IntranetController
{
    private ?GrupoService $grupoService = null;
    private ?ProfesorService $profesorService = null;
    private ?ProjecteDocumentService $projecteDocumentService = null;

    public function __construct(
        ?GrupoService $grupoService = null,
        ?ProfesorService $profesorService = null,
        ?ProjecteDocumentService $projecteDocumentService = null
    ) {
        parent::__construct();
        $this->grupoService = $grupoService;
        $this->profesorService = $profesorService;
        $this->projecteDocumentService = $projecteDocumentService;
    }

    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
    }

    private function profesores(): ProfesorService
    {
        if ($this->profesorService === null) {
            $this->profesorService = app(ProfesorService::class);
        }

        return $this->profesorService;
    }

    private function documents(): ProjecteDocumentService
    {
        if ($this->projecteDocumentService === null) {
            $this->projecteDocumentService = app(ProjecteDocumentService::class);
        }

        return $this->projecteDocumentService;
    }

    private function myTutorGroup()
    {
        return $this->grupos()->byTutorOrSubstitute(AuthUser()->dni, AuthUser()->sustituye_a);
    }

    private function projectsForTutorGroup(int $estat, ?callable $order = null)
    {
        $miGrupo = $this->myTutorGroup();
        if ($miGrupo === null) {
            return [null, collect()];
        }

        $alumnos = hazArray($miGrupo->Alumnos, 'nia', 'nia');
        $query = Projecte::whereIn('idAlumne', $alumnos)->where('estat', $estat);
        if ($order !== null) {
            $order($query);
        }

        return [$miGrupo, $query->get()];
    }

    public function send()
    {
        $this->authorize('send', Projecte::class);
        [$miGrupo, $projectes] = $this->projectsForTutorGroup(1);
        if ($miGrupo === null) {
            return back()->withErrors('No tens grup assignat');
        }

        $professorsEmails = [];
        foreach ($this->profesores()->byGrupo((string) $miGrupo->codigo) as $profesor) {
            $professorsEmails[] = $profesor->email;
        }

        $this->documents()->sendProjectsZip($miGrupo, $projectes, $professorsEmails);

        return back()->with('success', 'Se ha enviado el correo con los proyectos del grupo.');
    }

    public function acta()
    {
        $this->authorize('createActa', Projecte::class);
        [$miGrupo, $projectes] = $this->projectsForTutorGroup(1);
        if ($miGrupo === null) {
            return back()->withErrors('No tens grup assignat');
        }

        $acta = $this->documents()->createProposalActa($projectes, (string) authUser()->dni);

        return redirect()->route('reunion.edit', $acta->id);
    }

    public function actaE()
    {
        $this->authorize('createDefenseActa', Projecte::class);
        [$miGrupo, $projectes] = $this->projectsForTutorGroup(2, function ($query): void {
            $query->orderBy('defensa')->orderBy('hora_defensa');
        });
        if ($miGrupo === null) {
            return back()->withErrors('No tens grup assignat');
        }

        $acta = $this->documents()->createDefenseActa($projectes, (string) authUser()->dni);

        return redirect()->route('reunion.edit', $acta->id);
    }

    public function pdf($id)
    {
        $elemento = Projecte::findOrFail((int) $id);
        $this->authorize('view', $elemento);

        return app(PdfService::class)->hazPdf('pdf.propostaProjecte', $elemento, null)->stream();
    }
}
