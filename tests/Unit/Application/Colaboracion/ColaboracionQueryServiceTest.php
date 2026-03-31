<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Colaboracion;

use Illuminate\Support\Collection;
use Intranet\Application\Colaboracion\ColaboracionQueryService;
use Intranet\Application\Seguimiento\SeguimientoService;
use Intranet\Entities\Activity;
use Intranet\Entities\Colaboracion;
use Tests\TestCase;

class ColaboracionQueryServiceTest extends TestCase
{
    private function fakeEmpresa(): object
    {
        return new class() {
            public string $concierto = 'OK';
            public string $fichero = 'empresa.pdf';

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
}
