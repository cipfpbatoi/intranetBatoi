<?php

namespace Tests\Unit\Entities;

use Intranet\Entities\Task;
use Tests\TestCase;

class TaskTest extends TestCase
{
    public function test_vencimiento_null_safe_i_link_accessor(): void
    {
        $task = new Task([
            'vencimiento' => null,
            'fichero' => null,
            'enlace' => 'https://example.test/resource',
        ]);

        $this->assertSame('', $task->vencimiento);
        $this->assertSame('https://example.test/resource', $task->link);
    }

    public function test_image_accessor_usa_data_real_i_informativa(): void
    {
        $past = new Task([
            'vencimiento' => date('Y-m-d', strtotime('-1 day')),
            'informativa' => 0,
        ]);
        $this->assertSame('warning.png', $past->image);

        $futureInfo = new Task([
            'vencimiento' => date('Y-m-d', strtotime('+1 day')),
            'informativa' => 1,
        ]);
        $this->assertSame('informacion.jpeg', $futureInfo->image);

        $futureTask = new Task([
            'vencimiento' => date('Y-m-d', strtotime('+1 day')),
            'informativa' => 0,
        ]);
        $this->assertSame('task.png', $futureTask->image);
    }

    public function test_my_details_sense_auth_retorn_null(): void
    {
        $task = new Task();

        $this->assertNull($task->myDetails);
    }
}

