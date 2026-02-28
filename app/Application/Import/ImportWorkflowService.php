<?php

declare(strict_types=1);

namespace Intranet\Application\Import;

use Illuminate\Http\UploadedFile;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Application\Profesor\ProfesorService;
use RuntimeException;
use SplFileInfo;
use Intranet\Services\UI\AppAlert as Alert;

/**
 * Servei d'orquestració del flux d'importació.
 */
class ImportWorkflowService
{
    public function __construct(
        private readonly ProfesorService $profesorService,
        private readonly GrupoService $grupoService
    )
    {
    }

    /**
     * Executa el recorregut de taules d'un XML d'importació.
     *
     * @param array<int, array<string, mixed>> $camposBdXml
     * @param callable(mixed, array<string, mixed>, mixed): void $tableHandler
     */
    public function executeXmlImport(mixed $fxml, array $camposBdXml, mixed $firstImport, callable $tableHandler): void
    {
        $xmlPath = $this->resolveXmlPath($fxml);
        $xml = simplexml_load_file($xmlPath);
        if ($xml === false) {
            throw new RuntimeException("No s'ha pogut carregar l'XML: {$xmlPath}");
        }

        foreach ($camposBdXml as $table) {
            $tableHandler($xml->{$table['nombrexml']}, $table, $firstImport);
        }
    }

    private function resolveXmlPath(mixed $fxml): string
    {
        if (is_string($fxml)) {
            return $fxml;
        }

        if ($fxml instanceof UploadedFile) {
            return $fxml->getRealPath() ?: $fxml->getPathname();
        }

        if ($fxml instanceof SplFileInfo) {
            return $fxml->getRealPath() ?: $fxml->getPathname();
        }

        throw new RuntimeException('Format de fitxer XML no suportat.');
    }

    /**
     * Executa el recorregut amb pipeline pre/in/post.
     *
     * @param array<int, array<string, mixed>> $camposBdXml
     * @param callable(string, string): void $preHandler
     * @param callable(mixed, array<string, mixed>): void $inHandler
     * @param callable(string, string, mixed): void $postHandler
     */
    public function executeXmlImportWithHooks(
        mixed $fxml,
        array $camposBdXml,
        mixed $context,
        callable $preHandler,
        callable $inHandler,
        callable $postHandler
    ): void {
        $this->executeXmlImport($fxml, $camposBdXml, $context, function ($xmltable, $table, $ctx) use (
            $preHandler,
            $inHandler,
            $postHandler
        ): void {
            if (count($xmltable)) {
                $preHandler($table['nombreclase'], $table['nombrexml']);
                $inHandler($xmltable, $table);
                $postHandler($table['nombreclase'], $table['nombrexml'], $ctx);
                return;
            }

            Alert::danger('No hay registros de ' . $table['nombrexml'] . ' en el xml');
        });
    }

    /**
     * Executa el recorregut amb pipeline simple.
     *
     * @param array<int, array<string, mixed>> $camposBdXml
     * @param callable(mixed, array<string, mixed>, mixed): void $inHandler
     */
    public function executeXmlImportSimple(
        mixed $fxml,
        array $camposBdXml,
        mixed $context,
        callable $inHandler
    ): void {
        $this->executeXmlImport($fxml, $camposBdXml, $context, function ($xmltable, $table, $ctx) use ($inHandler): void {
            if (count($xmltable)) {
                $inHandler($xmltable, $table, $ctx);
                return;
            }

            Alert::danger('No hay registros de ' . $table['nombrexml'] . ' en el xml');
        });
    }

    public function assignTutores(): void
    {
        $profesores = $this->profesorService->all();
        $tutores = $this->grupoService->tutoresDniList();

        $tutorSet = array_fill_keys($tutores, true);
        $substitucions = $profesores
            ->whereNotNull('sustituye_a')
            ->mapWithKeys(static fn ($profesor): array => [trim((string) $profesor->dni) => trim((string) $profesor->sustituye_a)])
            ->all();

        foreach ($profesores as $profesor) {
            $dni = trim((string) $profesor->dni);
            $sustitueA = $substitucions[$dni] ?? null;
            $isTutor = isset($tutorSet[$dni]) || ($sustitueA !== null && $sustitueA !== '' && isset($tutorSet[$sustitueA]));

            $profesor->rol = $this->applyTutorRoleRules($isTutor, $profesor->rol);
            $profesor->save();
        }

        Alert::info('Tutors assignats');
    }

    private function applyTutorRoleRules(bool $isTutor, mixed $role): mixed
    {
        $rolTutor = config('roles.rol.tutor');
        $rolPracticas = config('roles.rol.practicas');

        if ($isTutor) {
            if (!esRol($role, $rolTutor)) {
                $role *= $rolTutor;
            }
            if (!esRol($role, $rolPracticas)) {
                $role *= $rolPracticas;
            }
            return $role;
        }

        if (esRol($role, $rolTutor)) {
            $role /= $rolTutor;
        }
        if (esRol($role, $rolPracticas)) {
            $role /= $rolPracticas;
        }

        return $role;
    }
}
