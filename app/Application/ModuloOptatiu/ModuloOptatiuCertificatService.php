<?php

declare(strict_types=1);

namespace Intranet\Application\ModuloOptatiu;

use Illuminate\Support\Collection;
use Intranet\Entities\Alumno;
use Intranet\Entities\AlumnoResultado;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\ModulOptatiuCertificat;
use Intranet\Entities\ModulOptatiuCertificatAlumne;
use Intranet\Jobs\SendEmail;
use Intranet\Services\Document\PdfService;
use Intranet\Services\School\ModuloGrupoService;
use Intranet\Services\School\SecretariaService;

/**
 * Cas d'ús per preparar, guardar i emetre certificats de mòduls optatius.
 */
class ModuloOptatiuCertificatService
{
    public const DOCUMENT_TYPE = 16;

    private const OPTIONAL_MODULE_CODE = 'CVOPT';
    private const NO_CURSA_NOTES = [12, 13];
    private const NO_SUPERA_NOTE = 4;

    public function __construct(
        private ?ModuloGrupoService $moduloGrupoService = null,
        private ?PdfService $pdfService = null,
        private ?SecretariaService $secretariaService = null
    ) {
    }

    /**
     * Retorna els mòduls-grup assignats al professor segons l'horari.
     *
     * @param string $dni
     * @return Collection<int, Modulo_grupo>
     */
    public function modulesForTeacher(string $dni): Collection
    {
        return collect($this->modulos()->misModulos($dni))
            ->each(static fn (Modulo_grupo $modulo): Modulo_grupo => $modulo->loadMissing('Grupo.Alumnos', 'ModuloCiclo.Modulo'))
            ->filter(static fn (Modulo_grupo $modulo): bool => self::isOptionalModule($modulo))
            ->sortBy(static fn (Modulo_grupo $modulo): string => $modulo->literal)
            ->values();
    }

    /**
     * Indica si el mòdul-grup correspon al mòdul optatiu codificat a ITACA.
     */
    public static function isOptionalModule(Modulo_grupo $moduloGrupo): bool
    {
        return strtoupper(trim((string) $moduloGrupo->ModuloCiclo?->idModulo)) === self::OPTIONAL_MODULE_CODE;
    }

    /**
     * Indica si el professor pot gestionar el mòdul-grup.
     */
    public function canManage(Modulo_grupo $moduloGrupo, string $dni): bool
    {
        return $this->modulesForTeacher($dni)
            ->contains(static fn (Modulo_grupo $candidate): bool => (int) $candidate->id === (int) $moduloGrupo->id);
    }

    /**
     * Obté o crea les metadades del certificat per al mòdul-grup.
     */
    public function certificateFor(Modulo_grupo $moduloGrupo, string $dni): ModulOptatiuCertificat
    {
        return ModulOptatiuCertificat::query()->firstOrCreate(
            ['idModuloGrupo' => $moduloGrupo->id],
            [
                'denominacio' => '',
                'idProfesor' => $dni,
            ]
        );
    }

    /**
     * Dades del panell: alumnat, notes existents i estat d'emissió.
     *
     * @return array{alumnes:Collection<int, Alumno>, resultats:Collection<string, AlumnoResultado>, estats:Collection<string, ModulOptatiuCertificatAlumne>, pdfDisponibles:Collection<string, bool>, potEmetre:bool}
     */
    public function panelData(ModulOptatiuCertificat $certificat): array
    {
        $moduloGrupo = $certificat->ModuloGrupo;
        $alumnes = $moduloGrupo->Grupo->Alumnos
            ->sortBy('nameFull')
            ->values();
        $resultats = AlumnoResultado::query()
            ->where('idModuloGrupo', $moduloGrupo->id)
            ->whereIn('idAlumno', $alumnes->pluck('nia')->all())
            ->get()
            ->keyBy('idAlumno');
        $estats = ModulOptatiuCertificatAlumne::query()
            ->where('idCertificat', $certificat->id)
            ->whereIn('idAlumno', $alumnes->pluck('nia')->all())
            ->get()
            ->keyBy('idAlumno');
        $denominacioValida = $this->hasRealDenomination($certificat);
        $pdfDisponibles = $alumnes->mapWithKeys(
            static fn (Alumno $alumne): array => [
                $alumne->nia => $denominacioValida
                    && self::isCertifiableNote((int) ($resultats->get($alumne->nia)?->nota ?? 0)),
            ]
        );
        $totesLesNotesGuardades = $alumnes->every(
            static fn (Alumno $alumne): bool => (int) ($resultats->get($alumne->nia)?->nota ?? 0) > 0
        );
        $potEmetre = $denominacioValida && $totesLesNotesGuardades && $pdfDisponibles->contains(true);

        return compact('alumnes', 'resultats', 'estats', 'pdfDisponibles', 'potEmetre');
    }

