<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intranet\UI\Botones\BotonBasico;
use Intranet\UI\Botones\BotonConfirmacion;
use Intranet\UI\Botones\BotonIcon;
use Intranet\UI\Botones\BotonImg;
use Intranet\UI\Botones\BotonPost;
use Intranet\Entities\Profesor;
use Tests\TestCase;

class BotonesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('app.url', 'http://intranet.test');
        config()->set('iconos.edit', 'fa-edit');
        config()->set('iconos.delete', 'fa-trash');

        $user = new Profesor([
            'dni' => '123A',
            'rol' => 3,
        ]);
        Auth::guard('profesor')->setUser($user);
    }

    public function test_boton_basico_renderitza_href_clase_i_text(): void
    {
        $boto = new BotonBasico('profesor.edit', [
            'text' => 'Editar',
            'class' => 'btn-warning',
            'id' => 'edit-',
            'icon' => 'fa-star',
            'data-test' => 'ok',
            'roles' => 3,
        ]);

        $html = $boto->render();

        $this->assertStringContainsString("href=\"http://intranet.test/profesor/edit\"", $html);
        $this->assertStringContainsString("class=\"btn-warning btn btn-round txtButton\"", $html);
        $this->assertStringContainsString("id=\"edit-\"", $html);
        $this->assertStringContainsString("data-test='ok'", $html);
        $this->assertStringContainsString("fa-star", $html);
        $this->assertStringContainsString("Editar", $html);
    }

    public function test_boton_confirmacio_afegix_clase_confirm(): void
    {
        $boto = new BotonConfirmacion('profesor.edit', [
            'text' => 'Confirmar',
            'roles' => 3,
        ]);

        $html = $boto->render();

        $this->assertStringContainsString('btn txtButton confirm', $html);
    }

    public function test_boton_icon_usa_icono_config_i_id(): void
    {
        $boto = new BotonIcon('profesor.edit', [
            'text' => 'Editar',
            'id' => 'edit-',
            'roles' => 3,
        ]);

        $html = $boto->render($this->makeElement(['id' => 9]));

        $this->assertStringContainsString("href=\"http://intranet.test/profesor/9/edit\"", $html);
        $this->assertStringContainsString("id=\"edit-9\"", $html);
        $this->assertStringContainsString("fa fa-edit", $html);
        $this->assertStringContainsString("Editar", $html);
    }

    public function test_boton_img_usa_img_config_i_id(): void
    {
        $boto = new BotonImg('profesor.delete', [
            'text' => 'Borrar',
            'roles' => 3,
        ]);

        $html = $boto->render($this->makeElement(['id' => 5]));

        $this->assertStringContainsString("href=\"http://intranet.test/profesor/5/delete\"", $html);
        $this->assertStringContainsString("id=\"delete5\"", $html);
        $this->assertStringContainsString("fa fa-trash", $html);
        $this->assertStringContainsString("Borrar", $html);
    }

    public function test_boton_post_renderitza_input_submit(): void
    {
        $boto = new BotonPost('profesor.edit', [
            'text' => 'Guardar',
            'id' => 'save',
            'roles' => 3,
            'data-one' => '1',
            'data-two' => '2',
        ]);

        $html = $boto->render();

        $this->assertStringContainsString("type='submit'", $html);
        $this->assertStringContainsString("value=\"Guardar\"", $html);
        $this->assertStringContainsString("id=\"save\"", $html);
        $this->assertStringContainsString("data-one='1'", $html);
        $this->assertStringContainsString("data-two='2'", $html);
    }

    public function test_boton_disabled_no_te_href_actiu_i_marca_classe(): void
    {
        $boto = new BotonBasico('profesor.edit', [
            'text' => 'Desactivat',
            'roles' => 3,
            'disabled' => true,
        ]);

        $html = $boto->render();

        $this->assertStringContainsString("href=\"#\"", $html);
        $this->assertStringContainsString("disabled", $html);
    }

    public function test_boton_basico_admet_target_rel_i_aria_label(): void
    {
        $boto = new BotonBasico('profesor.edit', [
            'text' => 'Obertura',
            'roles' => 3,
            'target' => '_blank',
            'rel' => 'noopener',
            'aria-label' => 'Obrir en nova finestra',
            'title' => 'Títol',
        ]);

        $html = $boto->render();

        $this->assertStringContainsString('target="_blank"', $html);
        $this->assertStringContainsString('rel="noopener"', $html);
        $this->assertStringContainsString('aria-label="Obrir en nova finestra"', $html);
        $this->assertStringContainsString('title="Títol"', $html);
    }

    public function test_boton_admet_data_confirm(): void
    {
        $boto = new BotonBasico('profesor.delete', [
            'text' => 'Eliminar',
            'roles' => 3,
            'data-confirm' => 'Segur que vols eliminar?',
        ]);

        $html = $boto->render();

        $this->assertStringContainsString("data-confirm='Segur que vols eliminar?'", $html);
    }

    public function test_boton_admet_data_loading_text(): void
    {
        $boto = new BotonPost('profesor.edit', [
            'text' => 'Enviar',
            'roles' => 3,
            'data-loading-text' => 'Enviant...',
        ]);

        $html = $boto->render();

        $this->assertStringContainsString("data-loading-text='Enviant...'", $html);
    }

    public function test_boton_admet_badge(): void
    {
        $boto = new BotonBasico('mensajes.index', [
            'text' => 'Missatges',
            'roles' => 3,
            'badge' => 3,
        ]);

        $html = $boto->render();

        $this->assertStringContainsString('<span class="badge">3</span>', $html);
    }

    public function test_boton_elemento_respecta_where(): void
    {
        $boto = new BotonIcon('profesor.edit', [
            'text' => 'Editar',
            'roles' => 3,
            'where' => ['estado', '==', 'ok'],
        ]);

        $html = $boto->render($this->makeElement(['id' => 1, 'estado' => 'ko']));

        $this->assertSame('', $html);
    }

    public function test_boton_elemento_orwhere_existe(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('fitxers/1.txt', 'ok');

        $boto = new BotonImg('profesor.edit', [
            'text' => 'Veure',
            'roles' => 3,
            'orWhere' => ['id', 'existe', 'fitxers/$.txt'],
        ]);

        $html = $boto->render($this->makeElement(['id' => 1]));

        $this->assertStringContainsString('Veure', $html);
    }

    public function test_boton_elemento_orwhere_no_compleix_no_renderitza(): void
    {
        $boto = new BotonImg('profesor.edit', [
            'text' => 'Veure',
            'roles' => 3,
            'orWhere' => ['id', '==', 2],
        ]);

        $html = $boto->render($this->makeElement(['id' => 1]));

        $this->assertSame('', $html);
    }

    public function test_boton_icon_sense_id_no_pinta_id_buit(): void
    {
        $boto = new BotonIcon('profesor.edit', [
            'text' => 'Editar',
            'roles' => 3,
        ]);

        $html = $boto->render($this->makeElement(['id' => 9]));

        $this->assertStringNotContainsString('id=""', $html);
    }

    public function test_boton_post_sense_id_no_pinta_id_buit(): void
    {
        $boto = new BotonPost('profesor.edit', [
            'text' => 'Guardar',
            'roles' => 3,
        ]);

        $html = $boto->render();

        $this->assertStringNotContainsString('id=""', $html);
    }

    private function makeElement(array $values): object
    {
        return new class($values) {
            public array $values;

            public function __construct(array $values)
            {
                $this->values = $values;
                foreach ($values as $key => $value) {
                    $this->$key = $value;
                }
            }

            public function getKey()
            {
                return $this->values['id'] ?? null;
            }
        };
    }
}
