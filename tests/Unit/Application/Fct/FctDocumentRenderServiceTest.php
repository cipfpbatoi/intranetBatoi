<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Fct;

use Illuminate\Http\JsonResponse;
use Intranet\Application\Fct\FctDocumentRenderService;
use Intranet\Services\Document\DocumentService;
use Mockery;
use Tests\TestCase;

class FctDocumentRenderServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_render_by_request_returns_back_with_error_when_document_service_fails(): void
    {
        config()->set('fctEmails.testMail', [
            'email' => ['subject' => 'Test'],
            'modelo' => 'Documento',
            'template' => 'email.test',
            'route' => 'fct',
        ]);

        $mock = Mockery::mock(DocumentService::class);
        $mock->shouldReceive('render')
            ->once()
            ->andReturn(new JsonResponse(['error' => 'Boom'], 400));

        $this->app->instance(DocumentService::class, $mock);

        $service = new class extends FctDocumentRenderService {
            protected function makeDocumentService($finder): DocumentService
            {
                return app(DocumentService::class);
            }
        };

        $request = request()->create('/documentacionFCT/testMail', 'POST', ['10' => 'on']);
        $response = $service->renderByRequest($request, 'testMail');

        $this->assertTrue(method_exists($response, 'getTargetUrl'));
    }
}
