<?php

namespace Tests\Unit\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Activity;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    public function test_record_crea_instancia_sense_usuari_autenticat(): void
    {
        $result = Activity::record('create', null, 'Comentari de prova');

        $this->assertInstanceOf(Activity::class, $result);
        $this->assertSame('create', $result->action);
        $this->assertSame('Comentari de prova', $result->comentari);
        $this->assertNull($result->model_class);
        $this->assertNull($result->model_id);
        $this->assertFalse($result->exists);
    }

    public function test_record_emplena_model_i_mostra_alerta_quan_hi_ha_model(): void
    {
        session()->forget('app_alerts');

        $model = new ActivityRecordDummyModel();
        $model->id = 99;

        $result = Activity::record('update', $model, 'Canvi');

        $this->assertInstanceOf(Activity::class, $result);
        $this->assertSame(ActivityRecordDummyModel::class, $result->model_class);
        $this->assertSame(99, $result->model_id);
        $this->assertSame('Canvi', $result->comentari);
        $this->assertTrue(
            collect(session('app_alerts', []))->contains(
                static fn (array $alert): bool => ($alert['type'] ?? null) === 'success'
            )
        );
    }
}

class ActivityRecordDummyModel extends Model
{
    protected $table = 'dummy_activity_records';
}
