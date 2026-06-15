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
                'denominacio' => (string) ($moduloGrupo->Xmodulo ?: $moduloGrupo->literal),
                'idProfesor' => $dni,
            ]
        );
    }

    /**
     * Dades del panell: alumnat, notes existents i estat d'emissió.
     *
     * @return array{alumnes:Collection<int, Alumno>, resultats:Collection<string, AlumnoResultado>, estats:Collection<string, ModulOptatiuCertificatAlumne>}
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

        return compact('alumnes', 'resultats', 'estats');
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
            if ($nota < 0 || $nota > 13) {
                continue;
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
        if (trim((string) $certificat->denominacio) === '') {
            $errors[] = 'Cal informar la denominació del mòdul optatiu.';
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
            try {
                $route = $this->pdfRoute($certificat, $alumne);
                $path = storage_path($route);
                if (!is_dir(dirname($path))) {
                    mkdir(dirname($path), 0777, true);
                }
                if (file_exists($path)) {
                    unlink($path);
                }

                $this->pdfs()->hazPdf(
                    'pdf.modulOptatiu.certificat',
                    ['alumne' => $alumne, 'certificat' => $certificat, 'resultat' => $resultado],
                    cargaDatosCertificado([]),
                    'portrait'
                )->save($path);

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
     * Ruta relativa al directori `storage`.
     */
    private function pdfRoute(ModulOptatiuCertificat $certificat, Alumno $alumne): string
    {
        return "tmp/modul_optatiu_{$certificat->id}_{$alumne->nia}.pdf";
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
