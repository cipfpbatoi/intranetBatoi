<?php

namespace Tests\Unit\Services;

use Intranet\Notifications\mensajePanel;
use Intranet\Services\Notifications\NotificationService;
use Mockery;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    public function testSendNotifiesReceptorWhenFound()
    {
        $receptor = Mockery::mock();
        $receptor->shouldReceive('notify')
            ->once()
            ->with(Mockery::type(mensajePanel::class));

        $service = new NotificationService(
            findAlumno: static fn ($id) => $id === '12345678' ? $receptor : null,
            findProfesor: static fn () => null,
            hasTable: static fn () => true,
            fechaProvider: static fn () => '10 de febrer de 2026'
        );
        $service->send('12345678', 'Hola', '#', 'Emisor');
    }

    public function testSendNotifiesEmisorWhenReceptorMissing()
    {
        $emisorUser = Mockery::mock();
        $emisorUser->shouldReceive('notify')
            ->once()
            ->with(Mockery::type(mensajePanel::class));

        $service = new NotificationService(
            findAlumno: static fn () => null,
            findProfesor: static fn ($id) => $id === 'P123' ? $emisorUser : null,
            hasTable: static fn () => true,
            fechaProvider: static fn () => '10 de febrer de 2026'
        );
        $service->send('12345678', 'Hola', '#', 'P123');
    }
}
