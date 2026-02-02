<?php

namespace Tests\Unit\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Intranet\Services\StateService;
use Mockery;
use Tests\TestCase;

class StateServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_put_estado_actualitza_i_avisa()
    {
        Config::set('modelos.DummyStateElement', []);

        $element = new DummyStateElement(true, true);

        $service = new StateService($element);
        $result = $service->putEstado(2, 'Missatge');

        $this->assertSame(2, $result);
        $this->assertSame(2, $element->estado);
    }

    public function test_put_estado_amb_data_crea_document_i_actualitza_camps()
    {
        Config::set('modelos.DummyStateElement', ['mensaje' => 'observacions']);

        $element = new DummyStateElement(true, false);
        $element->fechasolucion = null;
        $element->fichero = 'doc.pdf';

        $gestor = Mockery::mock('overload:Intranet\\Services\\GestorService');
        $gestor->shouldReceive('save')
            ->once()
            ->with(Mockery::subset([
                'tipoDocumento' => 'DummyStateElement',
                'rol' => '2',
            ]));

        $service = new StateService($element);
        $service->putEstado(3, 'Text', '2025-01-01');

        $this->assertSame('2025-01-01', $element->fechasolucion);
        $this->assertSame('Text', $element->observacions);
    }

    public function test_resolve_retorna_false_si_no_hi_ha_config()
    {
        Config::set('modelos.DummyStateElement', []);

        $service = new StateService(new DummyStateElement());

        $this->assertFalse($service->resolve());
    }

    public function test_resolve_registra_info_si_falta_config()
    {
        Config::set('modelos.DummyStateElement', []);
        Log::shouldReceive('info')->once();

        $service = new StateService(new DummyStateElement());

        $this->assertFalse($service->resolve());
    }

    public function test_print_usa_resolve_si_es_igual()
    {
        Config::set('modelos.DummyStateElement', ['print' => 5, 'resolve' => 5]);

        $element = new DummyStateElement(true, false);
        $element->fechasolucion = null;

        $service = new StateService($element);
        $result = $service->_print();

        $this->assertSame(5, $result);
        $this->assertSame(hoy(), $element->fechasolucion);
    }

    public function test_print_registra_info_si_falta_config()
    {
        Config::set('modelos.DummyStateElement', []);
        Log::shouldReceive('info')->once();

        $service = new StateService(new DummyStateElement());

        $this->assertFalse($service->_print());
    }

    public function test_put_estado_retorna_false_si_estat_invalid()
    {
        Config::set('modelos.DummyStateElement', []);
        Log::shouldReceive('warning')->once();

        $service = new StateService(new DummyStateElement());

        $this->assertFalse($service->putEstado('x'));
    }

    public function test_put_estado_retorna_false_si_no_hi_ha_element()
    {
        Log::shouldReceive('warning')->once();
        \Intranet\Entities\DummyEntity::$store = [];

        $service = new StateService(\Intranet\Entities\DummyEntity::class, 999);

        $this->assertFalse($service->putEstado(1));
    }

    public function test_make_all_modifica_estat_de_tots_els_elements()
    {
        Config::set('modelos.DummyEntity', []);

        $element1 = new \Intranet\Entities\DummyEntity(1);
        $element2 = new \Intranet\Entities\DummyEntity(2);

        \Intranet\Entities\DummyEntity::$store = [
            1 => $element1,
            2 => $element2,
        ];

        StateService::makeAll(collect([$element1, $element2]), 7);

        $this->assertSame(7, $element1->estado);
        $this->assertSame(7, $element2->estado);
    }

    public function test_make_link_registra_error_si_no_pot_guardar()
    {
        Log::shouldReceive('error')->once();

        $ok = new DummyLinkElement(false);
        $fail = new DummyLinkElement(true);
        $doc = (object) ['id' => 10];

        StateService::makeLink(collect([$ok, $fail]), $doc);

        $this->assertSame($doc, $ok->idDocumento);
    }
}

class DummyStateElement
{
    public int $id = 1;
    public int $estado = 0;
    public ?string $fichero = null;
    public bool $acceptsFecha;
    public bool $acceptsMensaje;
    private array $data = [];

    public function __construct(bool $acceptsFecha = false, bool $acceptsMensaje = false)
    {
        $this->acceptsFecha = $acceptsFecha;
        $this->acceptsMensaje = $acceptsMensaje;
    }

    public function __isset(string $name): bool
    {
        if ($name === 'fechasolucion') {
            return $this->acceptsFecha;
        }
        if ($name === 'observacions') {
            return $this->acceptsMensaje;
        }
        return property_exists($this, $name);
    }

    public function __set(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    public function __get(string $name)
    {
        return $this->data[$name] ?? null;
    }

    public function save(): bool
    {
        return true;
    }
}

class DummyLinkElement
{
    public bool $throwOnSave;
    public $idDocumento;

    public function __construct(bool $throwOnSave)
    {
        $this->throwOnSave = $throwOnSave;
    }

    public function save(): bool
    {
        if ($this->throwOnSave) {
            throw new \Exception('Error guardant');
        }

        return true;
    }
}

namespace Intranet\Entities;

class DummyEntity
{
    public static array $store = [];
    public int $id;
    public int $estado = 0;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function find($id): ?self
    {
        return self::$store[$id] ?? null;
    }

    public function save(): bool
    {
        return true;
    }
}
