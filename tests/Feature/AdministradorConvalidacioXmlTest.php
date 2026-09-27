<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Intranet\Application\Convalidacio\ConvalidacioException;
use Intranet\Application\Convalidacio\ConvalidacioXmlManager;
use Intranet\Http\Middleware\RoleMiddleware;
use Intranet\Http\Middleware\VerifyCsrfToken;
use Tests\TestCase;

/** Regressió del gestor privat d'XML de convalidacions. */
class AdministradorConvalidacioXmlTest extends TestCase
{
    private ConvalidacioXmlManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('convalidacions_xml');
        $this->manager = app(ConvalidacioXmlManager::class);
    }

    public function test_les_rutes_son_administratives_i_no_hi_ha_descarrega(): void
    {
        $route = Route::getRoutes()->getByName('convalidacions.xml.index');

        $this->assertNotNull($route);
        $this->assertContains('role:administrador', $route->gatherMiddleware());
        $this->assertNull(Route::getRoutes()->getByName('convalidacions.xml.download'));
    }

    public function test_administracio_pot_incorporar_substituir_i_eliminar_un_xml(): void
    {
        $this->withoutMiddleware([RoleMiddleware::class, VerifyCsrfToken::class]);

        $this->post(route('convalidacions.xml.store'), [
            'xml' => UploadedFile::fake()->createWithContent('avaluacio-2025.xml', $this->xml('6')),
        ])->assertSessionHasNoErrors();

        $fitxer = $this->manager->all()[0];
        Storage::disk('convalidacions_xml')->assertExists($fitxer['nom']);

        $this->put(route('convalidacions.xml.replace', $fitxer['id']), [
            'xml' => UploadedFile::fake()->createWithContent('nova.xml', $this->xml('8')),
        ])->assertSessionHasNoErrors();
        $this->assertStringContainsString('nota_numerica="8"', Storage::disk('convalidacions_xml')->get($fitxer['nom']));

        $this->delete(route('convalidacions.xml.destroy', $fitxer['id']))->assertSessionHasNoErrors();
        Storage::disk('convalidacions_xml')->assertMissing($fitxer['nom']);
    }

    public function test_un_reemplaçament_invalid_no_altera_el_fitxer_anterior(): void
    {
        $this->manager->store(UploadedFile::fake()->createWithContent('avaluacio.xml', $this->xml('6')));
        $fitxer = $this->manager->all()[0];
        $anterior = Storage::disk('convalidacions_xml')->get($fitxer['nom']);

        try {
            $this->manager->replace(
                $fitxer['id'],
                UploadedFile::fake()->createWithContent('invalid.xml', '<centro/>')
            );
            $this->fail('El reemplaçament invàlid havia de fallar.');
        } catch (ConvalidacioException) {
            $this->assertSame($anterior, Storage::disk('convalidacions_xml')->get($fitxer['nom']));
        }
    }

    /** XML sintètic mínim acceptat pel gestor. */
    private function xml(string $nota): string
    {
        return <<<XML
<?xml version="1.0"?>
<centro>
  <cursos><curso codigo="C" padre="" nombre_val="Cicle"/></cursos>
  <contenidos><contenido curso="C" codigo="M" nombre_val="Mòdul"/></contenidos>
  <calificaciones><calificacion alumno="12345678" curso="C" contenido="M" evaluacion="FI" nota_numerica="{$nota}"/></calificaciones>
</centro>
XML;
    }
}
