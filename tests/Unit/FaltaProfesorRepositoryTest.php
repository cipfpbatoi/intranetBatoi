<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Domain\FaltaProfesor\FaltaProfesorRepositoryInterface;
use Intranet\Infrastructure\Persistence\Eloquent\FaltaProfesor\EloquentFaltaProfesorRepository;
use Tests\TestCase;

class FaltaProfesorRepositoryTest extends TestCase
{
    use WithoutModelEvents;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->createSchema();
        $this->seedData();
    }

    public function test_binding_resol_repositori_eloquent(): void
    {
        $repo = $this->app->make(FaltaProfesorRepositoryInterface::class);
        $this->assertInstanceOf(EloquentFaltaProfesorRepository::class, $repo);
    }

    public function test_last_has_and_range_queries(): void
    {
        $repo = $this->app->make(FaltaProfesorRepositoryInterface::class);

        $this->assertTrue($repo->hasFichadoOnDay(date('Y-m-d'), 'P001'));
        $this->assertFalse($repo->hasFichadoOnDay('2030-01-01', 'P001'));

        $last = $repo->lastTodayByProfesor('P001');
        $this->assertNotNull($last);
        $this->assertSame('12:30:00', (string) $last->entrada);

        $range = $repo->rangeByProfesor('P001', date('Y-m-d'), date('Y-m-d'));
        $this->assertCount(2, $range);
    }

    private function createSchema(): void
    {
        Schema::connection('sqlite')->create('faltas_profesores', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor', 10);
            $table->date('dia');
            $table->time('entrada');
            $table->time('salida')->nullable();
            $table->timestamps();
        });
    }

    private function seedData(): void
    {
        DB::table('faltas_profesores')->insert([
            [
                'idProfesor' => 'P001',
                'dia' => date('Y-m-d'),
                'entrada' => '08:00:00',
                'salida' => '10:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => 'P001',
                'dia' => date('Y-m-d'),
                'entrada' => '12:30:00',
                'salida' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

