<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Colaboracion;

use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Intranet\Application\Colaboracion\ColaboracionQueryService;
use Intranet\Application\Seguimiento\SeguimientoService;
use Intranet\Entities\Activity;
use Intranet\Entities\Colaboracion;
use Tests\TestCase;

class ColaboracionQueryServiceTest extends TestCase
{
    private function fakeEmpresa(?string $nifGerente = '12345678Z'): object
    {
        return new class($nifGerente) {
            public int $id = 25;
            public string $concierto = 'OK';
            public string $fichero = 'empresa.pdf';
            public ?string $nif_gerente;

            public function __construct(?string $nifGerente)
            {
                $this->nif_gerente = $nifGerente;
            }

            public function getRawOriginal(string $key): ?string
            {
                return $key === 'data_signatura' ? now()->format('Y-m-d') : null;
            }
        };
    }

    /**
     * Verifica que el servici hidrata els contactes de la col·laboració principal
     * i també de les relacionades, evitant consultes posteriors des de Blade.
     */
    public function test_attach_related_and_contacts_hidrata_contactes_principals_i_relacionats(): void
    {
        $service = new ColaboracionQueryService(app(SeguimientoService::class));

        $meua = new Colaboracion();
        $meua->id = 1;
        $meua->idCentro = 10;
        $meua->empresa = 'Empresa A';
        $meua->setRelation('Ciclo', (object) ['departamento' => 'INF']);
        $meua->setRelation('Centro', (object) [
            'Empresa' => $this->fakeEmpresa(),
            'instructores' => collect([(object) ['id' => 1]]),
        ]);
        $meua->setRelation('fcts', collect());

        $relacionada = new Colaboracion();
        $relacionada->id = 2;
        $relacionada->idCentro = 10;
        $relacionada->empresa = 'Empresa A';
        $relacionada->setRelation('Ciclo', (object) ['departamento' => 'INF']);
        $relacionada->setRelation('Centro', (object) [
            'Empresa' => $this->fakeEmpresa(),
            'instructores' => collect([(object) ['id' => 1]]),
        ]);
        $relacionada->setRelation('fcts', collect());

        $contacteMeu = new Activity();
        $contacteMeu->id = 101;

        $contacteRelacionat = new Activity();
        $contacteRelacionat->id = 202;

        $activitiesByColab = new Collection([
            1 => collect([$contacteMeu]),
            2 => collect([$contacteRelacionat]),
        ]);

        $resultat = $service->attachRelatedAndContacts(
            collect([$meua]),
            collect([$relacionada]),
            $activitiesByColab
        );

        $this->assertCount(1, $resultat);
        $this->assertSame([$contacteMeu], $resultat->first()->contactos->all());
        $this->assertSame($contacteMeu, $resultat->first()->ultimaActividad);
        $this->assertCount(1, $resultat->first()->relacionadas);
        $this->assertSame(
            $contacteRelacionat,
            $resultat->first()->relacionadas->first()->ultimaActividad
        );
        $this->assertSame(
            [$contacteRelacionat],
            $resultat->first()->relacionadas->first()->contactos->all()
        );
    }

    public function test_marca_i_enllaca_les_empreses_sense_nif_del_gerent(): void
    {
        $service = new ColaboracionQueryService(app(SeguimientoService::class));
        $colaboracion = new Colaboracion();
        $colaboracion->id = 3;
        $colaboracion->idCentro = 30;
        $colaboracion->contacto = 'Contacte';
        $colaboracion->telefono = '600000000';
        $colaboracion->email = 'empresa@test.local';
        $colaboracion->setRelation('Centro', (object) [
            'Empresa' => $this->fakeEmpresa(null),
            'instructores' => collect([(object) ['id' => 1]]),
        ]);
        $colaboracion->setRelation('fcts', collect());

        $resultat = $service->attachRelatedAndContacts(
            collect([$colaboracion]),
            collect(),
            collect([3 => collect()])
        )->first();

        $badge = $resultat->fitxaBadges->firstWhere('label', 'Falta NIF del gerent');
        $this->assertNotNull($badge);
        $this->assertSame(route('empresa.edit', ['empresa' => 25]), $badge['url']);
        $this->assertTrue($resultat->fitxaIncompleta);
    }

    public function test_no_marca_les_empreses_amb_nif_del_gerent(): void
    {
        $service = new ColaboracionQueryService(app(SeguimientoService::class));
        $colaboracion = new Colaboracion();
        $colaboracion->id = 4;
        $colaboracion->idCentro = 40;
        $colaboracion->setRelation('Centro', (object) [
            'Empresa' => $this->fakeEmpresa(),
            'instructores' => collect([(object) ['id' => 1]]),
        ]);
        $colaboracion->setRelation('fcts', collect());

        $resultat = $service->attachRelatedAndContacts(
            collect([$colaboracion]),
            collect(),
            collect([4 => collect()])
        )->first();

        $this->assertNull($resultat->fitxaBadges->firstWhere('label', 'Falta NIF del gerent'));
    }

    /** Marca com a inactives les col·laboracions sense FCT durant el curs actual ni l'anterior. */
    public function test_detecta_dos_cursos_sense_activitat_fct(): void
    {
        Carbon::setTestNow('2026-10-18 12:00:00');

        try {
            $service = new ColaboracionQueryService(app(SeguimientoService::class));
            $recent = $this->colaboracionAmbUltimaFct(10, '2025-09-01');
            $old = $this->colaboracionAmbUltimaFct(11, '2025-08-31');
            $never = $this->colaboracionAmbUltimaFct(12, null);

            $resultat = $service->attachRelatedAndContacts(
                collect([$recent, $old, $never]),
                collect(),
                collect([
                    10 => collect(),
                    11 => collect(),
                    12 => collect(),
                ])
            )->keyBy('id');

            $this->assertFalse($resultat[10]->senseActivitatRecent);
            $this->assertTrue($resultat[11]->senseActivitatRecent);
            $this->assertTrue($resultat[12]->senseActivitatRecent);
            $this->assertSame('2025-09-01', $resultat[10]->ultimaFctData?->format('Y-m-d'));
            $this->assertSame('2025-08-31', $resultat[11]->ultimaFctData?->format('Y-m-d'));
            $this->assertNull($resultat[12]->ultimaFctData);
        } finally {
            Carbon::setTestNow();
        }
    }

    /** Crea una col·laboració mínima amb una data final d'FCT opcional. */
    private function colaboracionAmbUltimaFct(int $id, ?string $hasta): Colaboracion
    {
        $colaboracion = new Colaboracion();
        $colaboracion->id = $id;
        $colaboracion->idCentro = $id;
        $colaboracion->setRelation('Centro', (object) [
            'Empresa' => $this->fakeEmpresa(),
            'instructores' => collect([(object) ['id' => 1]]),
        ]);
        $colaboracion->setRelation('fcts', $hasta === null
            ? collect()
            : collect([(object) [
                'id' => $id * 10,
                'AlFct' => collect([(object) ['hasta' => $hasta]]),
            ]]));

        return $colaboracion;
    }
}
