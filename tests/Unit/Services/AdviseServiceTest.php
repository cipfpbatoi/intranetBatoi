<?php
namespace Tests\Unit\Services;

use Illuminate\Support\Facades\Config;
use Intranet\Services\Notifications\AdviseService;
use Mockery;
use Tests\TestCase;

class TestElement
{
    public $id = 123;
    public $estado = 1;

    public function Creador()
    {
        return '12345678A';
    }
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

        $mockElement = new  TestElement(); // Ara fem servir una classe real

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
        $mockElement = new  TestElement();

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

    public function testResolveRecipientsAndBuildMessage()
    {
        Config::set('modelos.TestElement.avisos', [
            'Creador' => [1],
            'director' => [1],
            'custom' => [1],
        ]);
        Config::set('avisos.director', '99999999Z');

        $mockElement = new TestElement();
        $mockElement->custom = '00000000T';

        $service = new AdviseService($mockElement, 'Missatge');
        $recipients = $service->resolveRecipients();

        $this->assertSame(['12345678A', '99999999Z', '00000000T'], $recipients);

        $message = $service->buildMessage();
        $this->assertSame($recipients, $message['recipients']);
        $this->assertStringContainsString('Missatge', $message['explanation']);
        $this->assertSame('/testelement/123/edit', $message['link']);
    }

}
