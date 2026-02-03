<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intranet\Botones\BotonBasico;
use Intranet\Botones\BotonConfirmacion;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonPost;
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
