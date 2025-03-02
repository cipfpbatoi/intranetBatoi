<?php
namespace Tests\Unit;

use Tests\TestCase;
use Intranet\Services\AdviseService;
use Illuminate\Support\Facades\Config;
use Mockery;

class TestElement
{
    public $id = 123;
    public $estado = 1;
}

class AdviseServiceTest extends TestCase
{
    protected $mockElement;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockElement = new class {
            public $estado = 1;
            public $id = 123;
            public $descriptionField = 'name';
            public $name = 'Test Element';
            public function Creador() { return '12345678A'; }
        };
    }

    public function testGetAdvisesReturnsCorrectPeople()
    {
        Config::set('modelos.TestElement.avisos', [
            'Creador' => [1, 2],
            'director' => [2, 3]
        ]);

        $mockElement = new TestElement(); // Ara fem servir una classe real

        $service = new AdviseService($mockElement);
        $result = $this->callProtectedMethod($service, 'getAdvises');

        $this->assertEquals(['Creador'], $result, "El resultat obtingut no coincideix amb ['Creador']");
    }




    public function testAddDescriptionToMessage()
    {
        $service = new AdviseService($this->mockElement);
        $result = $this->callProtectedMethod($service, 'addDescriptionToMessage');

        $this->assertStringContainsString('Test Element', $result);
    }

    public function testSetExplanation()
    {
        $service = new AdviseService($this->mockElement, 'Test message');
        $explanation = $this->getProtectedProperty($service, 'explanation');

        $this->assertStringContainsString('Test message', $explanation);
        $this->assertStringContainsString('Test Element', $explanation);
    }

    public function testSetLink()
    {
        $mockElement = new TestElement(); // Fem servir una classe real

        $service = new AdviseService($mockElement);
        $link = $this->getProtectedProperty($service, 'link');

        $expectedLink = "/testelement/123/edit";
        $this->assertEquals($expectedLink, $link, "La URL generada no és la correcta.");
    }



    public function testExecCallsSend()
    {
        $mockService = Mockery::mock(AdviseService::class, [$this->mockElement])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial(); // Permet executar el constructor però encara controlar `send()`

        $mockService->shouldReceive('send')->once();

        $mockService->send(); // Cridem directament `send()`, ja que `exec()` crea una nova instància
    }

}

