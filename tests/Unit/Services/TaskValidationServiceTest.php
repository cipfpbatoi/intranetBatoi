<?php

namespace Tests\Unit\Services;

use Intranet\Services\School\TaskValidationService;
use Tests\TestCase;

class TaskValidationServiceTest extends TestCase
{
    public function test_resolve_retorn_zero_quan_action_es_nul_o_desconeguda(): void
    {
        $service = new TaskValidationService();

        $this->assertSame(0, $service->resolve(null, 'P100'));
        $this->assertSame(0, $service->resolve('ActionInexistent', 'P100'));
        $this->assertSame(0, $service->resolve('ActionInexistent', null));
    }
}

