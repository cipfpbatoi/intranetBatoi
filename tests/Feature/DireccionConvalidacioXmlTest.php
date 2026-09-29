<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Intranet\Application\Convalidacio\ConvalidacioXmlManager;
use Intranet\Http\Middleware\RoleMiddleware;
use Intranet\Http\Middleware\VerifyCsrfToken;
use Tests\TestCase;

/** Regressió del gestor privat d'XML de convalidacions. */
class DireccionConvalidacioXmlTest extends TestCase
{
    private ConvalidacioXmlManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('convalidacions_xml');
        $this->manager = app(ConvalidacioXmlManager::class);
    }

    public function test_el_gestor_esta_integrat_en_les_rutes_de_direccio_i_no_te_descarrega(): void
    {
        $route = Route::getRoutes()->getByName('convalidacions.direction.xml.index');

        $this->assertNotNull($route);
        $this->assertSame('direccion/convalidacions/xml', $route->uri());
        $this->assertContains('role:direccion', $route->gatherMiddleware());
        $this->assertNull(Route::getRoutes()->getByName('convalidacions.direction.xml.download'));
        $this->assertNull(Route::getRoutes()->getByName('convalidacions.direction.xml.replace'));
        $this->assertNull(Route::getRoutes()->getByName('convalidacions.xml.index'));
    }

    public function test_direccio_pot_incorporar_i_eliminar_un_xml_ordenat_per_any(): void
    {
        $this->withoutMiddleware([RoleMiddleware::class, VerifyCsrfToken::class]);

        $this->post(route('convalidacions.direction.xml.store'), [
            'xml' => UploadedFile::fake()->createWithContent('avaluacio-2025.xml', $this->xml('6', '2024')),
        ])->assertSessionHasNoErrors();

        $fitxer = $this->manager->all()[0];
        $this->assertSame(2024, $fitxer['any']);
        $this->assertSame('avaluacio-2025.xml', $fitxer['nom']);
        Storage::disk('convalidacions_xml')->assertExists($fitxer['nom']);

        $this->manager->store(UploadedFile::fake()->createWithContent('avaluacio-2024.xml', $this->xml('8', '2025')));
        $this->assertSame([2025, 2024], array_column($this->manager->all(), 'any'));

        $this->delete(route('convalidacions.direction.xml.destroy', $fitxer['id']))->assertSessionHasNoErrors();
        Storage::disk('convalidacions_xml')->assertMissing($fitxer['nom']);
        $this->assertSame([2025], array_column($this->manager->all(), 'any'));
    }

    public function test_accepta_una_exportacio_xml_independentment_del_mime_informat(): void
    {
        $this->withoutMiddleware([RoleMiddleware::class, VerifyCsrfToken::class]);
        $temporal = tempnam(sys_get_temp_dir(), 'itaca-');
        $this->assertNotFalse($temporal);
        file_put_contents($temporal, $this->xml('7'));

        try {
            $fitxer = new UploadedFile(
                $temporal,
                'avaluacio-itaca.xml',
                'application/vnd.itaca+xml',
                UPLOAD_ERR_OK,
                true
            );

            $this->post(route('convalidacions.direction.xml.store'), ['xml' => $fitxer])
                ->assertSessionHasNoErrors();

            Storage::disk('convalidacions_xml')->assertExists('avaluacio-itaca.xml');
        } finally {
            if (is_file($temporal)) {
                unlink($temporal);
            }
        }
    }

    public function test_la_nova_interficie_accepta_un_sol_xml_com_a_llista(): void
    {
        $this->withoutMiddleware([RoleMiddleware::class, VerifyCsrfToken::class]);

        $this->post(route('convalidacions.direction.xml.store'), [
            'xml' => [UploadedFile::fake()->createWithContent('unica.xml', $this->xml('7'))],
        ])->assertSessionHasNoErrors();

        Storage::disk('convalidacions_xml')->assertExists('unica.xml');
        $this->assertSame(['unica.xml'], session('resultatPujadaXml.afegits'));
    }

    public function test_la_pujada_multiple_guarda_els_valids_i_retorna_errors_individuals(): void
    {
        $this->withoutMiddleware([RoleMiddleware::class, VerifyCsrfToken::class]);

        $response = $this->post(route('convalidacions.direction.xml.store'), [
            'xml' => [
                UploadedFile::fake()->createWithContent('correcte.xml', $this->xml('6')),
                UploadedFile::fake()->createWithContent('incorrecte.xml', '<xml/>'),
            ],
        ]);

        $response->assertSessionHas('resultatPujadaXml', function (array $resultat): bool {
            return $resultat['afegits'] === ['correcte.xml']
                && count($resultat['errors']) === 1
                && $resultat['errors'][0]['nom'] === 'incorrecte.xml';
        });
        Storage::disk('convalidacions_xml')->assertExists('correcte.xml');
        Storage::disk('convalidacions_xml')->assertMissing('incorrecte.xml');
    }

    public function test_rebutja_una_pujada_que_supera_el_maxim_de_fitxers(): void
    {
        $this->withoutMiddleware([RoleMiddleware::class, VerifyCsrfToken::class]);
        $files = [];
        for ($i = 1; $i <= 21; $i++) {
            $files[] = UploadedFile::fake()->createWithContent('avaluacio-' . $i . '.xml', $this->xml('6'));
        }

        $this->post(route('convalidacions.direction.xml.store'), ['xml' => $files])
            ->assertSessionHasErrors('xml');

        $this->assertSame([], $this->manager->all());
    }

    public function test_rebutja_una_avaluacio_sense_curs_academic(): void
    {
        $this->withoutMiddleware([RoleMiddleware::class, VerifyCsrfToken::class]);
        $xml = str_replace(' curso="2025"', '', $this->xml('6'));

        $this->post(route('convalidacions.direction.xml.store'), [
            'xml' => UploadedFile::fake()->createWithContent('sense-any.xml', $xml),
        ])->assertSessionHas('resultatPujadaXml', function (array $resultat): bool {
            return $resultat['afegits'] === []
                && count($resultat['errors']) === 1
                && $resultat['errors'][0]['nom'] === 'sense-any.xml';
        });

        Storage::disk('convalidacions_xml')->assertMissing('sense-any.xml');
    }

    /** XML sintètic mínim acceptat pel gestor. */
    private function xml(string $nota, string $any = '2025'): string
    {
        return <<<XML
<?xml version="1.0"?>
<centro curso="{$any}">
  <cursos><curso codigo="C" padre="" nombre_val="Cicle"/></cursos>
  <contenidos><contenido curso="C" codigo="M" nombre_val="Mòdul"/></contenidos>
  <calificaciones><calificacion alumno="12345678" curso="C" contenido="M" evaluacion="FI" nota_numerica="{$nota}"/></calificaciones>
</centro>
XML;
    }
}
