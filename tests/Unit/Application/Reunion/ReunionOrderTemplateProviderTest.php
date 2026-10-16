<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reunion;

use Intranet\Application\Reunion\OrderResolvers\LearningDifficultiesOrderResolver;
use Intranet\Application\Reunion\OrderResolvers\LoeProjectStudentsOrderResolver;
use Intranet\Application\Reunion\ReunionOrderTemplate;
use Intranet\Application\Reunion\ReunionOrderTemplateProvider;
use Intranet\Entities\OrdenReunion;
use Intranet\Entities\Reunion;
use Tests\TestCase;

/**
 * Proves de les plantilles tipades dels punts de reunió.
 */
class ReunionOrderTemplateProviderTest extends TestCase
{
    public function test_tipus_avaluacio_declara_codis_i_resolutor_explicit(): void
    {
        $templates = (new ReunionOrderTemplateProvider())->for(new Reunion(['tipo' => 7]));

        $this->assertContainsOnlyInstancesOf(ReunionOrderTemplate::class, $templates);
        $nese = collect($templates)->firstWhere('code', OrdenReunion::CODE_NESE_FOLLOW_UP);
        $this->assertSame(LearningDifficultiesOrderResolver::KEY, $nese?->resolver);
        $this->assertSame(
            'Alumnes amb dificultats acadèmiques i mesures a adoptar',
            $nese?->description
        );
    }

    public function test_projectes_usen_resolutor_sense_dsl_en_configuracio(): void
    {
        $templates = (new ReunionOrderTemplateProvider())->for(new Reunion(['tipo' => 11]));

        $this->assertCount(1, $templates);
        $this->assertSame(LoeProjectStudentsOrderResolver::KEY, $templates[0]->resolver);
        $this->assertSame(OrdenReunion::CODE_PROJECT_PROPOSAL_STUDENT, $templates[0]->code);
        $this->assertStringNotContainsString('->', serialize(config('tablas.tipoReunion')));
    }

    public function test_adaptador_llegat_no_executa_un_dsl_desconegut(): void
    {
        config()->set('tablas.tipoReunion.2.ordenes', ['ClassePerillosa->executa->camp']);
        config()->set('tablas.tipoReunion.2.resumen', 'Text inicial');

        $templates = (new ReunionOrderTemplateProvider())->for(new Reunion(['tipo' => 2]));

        $this->assertNull($templates[0]->resolver);
        $this->assertNull($templates[0]->code);
        $this->assertSame('ClassePerillosa->executa->camp', $templates[0]->description);
    }
}
