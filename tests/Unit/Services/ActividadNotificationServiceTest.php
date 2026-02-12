<?php

namespace Tests\Unit\Services;

use Illuminate\Support\Collection;
use Intranet\Entities\Actividad;
use Intranet\Entities\Profesor;
use Intranet\Services\Notifications\ActividadNotificationService;
use Intranet\Services\Notifications\NotificationService;
use Mockery;
use Tests\TestCase;

class ActividadNotificationServiceTest extends TestCase
{
    public function test_notify_activity_envia_missatges_a_profes_grups_i_participants(): void
    {
        $actividad = new Actividad([
            'name' => 'Eixida',
            'desde' => '2026-02-12 08:00:00',
            'hasta' => '2026-02-12 12:00:00',
        ]);
        $actividad->setRelation('grupos', collect([
            (object) ['codigo' => 'G1', 'nombre' => '1A'],
            (object) ['codigo' => 'G2', 'nombre' => '2B'],
        ]));
        $actividad->setRelation('profesores', collect([
            (object) ['dni' => 'P10', 'shortName' => 'Ana P.'],
            (object) ['dni' => 'P11', 'shortName' => 'Biel Q.'],
        ]));

        $coordinador = new Profesor(['dni' => 'P99']);
        $coordinador->shortName = 'Coord Test';

        $notificationService = Mockery::mock(NotificationService::class);
        $notificationService->shouldReceive('send')->twice();

        $adviseCalls = [];
        $service = new ActividadNotificationService(
            notificationService: $notificationService,
            groupTeachersResolver: static fn (string $groupCode): Collection => collect([
                (object) ['dni' => $groupCode . '-PROFE'],
            ]),
            adviseTeacherExecutor: static function (
                object $actividadArg,
                string $mensaje,
                string $dni,
                mixed $emisor
            ) use (&$adviseCalls): void {
                $adviseCalls[] = compact('mensaje', 'dni', 'emisor');
            }
        );

        $service->notifyActivity($actividad, $coordinador);

        $this->assertCount(2, $adviseCalls);
        $this->assertSame('P10', $adviseCalls[0]['dni']);
        $this->assertSame('Ana P.', $adviseCalls[0]['emisor']);
        $this->assertStringContainsString('Els grups: 1A, 2B', $adviseCalls[0]['mensaje']);
    }

    public function test_notify_activity_no_envia_a_grups_sense_profes_pero_si_a_participants(): void
    {
        $actividad = new Actividad([
            'name' => 'Visita',
            'desde' => '2026-02-12 10:00:00',
            'hasta' => '2026-02-12 11:00:00',
        ]);
        $actividad->setRelation('grupos', collect([
            (object) ['codigo' => 'G1', 'nombre' => '1A'],
        ]));
        $actividad->setRelation('profesores', collect([
            (object) ['dni' => 'P20', 'shortName' => 'Carla R.'],
        ]));

        $coordinador = new Profesor(['dni' => 'P99']);
        $coordinador->shortName = 'Coord Test';

        $notificationService = Mockery::mock(NotificationService::class);
        $notificationService->shouldReceive('send')->never();

        $adviseCalls = 0;
        $service = new ActividadNotificationService(
            notificationService: $notificationService,
            groupTeachersResolver: static fn (): Collection => collect(),
            adviseTeacherExecutor: static function () use (&$adviseCalls): void {
                $adviseCalls++;
            }
        );

        $service->notifyActivity($actividad, $coordinador);

        $this->assertSame(1, $adviseCalls);
    }
}