    /**
     * Resumeix l'estat d'emissió dels certificats possibles d'un mòdul-grup.
     *
     * @return array{certificables:int,emesos:int,pendents:int,complet:bool}
     */
    public function emissionSummary(Modulo_grupo $moduloGrupo): array
    {
        $moduloGrupo->loadMissing('Grupo.Alumnos');
        $alumnes = $moduloGrupo->Grupo?->Alumnos ?? collect();
        $alumneIds = $alumnes->pluck('nia')->all();

        $certificables = AlumnoResultado::query()
            ->where('idModuloGrupo', $moduloGrupo->id)
            ->whereIn('idAlumno', $alumneIds)
            ->get()
            ->filter(static fn (AlumnoResultado $resultat): bool => self::isCertifiableNote((int) $resultat->nota))
            ->pluck('idAlumno')
            ->unique()
            ->values();

        $totalCertificables = $certificables->count();
        $certificat = ModulOptatiuCertificat::query()
            ->where('idModuloGrupo', $moduloGrupo->id)
            ->first();

        $emesos = 0;
        if ($certificat && $totalCertificables > 0) {
            $emesos = ModulOptatiuCertificatAlumne::query()
                ->where('idCertificat', $certificat->id)
                ->whereIn('idAlumno', $certificables->all())
                ->whereNotNull('enviat_at')
                ->pluck('idAlumno')
                ->unique()
                ->count();
        }

        $pendents = max(0, $totalCertificables - $emesos);

        return [
            'certificables' => $totalCertificables,
            'emesos' => $emesos,
            'pendents' => $pendents,
            'complet' => $totalCertificables > 0 && $pendents === 0,
        ];
    }

    /**
     * Opcions de qualificació pròpies del certificat optatiu.
     *
     * @return array<int, string>
     */
    public function noteOptions(): array
    {
        $notes = config('auxiliares.notas');
        unset($notes[1], $notes[2], $notes[3]);
        $notes[self::NO_SUPERA_NOTE] = 'No supera';
        ksort($notes);

        return $notes;
    }

    /**
     * Guarda denominació i notes del certificat.
     *
     * @param array<string, mixed> $notes
     * @return int
     */
    public function save(ModulOptatiuCertificat $certificat, string $denominacio, array $notes): int
    {
        $certificat->denominacio = trim($denominacio);
        $certificat->save();

        $saved = 0;
        foreach ($notes as $idAlumno => $nota) {
            $nota = (int) $nota;
            if ($nota < 0 || $nota > 12) {
                continue;
            }
            if ($nota > 0 && $nota < 5) {
                $nota = self::NO_SUPERA_NOTE;
            }

            AlumnoResultado::query()->updateOrCreate(
                [
                    'idAlumno' => (string) $idAlumno,
                    'idModuloGrupo' => $certificat->idModuloGrupo,
                ],
                ['nota' => $nota]
            );
            $saved++;
        }

        return $saved;
    }

    /**
     * Llista les dades que impedeixen emetre certificats.
     *
     * @return array<int, string>
     */
    public function validationErrors(ModulOptatiuCertificat $certificat): array
    {
        $errors = [];
        if (!$this->hasRealDenomination($certificat)) {
            $errors[] = 'Cal informar la denominació real del mòdul optatiu impartit.';
        }

        $data = $this->panelData($certificat);
        foreach ($data['alumnes'] as $alumne) {
            $nota = (int) ($data['resultats']->get($alumne->nia)?->nota ?? 0);
            if ($nota <= 0) {
                $errors[] = "Falta la nota de {$alumne->fullName}.";
            }
        }

        return $errors;
    }

    /**
     * Llista les dades que impedeixen generar el certificat d'un alumne.
     *
     * @return array<int, string>
     */
    public function validationErrorsForAlumno(ModulOptatiuCertificat $certificat, Alumno $alumne): array
    {
        $errors = [];
        if (!$this->hasRealDenomination($certificat)) {
            $errors[] = 'Cal informar la denominació real del mòdul optatiu impartit.';
        }

        $resultat = $this->resultForAlumno($certificat, $alumne);
        $nota = (int) ($resultat?->nota ?? 0);
        if ($nota <= 0) {
            $errors[] = "Falta la nota de {$alumne->fullName}.";
        } elseif ($nota < 5) {
            $errors[] = "{$alumne->fullName} figura com a No supera.";
        } elseif (!self::isCertifiableNote($nota)) {
            $errors[] = "{$alumne->fullName} figura com a No cursa.";
        }

        return $errors;
    }

