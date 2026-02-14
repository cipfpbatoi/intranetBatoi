<?php

declare(strict_types=1);

namespace Intranet\Application\Import;

use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\Grupo;
use Styde\Html\Facades\Alert;

/**
 * Servei d'orquestració del flux d'importació.
 */
class ImportWorkflowService
{
    public function __construct(private readonly ProfesorService $profesorService)
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
        $xml = simplexml_load_file($fxml);
        foreach ($camposBdXml as $table) {
            $tableHandler($xml->{$table['nombrexml']}, $table, $firstImport);
        }
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
        foreach ($this->profesorService->all() as $profesor) {
            $profesor->rol = $this->applyTutorRoleRules(Grupo::QTutor($profesor->dni)->first(), $profesor->rol);
            $profesor->save();
        }

        Alert::info('Tutors assignats');
    }

    private function applyTutorRoleRules(mixed $grupo, mixed $role): mixed
    {
        $rolTutor = config('roles.rol.tutor');
        $rolPracticas = config('roles.rol.practicas');

        if ($grupo) {
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
