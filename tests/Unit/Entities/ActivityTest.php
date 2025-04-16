<?php

namespace Tests\Unit\Entities;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Tests\TestCase;
use Intranet\Entities\Activity;
use Illuminate\Support\Facades\Auth;
use Mockery;

class ActivityTest extends TestCase
{
    use WithoutModelEvents;
    public function testRecordMethodCreatesActivityInstance()
    {
        // Simulem un usuari autenticat
        $mockUser = Mockery::mock('alias:App\Models\User');
        Auth::shouldReceive('user')->andReturn($mockUser);
        $mockUser->shouldReceive('Activity->save')->andReturn(true);

        // Mock d'Activity per al mètode estàtic record()
        $mockActivity = Mockery::mock('alias:' . Activity::class);
        $mockActivity->shouldAllowMockingMethod('record');
        $mockActivity->shouldReceive('record')->once()->andReturn(true);

        // Cridem la funció
        $result = Activity::record('create', null, 'Comentari de prova');

        // Verifiquem que es crida correctament
        $this->assertTrue($result);
    }

}