    /**
     * Evita emetre certificats amb el literal genèric del codi CVOPT.
     */
    private function hasRealDenomination(ModulOptatiuCertificat $certificat): bool
    {
        $denominacio = trim((string) $certificat->denominacio);
        if ($denominacio === '') {
            return false;
        }

        $genericNames = collect([
            $certificat->ModuloGrupo?->Xmodulo,
            $certificat->ModuloGrupo?->ModuloCiclo?->Modulo?->cliteral,
            $certificat->ModuloGrupo?->ModuloCiclo?->Modulo?->vliteral,
            'Mòdul optatiu',
            'Módulo optativo',
            'Optatiu',
            'Optativo',
        ])
            ->map(static fn ($name): string => trim((string) $name))
            ->filter()
            ->unique()
            ->values();

        return !$genericNames->contains(
            static fn (string $generic): bool => strcasecmp($denominacio, $generic) === 0
        );
    }

    /**
     * Emet certificats, els registra a expedient i envia el correu a cada alumne.
     *
     * @return array{sent:int,errors:array<int,string>}
     */
    public function emit(ModulOptatiuCertificat $certificat): array
    {
        $errors = $this->validationErrors($certificat);
        if ($errors !== []) {
            return ['sent' => 0, 'errors' => $errors];
        }

        $sent = 0;
        $data = $this->panelData($certificat);
        $secretario = cargo('secretario');
        if (!$secretario) {
            return ['sent' => 0, 'errors' => ['No està configurat el càrrec de secretaria.']];
        }
        $remitente = ['email' => $secretario->email, 'nombre' => $secretario->FullName];

        foreach ($data['alumnes'] as $alumne) {
            $resultado = $data['resultats']->get($alumne->nia);
            if (!self::isCertifiableNote((int) ($resultado?->nota ?? 0))) {
                continue;
            }

            try {
                $route = $this->pdfRoute($certificat, $alumne);
                $path = storage_path($route);
                if (!is_dir(dirname($path))) {
                    mkdir(dirname($path), 0777, true);
                }
                if (file_exists($path)) {
                    unlink($path);
                }

                $this->pdf($certificat, $alumne, $resultado)->save($path);

                $estat = ModulOptatiuCertificatAlumne::query()->updateOrCreate(
                    ['idCertificat' => $certificat->id, 'idAlumno' => $alumne->nia],
                    ['fitxer' => $route]
                );

                $this->secretaria()->uploadFile([
                    'title' => self::DOCUMENT_TYPE,
                    'dni' => $alumne->dni,
                    'alumne' => trim($alumne->shortName),
                    'route' => $route,
                    'name' => basename($route),
                    'size' => filesize($path),
                ]);

                $estat->registrat_at = now();
                $estat->enviat_at = now();
                $estat->save();

                dispatch(new SendEmail(
                    $alumne->email,
                    $remitente,
                    'email.modulOptatiuCertificat',
                    $estat,
                    [$route => 'application/pdf']
                ));
                $sent++;
            } catch (\Throwable $exception) {
                $errors[] = $alumne->fullName . ': ' . $exception->getMessage();
            }
        }

        return ['sent' => $sent, 'errors' => $errors];
    }

    /**
     * Genera el PDF d'un alumne sense registrar-lo ni enviar correus.
     */
    public function pdf(ModulOptatiuCertificat $certificat, Alumno $alumne, ?AlumnoResultado $resultat = null): mixed
    {
        $resultat ??= $this->resultForAlumno($certificat, $alumne);

        return $this->pdfs()->hazPdf(
            'pdf.modulOptatiu.certificat',
            ['alumne' => $alumne, 'certificat' => $certificat, 'resultat' => $resultat],
            cargaDatosCertificado([]),
            'portrait'
        );
    }

    /**
     * Ruta relativa al directori `storage`.
     */
    private function pdfRoute(ModulOptatiuCertificat $certificat, Alumno $alumne): string
    {
        return "tmp/modul_optatiu_{$certificat->id}_{$alumne->nia}.pdf";
    }

    /**
     * Retorna el resultat de l'alumne per al mòdul-grup del certificat.
     */
    private function resultForAlumno(ModulOptatiuCertificat $certificat, Alumno $alumne): ?AlumnoResultado
    {
        return AlumnoResultado::query()
            ->where('idModuloGrupo', $certificat->idModuloGrupo)
            ->where('idAlumno', $alumne->nia)
            ->first();
    }

    /**
     * Indica si la qualificació genera certificat.
     */
    private static function isCertifiableNote(int $nota): bool
    {
        return $nota >= 5 && !in_array($nota, self::NO_CURSA_NOTES, true);
    }

    private function modulos(): ModuloGrupoService
    {
        return $this->moduloGrupoService ??= app(ModuloGrupoService::class);
    }

    private function pdfs(): PdfService
    {
        return $this->pdfService ??= app(PdfService::class);
    }

    private function secretaria(): SecretariaService
    {
        return $this->secretariaService ??= app(SecretariaService::class);
    }
}
