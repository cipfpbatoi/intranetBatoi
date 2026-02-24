<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Activity;
use Tests\TestCase;

class ActivityScopeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('activities');
        $schema->dropIfExists('fcts');

        $schema->create('activities', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('action')->nullable();
            $table->string('model_class')->nullable();
            $table->unsignedInteger('model_id')->nullable();
            $table->string('comentari')->nullable();
            $table->string('document')->nullable();
            $table->string('author_id')->nullable();
            $table->timestamps();
        });

        $schema->create('fcts', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idColaboracion')->nullable();
            $table->timestamps();
        });
    }

    public function test_scope_relation_id_inclou_model_i_colaboracio_relacionada(): void
    {
        DB::table('fcts')->insert([
            'id' => 10,
            'idColaboracion' => 200,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('activities')->insert([
            [
                'action' => 'email',
                'model_class' => 'Intranet\\Entities\\Fct',
                'model_id' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'action' => 'phone',
                'model_class' => 'Intranet\\Entities\\Colaboracion',
                'model_id' => 200,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'action' => 'book',
                'model_class' => 'Intranet\\Entities\\Colaboracion',
                'model_id' => 999,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $ids = Activity::query()->relationId(10)->pluck('model_id')->sort()->values()->all();

        $this->assertSame([10, 200], $ids);
    }

    public function test_scope_relation_id_quan_no_hi_ha_colaboracio_només_torna_id_directe(): void
    {
        DB::table('activities')->insert([
            [
                'action' => 'email',
                'model_class' => 'Intranet\\Entities\\Fct',
                'model_id' => 77,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'action' => 'email',
                'model_class' => 'Intranet\\Entities\\Fct',
                'model_id' => 88,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $ids = Activity::query()->relationId(77)->pluck('model_id')->all();

        $this->assertSame([77], $ids);
    }
}

