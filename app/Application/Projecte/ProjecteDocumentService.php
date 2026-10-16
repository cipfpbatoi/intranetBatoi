<?php

declare(strict_types=1);

namespace Intranet\Application\Projecte;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Intranet\Entities\OrdenReunion;
use Intranet\Entities\Projecte;
use Intranet\Entities\Reunion;
use Intranet\Entities\Grupo;
use Intranet\Services\Document\PdfService;

/**
 * Orquestra la generació documental específica del domini de projectes.
 */
class ProjecteDocumentService
{
    /**
     * Crea l'acta de valoració de propostes.
     *
     * @param Collection<int, Projecte> $projectes
     */
    public function createProposalActa(Collection $projectes, string $tutorDni): Reunion
    {
        $acta = new Reunion([
            'tipo' => 11,
            'numero' => 0,
            'curso' => curso(),
            'fecha' => hoy(),
            'idProfesor' => $tutorDni,
            'descripcion' => 'Acta valoració propostes',
            'objectivos' => "Valorar les propostes que ha fet l'alumnat per al mòdul de Projecte",
            'idEspacio' => 'SalaProf',
        ]);
        $acta->save();

        foreach ($projectes->values() as $key => $projecte) {
            $this->createOrder(
                $acta,
                OrdenReunion::CODE_PROJECT_PROPOSAL_STUDENT,
                (string) $projecte->Alumno->fullName,
                $projecte->titol . ' (Tutor individual)',
                $key + 1
            );
        }

        return $acta;
    }

    /**
     * Crea l'acta d'assignació de defensa.
     *
     * @param Collection<int, Projecte> $projectes
     */
    public function createDefenseActa(Collection $projectes, string $tutorDni): Reunion
    {
        $acta = new Reunion([
            'tipo' => 12,
            'numero' => 0,
            'curso' => curso(),
            'fecha' => hoy(),
            'idProfesor' => $tutorDni,
            'descripcion' => 'Data Defensa del mòdul de projecte',
            'objectivos' => "Assignar dia i hora per a la defensa dels Projectes",
            'idEspacio' => 'SalaProf',
        ]);
        $acta->save();

        foreach ($projectes->values() as $key => $projecte) {
            $this->createOrder(
                $acta,
                OrdenReunion::CODE_PROJECT_DEFENSE_STUDENT,
                (string) $projecte->Alumno->fullName,
                '(' . $projecte->titol . ')' . $projecte->defensa . '(' . $projecte->hora_defensa . ')',
                $key + 1
            );
        }

        return $acta;
    }

    /**
     * Genera el ZIP i envia el correu amb els projectes del grup.
     *
     * @param Collection<int, Projecte> $projectes
     * @param array<int, string> $emails
     */
    public function sendProjectsZip(Grupo $grupo, Collection $projectes, array $emails): void
    {
        $zipPath = app(PdfService::class)->hazZip('pdf.propostaProjecte', $projectes, null, 'portrait', 'idAlumne');

        Mail::send('email.projectes', ['grupo' => $grupo], function ($message) use ($zipPath, $emails): void {
            $message->to($emails)
                ->subject('Projectes del grup')
                ->attach($zipPath);
        });

        if ($zipPath && file_exists($zipPath)) {
            @unlink($zipPath);
        }
    }

    /**
     * Persistix un punt generat amb el seu codi intern immutable.
     */
    private function createOrder(
        Reunion $reunion,
        string $code,
        string $description,
        string $summary,
        int $position
    ): void {
        $order = new OrdenReunion([
            'idReunion' => $reunion->id,
            'descripcion' => $description,
            'resumen' => $summary,
            'orden' => $position,
        ]);
        $order->codigo = $code;
        $order->save();
    }
}
