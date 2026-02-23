<?php

namespace Tests\Unit\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intranet\Entities\Task;
use Intranet\Services\School\TaskFileService;
use Tests\TestCase;

class TaskFileServiceTest extends TestCase
{
    public function test_store_guardar_fitxer_en_public_eventos(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $task = new Task();
        $service = new TaskFileService();

        $path = $service->store($file, $task);

        $this->assertNotNull($path);
        $this->assertStringStartsWith('Eventos/', $path);
        $this->assertStringEndsWith('.pdf', $path);
        Storage::disk('public')->assertExists($path);
    }
}

