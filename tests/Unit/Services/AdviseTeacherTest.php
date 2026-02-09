<?php

namespace Tests\Unit\Services;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Intranet\Jobs\SendEmail;
use Intranet\Notifications\mensajePanel;
use Intranet\Services\Notifications\AdviseTeacher;
use Tests\TestCase;

class AdviseTeacherTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('horarios');
        $schema->dropIfExists('horas');
        $schema->dropIfExists('grupos');
        $schema->dropIfExists('profesores');

        $schema->create('profesores', function (Blueprint $table): void {
            $table->string('dni')->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('email')->nullable();
            $table->unsignedInteger('rol')->default(3);
            $table->string('sustituye_a')->nullable();
            $table->timestamps();
        });

        $schema->create('grupos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('tutor')->nullable();
            $table->timestamps();
        });

        $schema->create('horas', function (Blueprint $table): void {
            $table->unsignedInteger('codigo')->primary();
            $table->string('hora_ini');
            $table->string('hora_fin');
            $table->string('turno')->nullable();
        });

        $schema->create('horarios', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor');
            $table->string('idGrupo')->nullable();
            $table->string('dia_semana');
            $table->unsignedInteger('sesion_orden');
            $table->string('ocupacion')->nullable();
            $table->string('modulo')->nullable();
            $table->timestamps();
        });

        DB::connection('sqlite')->table('profesores')->insert([
            [
                'dni' => '2',
                'nombre' => 'Profe',
                'apellido1' => 'Receptor',
                'apellido2' => 'Test',
                'email' => 'professor@example.com',
                'rol' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dni' => '3',
                'nombre' => 'Profe',
                'apellido1' => 'Emisor',
                'apellido2' => 'Test',
                'email' => 'emisor@example.com',
                'rol' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dni' => '10',
                'nombre' => 'Tutor',
                'apellido1' => 'Grup',
                'apellido2' => 'Test',
                'email' => 'tutor@example.com',
                'rol' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::connection('sqlite')->table('grupos')->insert([
            [
                'codigo' => '1',
                'tutor' => '10',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => '2',
                'tutor' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::connection('sqlite')->table('horas')->insert([
            ['codigo' => 1, 'hora_ini' => '08:00', 'hora_fin' => '09:00', 'turno' => 'M'],
            ['codigo' => 2, 'hora_ini' => '09:00', 'hora_fin' => '10:00', 'turno' => 'M'],
            ['codigo' => 3, 'hora_ini' => '10:00', 'hora_fin' => '11:00', 'turno' => 'M'],
        ]);
    }

    public function test_exec_no_envia_missatges_si_no_hi_ha_grups_afectats(): void
    {
        Notification::fake();

        $elemento = new \stdClass();
        $elemento->desde = '2025-03-01';
        $elemento->hasta = '2025-03-01';
        $elemento->idProfesor = '3';
        $elemento->dia_completo = true;

        AdviseTeacher::exec($elemento);

        Notification::assertNothingSent();
    }

    public function test_exec_envia_missatges_si_hi_ha_professors_afectats(): void
    {
        Notification::fake();

        DB::connection('sqlite')->table('horarios')->insert([
            [
                'idProfesor' => '3',
                'idGrupo' => '1',
                'dia_semana' => 'S',
                'sesion_orden' => 1,
                'ocupacion' => null,
                'modulo' => 'M01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => '2',
                'idGrupo' => '1',
                'dia_semana' => 'S',
                'sesion_orden' => 1,
                'ocupacion' => null,
                'modulo' => 'M01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $elemento = (object) [
            'desde' => '2025-03-01',
            'hasta' => '2025-03-01',
            'idProfesor' => '3',
            'dia_completo' => true,
        ];

        AdviseTeacher::exec($elemento);

        $receptor = Profesor::find('2');
        Notification::assertSentTo($receptor, mensajePanel::class);
    }

    public function test_gruposAfectados_retornara_una_colleccio(): void
    {
        $elemento = (object) [
            'desde' => '2025-03-01',
            'hasta' => '2025-03-01',
            'idProfesor' => '3',
            'idGrupo' => '1',
            'dia_completo' => true,
        ];

        DB::connection('sqlite')->table('horarios')->insert([
            [
                'idProfesor' => '3',
                'idGrupo' => '1',
                'dia_semana' => 'S',
                'sesion_orden' => 1,
                'ocupacion' => null,
                'modulo' => 'M01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $resultat = AdviseTeacher::gruposAfectados($elemento, '3');
        $this->assertInstanceOf(Collection::class, $resultat);
    }

    public function test_sendEmailTutor_enviar_email_si_hi_ha_tutor(): void
    {
        Queue::fake();

        $elemento = (object) [
            'desde' => '2025-03-01',
            'hasta' => '2025-03-01',
            'idProfesor' => '3',
            'idGrupo' => '1',
            'dia_completo' => true,
        ];

        DB::connection('sqlite')->table('horarios')->insert([
            [
                'idProfesor' => '3',
                'idGrupo' => '1',
                'dia_semana' => 'S',
                'sesion_orden' => 1,
                'ocupacion' => null,
                'modulo' => 'M01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        AdviseTeacher::sendEmailTutor($elemento);

        Queue::assertPushed(SendEmail::class);
    }

    public function test_exec_converteix_emisor_objecte_a_string(): void
    {
        Notification::fake();

        DB::connection('sqlite')->table('horarios')->insert([
            [
                'idProfesor' => '3',
                'idGrupo' => '1',
                'dia_semana' => 'S',
                'sesion_orden' => 1,
                'ocupacion' => null,
                'modulo' => 'M01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => '2',
                'idGrupo' => '1',
                'dia_semana' => 'S',
                'sesion_orden' => 1,
                'ocupacion' => null,
                'modulo' => 'M01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $elemento = (object) [
            'desde' => '2025-03-01',
            'hasta' => '2025-03-01',
            'idProfesor' => '3',
            'dia_completo' => true,
        ];
        $emisor = (object) ['shortName' => 'Profe Prova'];

        AdviseTeacher::exec($elemento, null, null, $emisor);

        $receptor = Profesor::find('2');
        Notification::assertSentTo(
            $receptor,
            mensajePanel::class,
            function ($notification, $channels, $notifiable) {
                $data = $notification->toArray($notifiable);
                return ($data['emissor'] ?? null) === 'Profe Prova';
            }
        );
    }

    public function test_sendEmailTutor_usa_email_tutor_si_no_hi_ha_sustitucio(): void
    {
        Queue::fake();

        $elemento = (object) [
            'desde' => '2025-03-01',
            'hasta' => '2025-03-01',
            'idProfesor' => '3',
            'dia_completo' => true,
        ];

        DB::connection('sqlite')->table('horarios')->insert([
            [
                'idProfesor' => '3',
                'idGrupo' => '1',
                'dia_semana' => 'S',
                'sesion_orden' => 1,
                'ocupacion' => null,
                'modulo' => 'M01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        AdviseTeacher::sendEmailTutor($elemento);

        Queue::assertPushed(SendEmail::class, function ($job) {
            $correo = new \ReflectionProperty($job, 'correo');
            $correo->setAccessible(true);
            return $correo->getValue($job) === 'tutor@example.com';
        });
    }

    public function test_horariAltreGrup_filtra_grups_del_element(): void
    {
        $elemento = (object) [
            'desde' => '2025-03-01',
            'hasta' => '2025-03-02',
            'idProfesor' => '3',
            'grupos' => [
                (object) ['codigo' => 1],
            ],
            'dia_completo' => true,
        ];

        DB::connection('sqlite')->table('horarios')->insert([
            [
                'idProfesor' => '3',
                'idGrupo' => '1',
                'dia_semana' => 'S',
                'sesion_orden' => 1,
                'ocupacion' => null,
                'modulo' => 'M01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => '3',
                'idGrupo' => '2',
                'dia_semana' => 'S',
                'sesion_orden' => 2,
                'ocupacion' => null,
                'modulo' => 'M01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $resultat = AdviseTeacher::horariAltreGrup($elemento, '3');

        $this->assertSame(['2'], $resultat->pluck('idGrupo')->all());
    }
}
