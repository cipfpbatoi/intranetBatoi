<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Import;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Application\Import\ImportWorkflowService;
use Intranet\Application\Profesor\ProfesorService;
use Mockery;
use Styde\Html\Facades\Alert;
use Tests\TestCase;

class ImportWorkflowServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_xml_import_with_hooks_crida_pipeline_quan_hi_ha_registres(): void
    {
        $xmlPath = $this->createTempXml('<root><profesores><item><dni>P1</dni></item></profesores></root>');
        $camposBdXml = [
            ['nombrexml' => 'profesores', 'nombreclase' => 'Profesor'],
        ];

        $calls = [];

        $service = $this->buildService();
        $service->executeXmlImportWithHooks(
            $xmlPath,
            $camposBdXml,
            ['context' => true],
            function (string $className, string $xmlName) use (&$calls): void {
                $calls[] = ['pre', $className, $xmlName];
            },
            function ($xmltable, array $table) use (&$calls): void {
                $calls[] = ['in', $table['nombrexml'], count($xmltable)];
            },
            function (string $className, string $xmlName, $ctx) use (&$calls): void {
                $calls[] = ['post', $className, $xmlName, $ctx['context'] ?? false];
            }
        );

        $this->assertCount(3, $calls);
        $this->assertSame(['pre', 'Profesor', 'profesores'], $calls[0]);
        $this->assertSame(['in', 'profesores', 1], $calls[1]);
        $this->assertSame(['post', 'Profesor', 'profesores', true], $calls[2]);
    }

    public function test_execute_xml_import_with_hooks_mostra_alerta_si_taula_buida(): void
    {
        $xmlPath = $this->createTempXml('<root></root>');
        $camposBdXml = [
            ['nombrexml' => 'profesores', 'nombreclase' => 'Profesor'],
        ];

        $pre = false;
        $in = false;
        $post = false;

        Alert::shouldReceive('danger')
            ->once()
            ->withArgs(static fn (string $msg): bool => str_contains($msg, 'No hay registros de profesores en el xml'));

        $service = $this->buildService();
        $service->executeXmlImportWithHooks(
            $xmlPath,
            $camposBdXml,
            null,
            function () use (&$pre): void {
                $pre = true;
            },
            function () use (&$in): void {
                $in = true;
            },
            function () use (&$post): void {
                $post = true;
            }
        );

        $this->assertFalse($pre);
        $this->assertFalse($in);
        $this->assertFalse($post);
    }

    public function test_assign_tutores_aplica_regles_de_rol_i_guarda_canvis(): void
    {
        $rolProfesor = (int) config('roles.rol.profesor');
        $rolTutor = (int) config('roles.rol.tutor');
        $rolPractiques = (int) config('roles.rol.practicas');

        $profesorTutor = $this->newProfesorStub('TUT001', $rolProfesor);
        $profesorSubstitut = $this->newProfesorStub('SUB001', $rolProfesor, 'TUT001');
        $profesorNoTutor = $this->newProfesorStub('NOT001', $rolProfesor * $rolTutor * $rolPractiques);

        $profesorService = Mockery::mock(ProfesorService::class);
        $profesorService->shouldReceive('all')->once()->andReturn(new EloquentCollection([
            $profesorTutor,
            $profesorSubstitut,
            $profesorNoTutor,
        ]));

        $grupoService = Mockery::mock(GrupoService::class);
        $grupoService->shouldReceive('tutoresDniList')->once()->andReturn(['TUT001']);

        Alert::shouldReceive('info')->once()->with('Tutors assignats');

        $service = new ImportWorkflowService($profesorService, $grupoService);
        $service->assignTutores();

        $this->assertSame($rolProfesor * $rolTutor * $rolPractiques, $profesorTutor->rol);
        $this->assertSame($rolProfesor * $rolTutor * $rolPractiques, $profesorSubstitut->rol);
        $this->assertSame($rolProfesor, $profesorNoTutor->rol);

        $this->assertSame(1, $profesorTutor->saved);
        $this->assertSame(1, $profesorSubstitut->saved);
        $this->assertSame(1, $profesorNoTutor->saved);
    }

    private function buildService(): ImportWorkflowService
    {
        $profesorService = Mockery::mock(ProfesorService::class);
        $grupoService = Mockery::mock(GrupoService::class);

        return new ImportWorkflowService($profesorService, $grupoService);
    }

    private function createTempXml(string $xml): string
    {
        $path = tempnam(sys_get_temp_dir(), 'import_workflow_');
        if ($path === false) {
            throw new \RuntimeException('No s\'ha pogut crear fitxer temporal');
        }

        file_put_contents($path, $xml);

        return $path;
    }

    private function newProfesorStub(string $dni, int $rol, ?string $sustituyeA = null): object
    {
        return new class($dni, $rol, $sustituyeA) {
            public string $dni;
            public int $rol;
            public ?string $sustituye_a;
            public int $saved = 0;

            public function __construct(string $dni, int $rol, ?string $sustituyeA)
            {
                $this->dni = $dni;
                $this->rol = $rol;
                $this->sustituye_a = $sustituyeA;
            }

            public function save(): void
            {
                $this->saved++;
            }
        };
    }
}
