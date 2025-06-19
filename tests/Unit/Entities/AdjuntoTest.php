<?php

namespace Tests\Unit\Entities;

use Tests\TestCase;
use Intranet\Entities\Adjunto;
use Mockery;


class AdjuntoTest extends TestCase
{
    public function testGetPathAttribute()
    {
        $adjunto = Mockery::mock(Adjunto::class)->makePartial();
        $adjunto->route = 'test_folder';
        $adjunto->title = 'document';
        $adjunto->extension = 'pdf';

        $expectedPath = storage_path() . Adjunto::CARPETA . 'test_folder/document.pdf';
        $this->assertEquals($expectedPath, $adjunto->path);
    }

    public function testGetFileAttribute()
    {
        $adjunto = Mockery::mock(Adjunto::class)->makePartial();
        $adjunto->route = 'test_folder';
        $adjunto->title = 'document';
        $adjunto->extension = 'pdf';

        $this->assertEquals('test_folder/document.pdf', $adjunto->file);
    }

    public function testGetModeloAttribute()
    {
        $adjunto = Mockery::mock(Adjunto::class)->makePartial();
        $adjunto->shouldReceive('getPathAttribute')->andReturn('model1/12345/file');

        $this->assertEquals('model1', $adjunto->modelo);
    }

    public function testGetModeloIdAttribute()
    {
        $adjunto = Mockery::mock(Adjunto::class)->makePartial();
        $adjunto->shouldReceive('getPathAttribute')->andReturn('model1/12345/file');

        $this->assertEquals('12345', $adjunto->modelo_id);
    }


    public function testGetDirectoryAttribute()
    {
        $adjunto = Mockery::mock(Adjunto::class)->makePartial();
        $adjunto->route = 'test_folder';

        $expectedDir = storage_path() . Adjunto::CARPETA . 'test_folder';
        $this->assertEquals($expectedDir, $adjunto->directory);
    }
}