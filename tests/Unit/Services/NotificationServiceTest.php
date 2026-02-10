<?php

namespace Tests\Unit\Services;

use Intranet\Notifications\mensajePanel;
use Intranet\Services\Notifications\NotificationService;
use Mockery;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testSendNotifiesReceptorWhenFound()
    {
        $receptor = Mockery::mock();
        $receptor->shouldReceive('notify')
            ->once()
            ->with(Mockery::type(mensajePanel::class));

        $alumno = Mockery::mock('alias:Intranet\\Entities\\Alumno');
        $alumno->shouldReceive('find')
            ->with('12345678')
            ->andReturn($receptor);

        $service = new NotificationService();
        $service->send('12345678', 'Hola', '#', 'Emisor');
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testSendNotifiesEmisorWhenReceptorMissing()
    {
        $alumno = Mockery::mock('alias:Intranet\\Entities\\Alumno');
        $alumno->shouldReceive('find')
            ->with('12345678')
            ->andReturn(null);

        $emisorUser = Mockery::mock();
        $emisorUser->shouldReceive('notify')
            ->once()
            ->with(Mockery::type(mensajePanel::class));

        $profesor = Mockery::mock('alias:Intranet\\Entities\\Profesor');
        $profesor->shouldReceive('find')
            ->with('P123')
            ->andReturn($emisorUser);

        $service = new NotificationService();
        $service->send('12345678', 'Hola', '#', 'P123');
    }
}
